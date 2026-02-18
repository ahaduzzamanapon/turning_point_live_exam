<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Student API
    Route::get('/student/dashboard', [StudentApiController::class, 'dashboard']);
    Route::get('/student/exams', [StudentApiController::class, 'exams']);
    Route::get('/student/events', [StudentApiController::class, 'events']);
    Route::post('/student/events/{id}/join', [StudentApiController::class, 'joinEvent']);
    Route::post('/student/events/{id}/enter', [StudentApiController::class, 'enterEvent']);
    Route::post('/student/events/exam/{participantId}/submit', [StudentApiController::class, 'submitEventAnswer']);
    Route::post('/student/events/exam/{participantId}/finish', [StudentApiController::class, 'finishEvent']);
    Route::get('/student/events/exam/{participantId}/result', [StudentApiController::class, 'eventResult']);
    Route::get('/student/wallet', [StudentApiController::class, 'wallet']);

    // Exam API
    Route::post('/student/exams/{id}/start', [StudentApiController::class, 'startExam']);
    Route::post('/student/exams/attempt/{attemptId}/submit', [StudentApiController::class, 'submitExamAnswer']);
    Route::post('/student/exams/attempt/{attemptId}/finish', [StudentApiController::class, 'finishExam']);
});
