<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventPaperController extends Controller
{
    public function index(Event $event)
    {
        $event->load(['questions.subject']);
        $subjects = Subject::with('topics')->get();
        return view('admin.events.paper', compact('event', 'subjects'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);

        $currentCount = $event->questions()->count();
        $newIds = $request->question_ids;

        $attachData = [];
        foreach ($newIds as $index => $qid) {
            // Prevent duplicates
            if (!$event->questions()->where('question_id', $qid)->exists()) {
                $attachData[$qid] = ['order' => $currentCount + $index + 1];
            }
        }

        if (!empty($attachData)) {
            $event->questions()->attach($attachData);
            return redirect()->back()->with('success', 'Questions added successfully.');
        }

        return redirect()->back()->with('info', 'Selected questions are already in the paper.');
    }

    // Auto-generate questions based on total marks
    public function autoGenerate(Event $event)
    {
        // 1. Clear existing questions
        $event->questions()->detach();

        // 2. Determine how many questions needed
        // Assuming 1 mark per question for simplicity, or we can just limit by 100 if marks not 1-to-1
        // Ideally we should use $event->total_marks / marks_per_question.
        // For now, let's assume 1 question = 1 mark.
        $limit = $event->total_marks;

        $questions = Question::where('status', 'APPROVED')
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->back()->with('error', 'No questions available to generate paper.');
        }

        // 3. Attach
        $attachData = [];
        foreach ($questions as $index => $q) {
            $attachData[$q->id] = ['order' => $index + 1];
        }

        $event->questions()->attach($attachData);

        return redirect()->back()->with('success', 'Questions auto-generated successfully.');
    }

    // Reorder questions via Ajax
    public function reorder(Request $request, Event $event)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:questions,id'
        ]);

        $order = $request->order;

        foreach ($order as $index => $questionId) {
            $event->questions()->updateExistingPivot($questionId, ['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Event $event, Question $question)
    {
        $event->questions()->detach($question->id);
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

        // Exclude questions already in the event
        if ($request->event_id) {
            $existingIds = DB::table('event_questions')->where('event_id', $request->event_id)->pluck('question_id');
            $query->whereNotIn('id', $existingIds);
        }

        $questions = $query->with(['subject', 'topic'])->limit(50)->get();

        return response()->json($questions);
    }
}
