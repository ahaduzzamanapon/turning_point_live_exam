<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_exams' => Exam::count(),
            'active_exams' => Exam::where('status', 'PUBLISHED')
                ->where('end_time', '>', now())
                ->count(),
            'total_students' => User::count(), // Temporary fix: Count all users until role logic is confirmed
            'total_attempts' => ExamAttempt::count(),
        ];

        $recent_attempts = ExamAttempt::with(['user', 'exam'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_attempts'));
    }
}
