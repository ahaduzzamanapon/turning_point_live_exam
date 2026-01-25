<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExamGeneratorService
{
    /**
     * Generate or retrieve an exam paper for a student.
     *
     * @param Exam $exam
     * @param User $user
     * @return ExamAttempt
     */
    public function generateExamPaper(Exam $exam, User $user)
    {
        // Check if an attempt already exists
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingAttempt) {
            return $existingAttempt;
        }

        return DB::transaction(function () use ($exam, $user) {
            // Create new attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'start_time' => now(),
                'status' => 'ONGOING',
            ]);

            // Fetch Rules
            $rules = $exam->rules;
            $selectedQuestions = collect();

            foreach ($rules as $rule) {
                // Fetch Easy Questions
                if ($rule->easy > 0) {
                    $questions = Question::where('subject_id', $rule->subject_id)
                        ->where('difficulty', 'EASY')
                        ->where('status', 'APPROVED')
                        ->inRandomOrder()
                        ->limit($rule->easy)
                        ->get();
                    $selectedQuestions = $selectedQuestions->merge($questions);
                }

                // Fetch Medium Questions
                if ($rule->medium > 0) {
                    $questions = Question::where('subject_id', $rule->subject_id)
                        ->where('difficulty', 'MEDIUM')
                        ->where('status', 'APPROVED')
                        ->inRandomOrder()
                        ->limit($rule->medium)
                        ->get();
                    $selectedQuestions = $selectedQuestions->merge($questions);
                }

                // Fetch Hard Questions
                if ($rule->hard > 0) {
                    $questions = Question::where('subject_id', $rule->subject_id)
                        ->where('difficulty', 'HARD')
                        ->where('status', 'APPROVED')
                        ->inRandomOrder()
                        ->limit($rule->hard)
                        ->get();
                    $selectedQuestions = $selectedQuestions->merge($questions);
                }
            }

            // Shuffle final question set
            $finalQuestions = $selectedQuestions->shuffle();

            // Store Questions in ExamAnswers (Lock the paper)
            foreach ($finalQuestions as $question) {
                ExamAnswer::create([
                    'exam_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_options' => null // No answer yet
                ]);
            }

            return $attempt;
        });
    }
}
