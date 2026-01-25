<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::with(['subject', 'topic'])->latest()->paginate(10);
        return view('admin.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('status', '=', true)->get();
        // Topics will be loaded via JS or just load all for now if not too many
        $topics = Topic::all();
        return view('admin.questions.create', compact('subjects', 'topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:SINGLE,MULTIPLE',
            'difficulty' => 'required|in:EASY,MEDIUM,HARD',
            'negative_mark' => 'numeric|min:0',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option' => 'required_if:question_type,SINGLE',
            'correct_options' => 'required_if:question_type,MULTIPLE|array',
            'answer_explanation' => 'nullable|string' // Validation
        ]);

        DB::transaction(function () use ($validated, $request) {
            $question = Question::create([
                'subject_id' => $validated['subject_id'],
                'topic_id' => $validated['topic_id'],
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'difficulty' => $validated['difficulty'],
                'negative_mark' => $validated['negative_mark'],
                'status' => 'APPROVED',
                'answer_explanation' => $validated['answer_explanation'] ?? null
            ]);

            foreach ($validated['options'] as $key => $optionData) {
                $isCorrect = false;
                if ($validated['question_type'] === 'SINGLE') {
                    // In create view, radio value should be the index
                    if ($request->correct_option == $key) {
                        $isCorrect = true;
                    }
                } else {
                    // Checkbox values should be indices
                    if (in_array($key, $request->correct_options ?? [])) {
                        $isCorrect = true;
                    }
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'],
                    'is_correct' => $isCorrect
                ]);
            }
        });

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $subjects = Subject::where('status', '=', true)->get();
        $topics = Topic::where('subject_id', $question->subject_id)->get();
        return view('admin.questions.edit', compact('question', 'subjects', 'topics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        // Validation similar to store but handling existing options is tricky.
        // For simplicity, we can delete old options and recreate them, or update carefully.
        // Deleting and recreating is easier for this prototype.

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:SINGLE,MULTIPLE',
            'difficulty' => 'required|in:EASY,MEDIUM,HARD',
            'negative_mark' => 'numeric|min:0',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'answer_explanation' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $request, $question) {
            $question->update([
                'subject_id' => $validated['subject_id'],
                'topic_id' => $validated['topic_id'],
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'difficulty' => $validated['difficulty'],
                'negative_mark' => $validated['negative_mark'],
                'answer_explanation' => $validated['answer_explanation'] ?? null
            ]);

            // Remove old options
            $question->options()->delete();

            // Recreate options
            foreach ($validated['options'] as $key => $optionData) {
                $isCorrect = false;
                if ($validated['question_type'] === 'SINGLE') {
                    if ($request->correct_option == $key) {
                        $isCorrect = true;
                    }
                } else {
                    if (in_array($key, $request->correct_options ?? [])) {
                        $isCorrect = true;
                    }
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'],
                    'is_correct' => $isCorrect
                ]);
            }
        });

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
