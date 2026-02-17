<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster_image' => 'nullable|image|max:2048', // 2MB Max
            'registration_fee' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'negative_marking' => 'required|numeric|min:0',
            'prize_pool_json' => 'nullable|json', // We'll accept JSON string from UI
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(6);
        $posterPath = null;

        if ($request->hasFile('poster_image')) {
            $posterPath = $request->file('poster_image')->store('events', 'public');
        }

        $prizePool = $validated['prize_pool_json'] ? json_decode($validated['prize_pool_json'], true) : [];

        Event::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'poster_image' => $posterPath,
            'registration_fee' => $validated['registration_fee'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $validated['total_marks'],
            'negative_marking' => $validated['negative_marking'],
            'prize_pool_config' => $prizePool,
            'status' => 'UPCOMING',
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster_image' => 'nullable|image|max:2048',
            'registration_fee' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'negative_marking' => 'required|numeric|min:0',
            'prize_pool_json' => 'nullable|json',
            'status' => 'required|in:UPCOMING,LIVE,COMPLETED,CANCELLED',
        ]);

        if ($request->hasFile('poster_image')) {
            // Delete old
            if ($event->poster_image) {
                Storage::disk('public')->delete($event->poster_image);
            }
            $event->poster_image = $request->file('poster_image')->store('events', 'public');
        }

        $prizePool = $validated['prize_pool_json'] ? json_decode($validated['prize_pool_json'], true) : $event->prize_pool_config;

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'registration_fee' => $validated['registration_fee'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $validated['total_marks'],
            'negative_marking' => $validated['negative_marking'],
            'prize_pool_config' => $prizePool,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        if ($event->poster_image) {
            Storage::disk('public')->delete($event->poster_image);
        }
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }
}
