<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'UPCOMING')
            ->orderBy('start_time', 'asc')
            ->paginate(9);

        return view('student.events.index', compact('events'));
    }

    public function join(Event $event)
    {
        $user = auth()->user();

        // 1. Check if already joined
        if ($user->events()->where('event_id', $event->id)->exists()) {
            return back()->with('error', 'You have already joined this event.');
        }

        // 2. Check Balance
        if ($user->wallet->balance < $event->registration_fee) {
            return back()->with('error', 'Insufficient balance. Please add funds directly to your wallet.');
        }

        try {
            \DB::transaction(function () use ($user, $event) {
                // Deduct Fee
                $user->wallet->withdraw($event->registration_fee, "Event Registration: " . $event->title, $event->id);

                // Add Participant
                \App\Models\EventParticipant::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'payment_status' => 'PAID',
                    'status' => 'REGISTERED'
                ]);
            });

            return back()->with('success', 'Successfully joined the event!');
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
    public function enter(Event $event)
    {
        $user = auth()->user();

        // 1. Check Registration
        $participant = $user->events()->where('event_id', $event->id)->first();
        if (!$participant) {
            return back()->with('error', 'You must register for this event first.');
        }

        // 2. Check Time
        $now = now();
        if ($now->lt($event->start_time)) {
            return back()->with('error', 'Event has not started yet.');
        }
        if ($now->gt($event->end_time)) {
            return back()->with('error', 'Event has ended.');
        }

        // 3. Get Pivot/Participant Record
        // We used belongsToMany to get the user's event, so $participant is the User model with pivot.
        // But we need the EventParticipant model id for answers.
        // Let's fetch the actual model or use the pivot accessor if set up.
        // Simpler to query the model directly for ID.
        $participantRecord = \App\Models\EventParticipant::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        return redirect()->route('student.events.live', $participantRecord->id);
    }

    public function live($participantId)
    {
        $participant = \App\Models\EventParticipant::with(['event', 'user'])->findOrFail($participantId);

        if ($participant->user_id !== auth()->id()) {
            abort(403);
        }

        $event = $participant->event;
        $now = now();

        if ($now->gt($event->end_time)) {
            return redirect()->route('student.events.index')->with('error', 'Event has ended.');
        }

        // Calculate remaining time
        $remainingSeconds = $now->diffInSeconds($event->end_time, false);
        if ($remainingSeconds <= 0) {
            return redirect()->route('student.events.index')->with('error', 'Event has ended.');
        }

        // Load Questions (Assuming Event has questions relation)
        // If Logic is random questions on entry? User didn't specify. 
        // Assuming static set of questions for the event for now.
        // We need to fetch questions associated with the event.
        // And we need to load 'saved answers' if any.

        // Load Questions
        $questions = $event->questions; // Direct relation

        // Self-healing: If no questions, assign them now (for old events or failures)
        if ($questions->isEmpty()) {
            $questionsCount = $event->total_marks > 0 ? $event->total_marks : 10;
            $newQuestions = \App\Models\Question::where('status', 'APPROVED')
                ->inRandomOrder()
                ->limit($questionsCount)
                ->get();

            if ($newQuestions->isNotEmpty()) {
                $pivotData = [];
                foreach ($newQuestions as $index => $q) {
                    $pivotData[$q->id] = ['order' => $index + 1];
                }
                $event->questions()->attach($pivotData);
                $event->refresh();
                $questions = $event->questions;
            }
        }

        // Fetch User's answers for this participant record
        $existingAnswers = \App\Models\EventAnswer::where('event_participant_id', $participant->id)
            ->get()
            ->keyBy('question_id');

        // Transform for View (similar to Exam structure)
        $questionsWithState = $questions->map(function ($q) use ($existingAnswers) {
            $answer = $existingAnswers->get($q->id);
            // Attach selected options to the question object temporarily for view
            $q->selected_options = $answer ? $answer->selected_options : [];
            return $q;
        });

        return view('student.events.live', compact('event', 'participant', 'questionsWithState', 'remainingSeconds'));
    }

    public function submitAnswer(Request $request, $participantId)
    {
        $participant = \App\Models\EventParticipant::findOrFail($participantId);
        if ($participant->user_id !== auth()->id())
            abort(403);

        $questionId = $request->input('question_id');
        $selectedOptions = $request->input('selected_options'); // Array

        // Update or Create Answer
        \App\Models\EventAnswer::updateOrCreate(
            [
                'event_participant_id' => $participant->id,
                'question_id' => $questionId
            ],
            [
                'selected_options' => $selectedOptions
            ]
        );

        return response()->json(['success' => true]);
    }

    public function finish(Request $request, $participantId)
    {
        $participant = \App\Models\EventParticipant::with('event')->findOrFail($participantId);
        if ($participant->user_id !== auth()->id())
            abort(403);

        if ($participant->status !== 'COMPLETED') {
            // Calculate Score
            $totalScore = 0;
            $answers = \App\Models\EventAnswer::where('event_participant_id', $participant->id)->get();
            $questions = \App\Models\Question::whereIn('id', $answers->pluck('question_id'))->with('options')->get()->keyBy('id');

            foreach ($answers as $answer) {
                $question = $questions->get($answer->question_id);
                if (!$question)
                    continue;

                $isCorrect = false;
                $marks = 0;

                // Simple Single/Multiple Choice Logic
                // Assuming 1 mark per question for now or use $question->marks if available
                $qMarks = 1; // Default or fetch from question/event settings

                if ($question->question_type === 'SINGLE') {
                    $correctOption = $question->options->where('is_correct', true)->first();
                    if ($correctOption && isset($answer->selected_options[0]) && $answer->selected_options[0] == $correctOption->id) {
                        $isCorrect = true;
                        $marks = $qMarks;
                    }
                } elseif ($question->question_type === 'MULTIPLE') {
                    // All correct options must be selected and NO incorrect options
                    $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
                    $selectedIds = $answer->selected_options ?? [];

                    if (count($correctOptionIds) === count($selectedIds) && empty(array_diff($correctOptionIds, $selectedIds))) {
                        $isCorrect = true;
                        $marks = $qMarks;
                    }
                }

                $answer->is_correct = $isCorrect;
                $answer->marks_obtained = $marks;
                $answer->save();

                $totalScore += $marks;
            }

            $participant->status = 'COMPLETED';
            $participant->score = $totalScore;
            $participant->save();
        }

        // Check if event has ended
        if (now()->lt($participant->event->end_time)) {
            return redirect()->route('student.events.index')->with('success', 'Event submitted successfully! Waiting for results.');
        }

        return redirect()->route('student.events.result', $participant->id)->with('success', 'Event submitted successfully!');
    }

    public function result($participantId)
    {
        $participant = \App\Models\EventParticipant::with(['event', 'user'])->findOrFail($participantId);

        if ($participant->user_id !== auth()->id()) {
            abort(403);
        }

        // Load Answers with Questions and Options
        // We need to pass data similar to exam result view
        $answers = \App\Models\EventAnswer::where('event_participant_id', $participant->id)
            ->with(['question.options'])
            ->get();

        // Manually attach selected options to question object for view compatibility if needed, 
        // OR just pass $answers and iterate in view.
        // The Exam view uses $attempt->answers. Here we have $participant and separate $answers collection.
        // Let's pass 'participant' and 'answers'.

        return view('student.events.result', compact('participant', 'answers'));
    }
}
