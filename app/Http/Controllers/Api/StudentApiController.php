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
                $event->is_registered = $request->user()->events()->where('event_id', $event->id)->exists();
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
}
