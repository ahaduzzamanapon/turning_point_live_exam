<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Exam;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        $attempts = ExamAttempt::with(['exam', 'user'])
            ->where('status', 'COMPLETED')
            ->latest()
            ->paginate(20);

        return view('admin.results.index', compact('attempts'));
    }

    public function show($id)
    {
        $attempt = ExamAttempt::with(['exam', 'user', 'answers.question.options'])->findOrFail($id);
        return view('admin.results.show', compact('attempt'));
    }
}
