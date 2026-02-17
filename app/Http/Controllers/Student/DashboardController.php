<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Event;
use App\Models\ExamAttempt;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Stats
        $availableExamsCount = Exam::where('status', 'PUBLISHED')->count();
        $upcomingEventsCount = Event::where('status', 'PUBLISHED')
            ->where('start_time', '>', now())
            ->count();

        // Recent Results
        $recentResults = ExamAttempt::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'availableExamsCount',
            'upcomingEventsCount',
            'recentResults'
        ));
    }
}
