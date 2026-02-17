<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes
Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');

// Student Exam Routes (Protected by auth AND verified)
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/exams', [App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
    Route::post('/exams/{exam}/start', [App\Http\Controllers\Student\ExamController::class, 'start'])->name('exams.start');
    Route::get('/exam/{attempt}/live', [App\Http\Controllers\Student\ExamController::class, 'live'])->name('exams.live');
    Route::post('/exam/{attempt}/submit', [App\Http\Controllers\Student\ExamController::class, 'submitAnswer'])->name('exams.submit');
    Route::post('/exam/{attempt}/finish', [App\Http\Controllers\Student\ExamController::class, 'finish'])->name('exams.finish');
    Route::get('/exam/{attempt}/result', [App\Http\Controllers\Student\ExamController::class, 'result'])->name('exams.result');

    // Wallet
    Route::get('/wallet', [App\Http\Controllers\Student\WalletController::class, 'index'])->name('wallet.index');

    // Events
    Route::get('/events', [App\Http\Controllers\Student\EventController::class, 'index'])->name('events.index');
    Route::post('/events/{event}/join', [App\Http\Controllers\Student\EventController::class, 'join'])->name('events.join');
});

Route::redirect('/admin', '/admin/login');

// Test Mail Route
Route::get('/test-mail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('This is a test email from TurningPoint Exam System.', function ($message) {
            $message->to('exam@exam.turningandtargetpoint.com') // Sending to self/admin to verify
                ->subject('Test Email - Exam System');
        });
        return 'Email sent successfully to exam@exam.turningandtargetpoint.com! Check inbox.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
