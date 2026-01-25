<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Services\ExamGeneratorService;
use App\Services\EvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    protected $examGenerator;
    protected $evaluator;

    public function __construct(ExamGeneratorService $examGenerator, EvaluationService $evaluator)
    {
        $this->examGenerator = $examGenerator;
        $this->evaluator = $evaluator;
    }

    public function index()
    {
        $userId = Auth::id();
        $exams = Exam::where('status', 'PUBLISHED')
            ->where(function ($q) use ($userId) {
                $q->where('access_mode', 'PUBLIC')
                    ->orWhereHas('assignments', function ($subQ) use ($userId) {
                        $subQ->where('user_id', $userId);
                    });
            })
            ->with([
                'attempts' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->orderBy('start_time', 'asc')
            ->get();

        return view('student.exams.index', compact('exams'));
    }

    public function start(Exam $exam)
    {
        // Check availability
        $now = now();
        if ($now->lt($exam->start_time) || $now->gt($exam->end_time)) {
            return redirect()->back()->with('error', 'Exam is not currently active.');
        }

        $user = Auth::user();
        if (!$user) {
            // For dev/demo if auth not fully set up or to prevent crash
            return redirect()->route('login')->with('error', 'Please login to start exam.');
        }

        // Generate or retrieve attempt
        $attempt = $this->examGenerator->generateExamPaper($exam, $user);

        if ($attempt->status === 'COMPLETED') {
            return redirect()->route('student.exams.result', $attempt->id);
        }

        return redirect()->route('student.exams.live', $attempt->id);
    }

    public function live(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status === 'COMPLETED') {
            return redirect()->route('student.exams.result', $attempt->id);
        }

        // Calculate remaining time
        $exam = $attempt->exam;
        // Total duration in seconds
        $durationSeconds = $exam->duration_minutes * 60;

        // Time elapsed since start
        $elapsed = now()->diffInSeconds($attempt->start_time); // abs value
        $remainingSeconds = max(0, $durationSeconds - $elapsed);

        // Also check against hard end time
        $timeUntilHardEnd = now()->diffInSeconds($exam->end_time, false);
        if ($timeUntilHardEnd < $remainingSeconds) {
            $remainingSeconds = max(0, $timeUntilHardEnd);
        }

        // Load questions with options (without is_correct flag ideally, but for now we trust blade not to show it)
        // Actually we should hide is_correct from JSON inspection if we want to be secure.
        $questions = $attempt->answers()->with([
            'question.options' => function ($q) {
                $q->select('id', 'question_id', 'option_text'); // Exclude is_correct
            }
        ])->get();

        return view('student.exams.live', compact('attempt', 'questions', 'remainingSeconds'));
    }

    public function submitAnswer(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status === 'COMPLETED') {
            return response()->json(['message' => 'Exam already submitted'], 400);
        }

        $questionId = $request->input('question_id');
        $selectedOptions = $request->input('selected_options'); // Array of IDs

        $answer = ExamAnswer::where('exam_attempt_id', $attempt->id)
            ->where('question_id', $questionId)
            ->first();

        if ($answer) {
            $answer->selected_options = $selectedOptions;
            $answer->save();
        }

        return response()->json(['success' => true]);
    }

    public function finish(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status !== 'COMPLETED') {
            $this->evaluator->evaluateAttempt($attempt);
        }

        return redirect()->route('student.exams.result', $attempt->id);
    }

    public function result(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        return view('student.exams.result', compact('attempt'));
    }
}
