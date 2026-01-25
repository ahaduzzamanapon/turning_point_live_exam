<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamPaperController extends Controller
{
    public function index(Exam $exam)
    {
        $exam->load(['questions.subject']);
        $subjects = Subject::with('topics')->get();
        return view('admin.exams.paper', compact('exam', 'subjects'));
    }

    public function generate(Request $request, Exam $exam)
    {
        // 1. Clear existing questions
        $exam->questions()->detach();

        // 2. Fetch questions based on rules
        $rules = $exam->rules;
        $selectedQuestionIds = [];

        foreach ($rules as $rule) {
            if ($rule->easy > 0) {
                $ids = Question::where('subject_id', $rule->subject_id)
                    ->where('difficulty', 'EASY')
                    ->where('status', 'APPROVED')
                    ->inRandomOrder()->limit($rule->easy)->pluck('id')->toArray();
                $selectedQuestionIds = array_merge($selectedQuestionIds, $ids);
            }
            if ($rule->medium > 0) {
                $ids = Question::where('subject_id', $rule->subject_id)
                    ->where('difficulty', 'MEDIUM')
                    ->where('status', 'APPROVED')
                    ->inRandomOrder()->limit($rule->medium)->pluck('id')->toArray();
                $selectedQuestionIds = array_merge($selectedQuestionIds, $ids);
            }
            if ($rule->hard > 0) {
                $ids = Question::where('subject_id', $rule->subject_id)
                    ->where('difficulty', 'HARD')
                    ->where('status', 'APPROVED')
                    ->inRandomOrder()->limit($rule->hard)->pluck('id')->toArray();
                $selectedQuestionIds = array_merge($selectedQuestionIds, $ids);
            }
        }

        // 3. Attach new questions
        // Shuffle to randomize order initially
        shuffle($selectedQuestionIds);

        $attachData = [];
        foreach ($selectedQuestionIds as $index => $qid) {
            $attachData[$qid] = ['pivot_order' => $index + 1];
        }

        $exam->questions()->attach($attachData);

        return redirect()->back()->with('success', 'Question paper generated from rules successfully.');
    }

    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);

        $currentCount = $exam->questions()->count();
        $newIds = $request->question_ids;

        $attachData = [];
        foreach ($newIds as $index => $qid) {
            // Prevent duplicates
            if (!$exam->questions()->where('question_id', $qid)->exists()) {
                $attachData[$qid] = ['pivot_order' => $currentCount + $index + 1];
            }
        }

        if (!empty($attachData)) {
            $exam->questions()->attach($attachData);
            return redirect()->back()->with('success', 'Questions added successfully.');
        }

        return redirect()->back()->with('info', 'Selected questions are already in the paper.');
    }

    public function destroy(Exam $exam, Question $question)
    {
        $exam->questions()->detach($question->id);
        return redirect()->back()->with('success', 'Question removed from paper.');
    }

    // Ajax search for manual add
    public function search(Request $request)
    {
        $query = Question::query()->where('status', 'APPROVED');

        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->topic_id) {
            $query->where('topic_id', $request->topic_id);
        }
        if ($request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        // Exclude questions already in the exam if passed exam_id
        if ($request->exam_id) {
            $existingIds = DB::table('exam_questions')->where('exam_id', $request->exam_id)->pluck('question_id');
            $query->whereNotIn('id', $existingIds);
        }

        $questions = $query->with(['subject', 'topic'])->limit(50)->get();

        return response()->json($questions);
    }
}
