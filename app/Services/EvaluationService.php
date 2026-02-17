<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class EvaluationService
{
    /**
     * Evaluate an exam attempt and calculate the score.
     *
     * @param ExamAttempt $attempt
     * @return ExamAttempt
     */
    public function evaluateAttempt(ExamAttempt $attempt)
    {
        return DB::transaction(function () use ($attempt) {
            $totalScore = 0;
            $answers = $attempt->answers()->with('question.options')->get();

            foreach ($answers as $answer) {
                $question = $answer->question;

                if (!$question) {
                    continue; // Skip if question no longer exists
                }

                $selectedOptionIds = $answer->selected_options ?? [];

                // Ensure selected_options is an array (it might be null or json decoded)
                if (!is_array($selectedOptionIds)) {
                    $selectedOptionIds = [];
                }

                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->toArray();

                $isCorrect = false;

                if ($question->question_type === 'SINGLE') {
                    // Start SINGLE logic
                    if (count($selectedOptionIds) === 1 && count($correctOptionIds) > 0) {
                        if ($selectedOptionIds[0] == $correctOptionIds[0]) {
                            $isCorrect = true;
                        }
                    }
                    // End SINGLE logic
                } elseif ($question->question_type === 'MULTIPLE') {
                    // Start MULTIPLE logic (Exact Match Required)
                    // Check if no diff between selected and correct
                    if (
                        !empty($selectedOptionIds) &&
                        empty(array_diff($selectedOptionIds, $correctOptionIds)) &&
                        empty(array_diff($correctOptionIds, $selectedOptionIds))
                    ) {
                        $isCorrect = true;
                    }
                    // End MULTIPLE logic
                }

                if ($isCorrect) {
                    $totalScore += 1; // Assuming 1 mark per question for now, or fetch from question/exam logic if dynamic
                } else {
                    // Apply negative marking if incorrect and strictly if it wasn't skipped? 
                    // Usually negative marking applies to wrong answers.
                    // If selected_options is not empty, it's an attempt.
                    if (!empty($selectedOptionIds)) {
                        $totalScore -= $question->negative_mark;
                    }
                }
            }

            // Update Attempt
            $attempt->update([
                'score' => $totalScore,
                'status' => 'COMPLETED', // Or check passed/failed based on cutoff
                'end_time' => now() // If not already set
            ]);

            return $attempt;
        });
    }
}
