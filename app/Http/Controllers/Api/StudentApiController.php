<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Event;
use App\Models\ExamAttempt;

class StudentApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Stats
        $availableExamsCount = Exam::where('status', 'PUBLISHED')->count();
        $upcomingEventsCount = Event::whereIn('status', ['UPCOMING', 'LIVE'])
            ->where('start_time', '>', now())
            ->count();

        // Recent Results
        $recentResults = ExamAttempt::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'wallet_balance' => $user->wallet->balance ?? 0,
                'available_exams' => $availableExamsCount,
                'upcoming_events' => $upcomingEventsCount,
            ],
            'recent_results' => $recentResults,
            'user' => $user
        ]);
    }

    public function exams(Request $request)
    {
        $exams = Exam::where('status', 'PUBLISHED')
            ->latest()
            ->get()
            ->map(function ($exam) use ($request) {
                $attempt = $exam->attempts()->where('user_id', $request->user()->id)->first();
                $exam->user_attempt = $attempt;
                return $exam;
            });

        return response()->json($exams);
    }

    public function events(Request $request)
    {
        $events = Event::whereIn('status', ['UPCOMING', 'LIVE'])
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get()
            ->map(function ($event) use ($request) {
                $participant = $request->user()->events()->where('event_id', $event->id)->first();
                $event->is_registered = $participant ? true : false;
                $event->participant_status = $participant ? $participant->pivot->status : null;
                $event->participant_id = $participant ? $participant->pivot->id : null;
                return $event;
            });

        return response()->json($events);
    }

    public function joinEvent(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        // 1. Check if already joined
        if ($user->events()->where('event_id', $event->id)->exists()) {
            return response()->json(['message' => 'You have already joined this event.'], 400);
        }

        // 2. Check Balance
        if ($user->wallet->balance < $event->registration_fee) {
            return response()->json(['message' => 'Insufficient balance. Please add funds directly to your wallet.'], 400);
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

            return response()->json(['message' => 'Successfully joined the event!', 'balance' => $user->wallet->refresh()->balance]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    public function wallet(Request $request)
    {
        return response()->json($request->user()->wallet);
    }

    public function enterEvent(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        // 1. Check Registration
        $participant = \App\Models\EventParticipant::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if (!$participant) {
            return response()->json(['message' => 'You must register for this event first.'], 403);
        }

        // 2. Check Time
        $now = now();
        if ($now->lt($event->start_time)) {
            return response()->json(['message' => 'Event has not started yet.'], 403);
        }
        if ($now->gt($event->end_time)) {
            return response()->json(['message' => 'Event has ended.'], 403);
        }

        if ($participant->status === 'COMPLETED') {
            return response()->json(['message' => 'You have already completed this event.'], 403);
        }

        // 3. Load Questions & State
        $remainingSeconds = $now->diffInSeconds($event->end_time, false);

        $questions = $event->questions;

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

        $existingAnswers = \App\Models\EventAnswer::where('event_participant_id', $participant->id)
            ->get()
            ->keyBy('question_id');

        $questionsWithState = $questions->map(function ($q) use ($existingAnswers) {
            $answer = $existingAnswers->get($q->id);
            $q->selected_options = $answer ? $answer->selected_options : [];
            // Hide is_correct/correct_options if they exist in serialization
            $q->makeHidden(['is_correct', 'correct_options', 'explanation']);
            return $q;
        });

        return response()->json([
            'event' => $event,
            'participant' => $participant,
            'questions' => $questionsWithState,
            'remaining_seconds' => max(0, $remainingSeconds)
        ]);
    }

    public function submitEventAnswer(Request $request, $participantId)
    {
        $participant = \App\Models\EventParticipant::findOrFail($participantId);
        if ($participant->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($participant->status === 'COMPLETED') {
            return response()->json(['message' => 'Event already submitted'], 400);
        }

        $questionId = $request->input('question_id');
        $selectedOptions = $request->input('selected_options'); // Array

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

    public function finishEvent(Request $request, $participantId)
    {
        $participant = \App\Models\EventParticipant::findOrFail($participantId);
        if ($participant->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $participant->status = 'COMPLETED';
        $participant->save();

        if (now()->lt($participant->event->end_time)) {
            return response()->json(['success' => true, 'message' => 'Event submitted successfully! Waiting for results.']);
        }

        return response()->json(['success' => true, 'message' => 'Event submitted successfully!']);
    }
}
