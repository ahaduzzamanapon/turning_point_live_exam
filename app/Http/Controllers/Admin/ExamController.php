<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamRule;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exam::latest('created_at')->paginate(10);
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('status', '=', true)->get();
        return view('admin.exams.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'negative_marking' => 'boolean',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:DRAFT,PUBLISHED',
            'rules' => 'required|array|min:1',
            'rules.*.subject_id' => 'required|exists:subjects,id',
            'rules.*.easy' => 'required|integer|min:0',
            'rules.*.medium' => 'required|integer|min:0',
            'rules.*.hard' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $exam = Exam::create([
                'title' => $validated['title'],
                'duration_minutes' => $validated['duration_minutes'],
                'total_marks' => $validated['total_marks'],
                'negative_marking' => $request->has('negative_marking'),
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => $validated['status'],
            ]);

            foreach ($validated['rules'] as $ruleData) {
                // Calculate total questions for this rule
                $totalQuestions = $ruleData['easy'] + $ruleData['medium'] + $ruleData['hard'];

                if ($totalQuestions > 0) {
                    ExamRule::create([
                        'exam_id' => $exam->id,
                        'subject_id' => $ruleData['subject_id'],
                        'easy' => $ruleData['easy'],
                        'medium' => $ruleData['medium'],
                        'hard' => $ruleData['hard'],
                        'total_questions' => $totalQuestions,
                    ]);
                }
            }
        });

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $subjects = Subject::where('status', '=', true)->get();
        return view('admin.exams.edit', compact('exam', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'negative_marking' => 'boolean',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:DRAFT,PUBLISHED', // Removed COMPLETED from manual update for now
            'rules' => 'nullable|array', // Rules optional on update if we don't want to force reset, but better to force.
            'rules.*.subject_id' => 'required|exists:subjects,id',
            'rules.*.easy' => 'required|integer|min:0',
            'rules.*.medium' => 'required|integer|min:0',
            'rules.*.hard' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, $exam) {
            $exam->update([
                'title' => $validated['title'],
                'duration_minutes' => $validated['duration_minutes'],
                'total_marks' => $validated['total_marks'],
                'negative_marking' => $request->has('negative_marking'),
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => $validated['status'],
            ]);

            if (isset($validated['rules'])) {
                // Remove old rules
                $exam->rules()->delete();

                foreach ($validated['rules'] as $ruleData) {
                    $totalQuestions = $ruleData['easy'] + $ruleData['medium'] + $ruleData['hard'];
                    if ($totalQuestions > 0) {
                        ExamRule::create([
                            'exam_id' => $exam->id,
                            'subject_id' => $ruleData['subject_id'],
                            'easy' => $ruleData['easy'],
                            'medium' => $ruleData['medium'],
                            'hard' => $ruleData['hard'],
                            'total_questions' => $totalQuestions,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    public function assign(Exam $exam)
    {
        $students = \App\Models\User::all(); // Fetch all students
        $assignedUserIds = $exam->assignments->pluck('id')->toArray();
        return view('admin.exams.assign', compact('exam', 'students', 'assignedUserIds'));
    }

    public function storeAssignment(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'access_mode' => 'required|in:PUBLIC,PRIVATE',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id'
        ]);

        DB::transaction(function () use ($validated, $exam) {
            $exam->update(['access_mode' => $validated['access_mode']]);

            if ($validated['access_mode'] === 'PRIVATE') {
                $exam->assignments()->sync($validated['user_ids'] ?? []);
            } else {
                // If public, clear assignments? Or keep them but ignore?
                // Better to clear or keep. Let's keep for history but typically Public means all.
                // For logic simplicity, we can clear to avoid confusion.
                $exam->assignments()->detach();
            }
        });

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam assignment updated successfully.');
    }
}
