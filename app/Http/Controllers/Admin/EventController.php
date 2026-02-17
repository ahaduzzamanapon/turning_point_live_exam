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
            'prize_pool' => 'nullable|array',
            'prize_pool.*.rank' => 'required_with:prize_pool',
            'prize_pool.*.amount' => 'required_with:prize_pool|numeric',
        ]);

        $slug = Str::slug($validated['title']) . '-' . Str::random(6);
        $posterPath = null;

        if ($request->hasFile('poster_image')) {
            $posterPath = $request->file('poster_image')->store('events', 'public');
        }

        $prizePool = [];
        if ($request->has('prize_pool')) {
            foreach ($request->prize_pool as $pool) {
                if (!empty($pool['rank']) && !empty($pool['amount'])) {
                    $prizePool[] = [
                        'rank' => $pool['rank'],
                        'amount' => $pool['amount']
                    ];
                }
            }
        }

        $event = Event::create([
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

        // Auto-assign Questions based on Total Marks (Assuming 1 mark per question)
        // This is a basic implementation. Ideally, Admin should select or define rules.
        // But to fix "auto not created", we do this.
        $questionsCount = $validated['total_marks']; // e.g. 100 marks = 100 questions.

        $questions = \App\Models\Question::where('status', 'APPROVED') // Assuming 'APPROVED' status exists
            ->inRandomOrder()
            ->limit($questionsCount)
            ->get();

        if ($questions->isNotEmpty()) {
            // Attach with order
            $pivotData = [];
            foreach ($questions as $index => $q) {
                $pivotData[$q->id] = ['order' => $index + 1];
            }
            $event->questions()->attach($pivotData);
        }

        return redirect()->route('admin.events.paper.index', $event->id)->with('success', 'Event created! You can now review and manage the question paper.');
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
            'prize_pool' => 'nullable|array',
            'prize_pool.*.rank' => 'required_with:prize_pool',
            'prize_pool.*.amount' => 'required_with:prize_pool|numeric',
            'status' => 'required|in:UPCOMING,LIVE,COMPLETED,CANCELLED',
        ]);

        if ($request->hasFile('poster_image')) {
            // Delete old
            if ($event->poster_image) {
                Storage::disk('public')->delete($event->poster_image);
            }
            $event->poster_image = $request->file('poster_image')->store('events', 'public');
        }

        $prizePool = $event->prize_pool_config; // Default to existing
        if ($request->has('prize_pool')) {
            $prizePool = []; // Reset if new data provided
            foreach ($request->prize_pool as $pool) {
                if (!empty($pool['rank']) && !empty($pool['amount'])) {
                    $prizePool[] = [
                        'rank' => $pool['rank'],
                        'amount' => $pool['amount']
                    ];
                }
            }
        }

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

    public function results(Event $event)
    {
        $event->load(['participants']);

        $totalParticipants = $event->participants->count();
        $totalCollection = $event->participants->where('pivot.payment_status', 'PAID')->count() * $event->registration_fee;

        // Calculate Distributed/Projected Prize
        $totalPrizeDistributed = $event->participants->sum('pivot.prize_won');

        $profit = $totalCollection - $totalPrizeDistributed;

        // Rank Participants by Score DESC, then Time Taken ASC (using updated_at as proxy for now)
        $participants = $event->participants()
            ->orderByPivot('score', 'desc')
            ->orderByPivot('updated_at', 'asc')
            ->get();

        return view('admin.events.results', compact('event', 'participants', 'totalParticipants', 'totalCollection', 'totalPrizeDistributed', 'profit'));
    }

    public function distributePrizes(Event $event)
    {
        $prizeConfig = $event->prize_pool_config ?? [];

        if (empty($prizeConfig)) {
            return back()->with('error', 'No prize pool configured.');
        }

        // Get Ranked Participants
        $participants = $event->participants()
            ->where('event_participants.status', 'COMPLETED')
            ->orderByPivot('score', 'desc')
            ->orderByPivot('updated_at', 'asc')
            ->get();

        $count = 0;

        foreach ($participants as $index => $p) {
            $rank = $index + 1;
            $prizeAmount = 0;

            // Find prize for this rank
            foreach ($prizeConfig as $key => $rule) {
                // Support Key-Value format (Rank => Amount) if rule is not array
                if (!is_array($rule)) {
                    // Assume Key is Rank, Value is Amount
                    $ruleRank = $key;
                    $amount = $rule;
                } else {
                    $ruleRank = $rule['rank'] ?? null;
                    $amount = $rule['amount'] ?? 0;
                }

                if (!$ruleRank)
                    continue;

                if (str_contains((string) $ruleRank, '-')) {
                    [$start, $end] = explode('-', $ruleRank);
                    if ($rank >= $start && $rank <= $end) {
                        $prizeAmount = $amount;
                        break;
                    }
                } else {
                    if ($rank == $ruleRank) {
                        $prizeAmount = $amount;
                        break;
                    }
                }
            }

            // Always update rank, update prize if > 0
            if ($prizeAmount > 0) {
                // Check if already awarded (optional, but good for idempotency if needed, or allow overwrite)
                // For now, allow overwrite or re-distribution

                // Credit to Wallet
                $p->wallet->deposit($prizeAmount, "Prize for Event: " . $event->title . " (Rank $rank)", $event->id);

                $p->pivot->prize_won = $prizeAmount;
                $p->pivot->rank = $rank;
                $p->pivot->save();

                $count++;
            } else {
                $p->pivot->rank = $rank;
                $p->pivot->prize_won = 0;
                $p->pivot->save();
            }
        }

        return back()->with('success', "Prizes distributed to $count participants.");
    }
}
