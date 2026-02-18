<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Event;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\Question;

class StudentApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $availableExamsCount = Exam::where('status', 'PUBLISHED')->count();
        $upcomingEventsCount = Event::whereIn('status', ['UPCOMING', 'LIVE'])->count();

        $recentResults = ExamAttempt::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->with('exam:id,title,total_marks')
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
                $attempt = $exam->attempts()->where('user_id', $request->user()->id)->latest()->first();
                $exam->user_attempt = $attempt;
                $exam->questions_count = $exam->questions()->count();
                return $exam;
            });

        return response()->json($exams);
    }

    // ========================
    // EVENTS
    // ========================

    public function events(Request $request)
    {
        // Show ALL events (upcoming, live, completed) so user can see results
        $events = Event::whereIn('status', ['UPCOMING', 'LIVE', 'COMPLETED'])
            ->orderByRaw("CASE status WHEN 'LIVE' THEN 1 WHEN 'UPCOMING' THEN 2 WHEN 'COMPLETED' THEN 3 ELSE 4 END")
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(function ($event) use ($request) {
                $participant = $request->user()->events()->where('event_id', $event->id)->first();
                $event->is_registered = $participant ? true : false;
                $event->participant_status = $participant ? $participant->pivot->status : null;
                $event->participant_id = $participant ? $participant->pivot->id : null;
                $event->my_rank = $participant ? $participant->pivot->rank : null;
                $event->my_score = $participant ? $participant->pivot->score : null;
                $event->my_prize = $participant ? $participant->pivot->prize_won : null;
                $event->total_participants = $event->participants()->count();
                return $event;
            });

        return response()->json($events);
    }

    public function joinEvent(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        if ($user->events()->where('event_id', $event->id)->exists()) {
            return response()->json(['message' => 'You have already joined this event.'], 400);
        }

        if ($user->wallet->balance < $event->registration_fee) {
            return response()->json(['message' => 'Insufficient balance. Please add funds to your wallet.'], 400);
        }

        try {
            \DB::transaction(function () use ($user, $event) {
                $user->wallet->withdraw($event->registration_fee, "Event Registration: " . $event->title, $event->id);

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

    public function enterEvent(Request $request, $id)
    {
        $user = $request->user();
        $event = Event::findOrFail($id);

        $participant = \App\Models\EventParticipant::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if (!$participant) {
            return response()->json(['message' => 'You must register for this event first.'], 403);
        }

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

        $remainingSeconds = $now->diffInSeconds($event->end_time, false);

        $questions = $event->questions;

        // Self-healing: If no questions, assign them now
        if ($questions->isEmpty()) {
            $questionsCount = $event->total_marks > 0 ? $event->total_marks : 10;
            $newQuestions = Question::where('status', 'APPROVED')
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
        $selectedOptions = $request->input('selected_options');

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

    // ========================
    // WALLET
    // ========================

    public function wallet(Request $request)
    {
        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json([
                'balance' => 0,
                'transactions' => [],
            ]);
        }

        $transactions = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'balance' => $wallet->balance ?? 0,
            'transactions' => $transactions ?? [],
        ]);
    }

    // ========================
    // EXAMS
    // ========================

    public function startExam(Request $request, $id)
    {
        $user = $request->user();
        $exam = Exam::findOrFail($id);

        // Check if already has an active attempt
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['IN_PROGRESS'])
            ->first();

        if ($existingAttempt) {
            // Resume existing attempt
            $remainingSeconds = now()->diffInSeconds(
                $existingAttempt->start_time->addMinutes($exam->duration_minutes),
                false
            );

            if ($remainingSeconds <= 0) {
                $existingAttempt->status = 'COMPLETED';
                $existingAttempt->end_time = now();
                $existingAttempt->save();
                return response()->json(['message' => 'Exam time has expired.'], 403);
            }

            $questions = $exam->questions()->with('options')->get();
            $existingAnswers = ExamAnswer::where('exam_attempt_id', $existingAttempt->id)
                ->get()
                ->keyBy('question_id');

            $questionsWithState = $questions->map(function ($q) use ($existingAnswers) {
                $answer = $existingAnswers->get($q->id);
                $q->selected_options = $answer ? $answer->selected_options : [];
                $q->makeHidden(['correct_options', 'explanation']);
                return $q;
            });

            return response()->json([
                'attempt' => $existingAttempt,
                'exam' => $exam,
                'questions' => $questionsWithState,
                'remaining_seconds' => max(0, $remainingSeconds),
            ]);
        }

        // Check if already completed
        $completedAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->first();

        if ($completedAttempt) {
            return response()->json(['message' => 'You have already completed this exam.'], 403);
        }

        // Create new attempt
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'start_time' => now(),
            'status' => 'IN_PROGRESS',
        ]);

        $questions = $exam->questions()->with('options')->get();
        $questionsWithState = $questions->map(function ($q) {
            $q->selected_options = [];
            $q->makeHidden(['correct_options', 'explanation']);
            return $q;
        });

        return response()->json([
            'attempt' => $attempt,
            'exam' => $exam,
            'questions' => $questionsWithState,
            'remaining_seconds' => $exam->duration_minutes * 60,
        ]);
    }

    public function submitExamAnswer(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);
        if ($attempt->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($attempt->status === 'COMPLETED') {
            return response()->json(['message' => 'Exam already submitted'], 400);
        }

        $questionId = $request->input('question_id');
        $selectedOptions = $request->input('selected_options');

        ExamAnswer::updateOrCreate(
            [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $questionId
            ],
            [
                'selected_options' => $selectedOptions
            ]
        );

        return response()->json(['success' => true]);
    }

    public function finishExam(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);
        if ($attempt->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($attempt->status === 'COMPLETED') {
            return response()->json(['message' => 'Exam already submitted'], 400);
        }

        // Calculate score
        $exam = $attempt->exam;
        $answers = ExamAnswer::where('exam_attempt_id', $attempt->id)->get();
        $questions = $exam->questions()->get()->keyBy('id');

        $score = 0;
        $correct = 0;
        $wrong = 0;

        foreach ($answers as $answer) {
            $question = $questions->get($answer->question_id);
            if (!$question)
                continue;

            $correctOptions = $question->correct_options ?? [];
            $selectedOptions = $answer->selected_options ?? [];

            // Sort for comparison
            sort($correctOptions);
            sort($selectedOptions);

            if ($correctOptions == $selectedOptions) {
                $score += 1; // 1 mark per question
                $correct++;
            } else {
                $wrong++;
                if ($exam->negative_marking) {
                    $score -= 0.25;
                }
            }
        }

        $attempt->score = max(0, $score);
        $attempt->status = 'COMPLETED';
        $attempt->end_time = now();
        $attempt->save();

        return response()->json([
            'success' => true,
            'message' => 'Exam submitted successfully!',
            'score' => $attempt->score,
            'total' => $questions->count(),
            'correct' => $correct,
            'wrong' => $wrong,
            'unanswered' => $questions->count() - $correct - $wrong,
        ]);
    }
}
