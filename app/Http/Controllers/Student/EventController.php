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
}
