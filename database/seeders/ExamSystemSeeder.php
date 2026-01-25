<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;

class ExamSystemSeeder extends Seeder
{
    public function run()
    {
        // Create Subjects
        $bangla = Subject::create(['name' => 'Bangla', 'status' => true]);
        $english = Subject::create(['name' => 'English', 'status' => true]);
        $math = Subject::create(['name' => 'General Math', 'status' => true]);
        $gk = Subject::create(['name' => 'General Knowledge', 'status' => true]);

        // Create Topics
        $banglaTopic1 = Topic::create(['subject_id' => $bangla->id, 'name' => 'Literature']);
        $banglaTopic2 = Topic::create(['subject_id' => $bangla->id, 'name' => 'Grammar']);

        $englishTopic1 = Topic::create(['subject_id' => $english->id, 'name' => 'Grammar']);
        $englishTopic2 = Topic::create(['subject_id' => $english->id, 'name' => 'Literature']);

        $mathTopic1 = Topic::create(['subject_id' => $math->id, 'name' => 'Algebra']);
        $mathTopic2 = Topic::create(['subject_id' => $math->id, 'name' => 'Geometry']);

        // Create Sample Questions (Bangla)
        $this->createQuestion($bangla->id, $banglaTopic1->id, 'Who is the writer of "Gitanjali"?', 'SINGLE', 'EASY', [
            ['text' => 'Kazi Nazrul Islam', 'correct' => false],
            ['text' => 'Rabindranath Tagore', 'correct' => true],
            ['text' => 'Jibanananda Das', 'correct' => false],
            ['text' => 'Jashimuddin', 'correct' => false],
        ]);

        // Create Sample Questions (English)
        $this->createQuestion($english->id, $englishTopic1->id, 'What is the synonym of "Happy"?', 'SINGLE', 'EASY', [
            ['text' => 'Sad', 'correct' => false],
            ['text' => 'Joyful', 'correct' => true],
            ['text' => 'Angry', 'correct' => false],
            ['text' => 'Bored', 'correct' => false],
        ]);

        $this->createQuestion($english->id, $englishTopic1->id, 'Select the correct prepositions:', 'MULTIPLE', 'MEDIUM', [
            ['text' => 'He is afraid __ dogs.', 'correct' => true], // of
            ['text' => 'He is interested __ music.', 'correct' => true], // in
            ['text' => 'He is good __ math.', 'correct' => true], // at
            ['text' => 'He is busy __ work.', 'correct' => false], // with (maybe not best example for multiple correct logic check but ok for demo)
        ]);

        // Create Sample Questions (Math)
        $this->createQuestion($math->id, $mathTopic1->id, 'Solve for x: 2x + 5 = 15', 'SINGLE', 'EASY', [
            ['text' => '5', 'correct' => true],
            ['text' => '10', 'correct' => false],
            ['text' => '2', 'correct' => false],
            ['text' => '7', 'correct' => false],
        ]);
        // Create Student User
        $student = \App\Models\User::firstOrCreate(
            ['email' => 'student@example.com'],
            ['name' => 'John Student', 'password' => \Illuminate\Support\Facades\Hash::make('password')]
        );

        // Create Examiner User (User with Examiner role if applicable, or Admin)
        // For now, assuming Examiner is an Admin with restricted permissions or just an Admin
        $examiner = \App\Models\Admin::firstOrCreate(
            ['email' => 'examiner@example.com'],
            ['name' => 'John Examiner', 'password' => \Illuminate\Support\Facades\Hash::make('password')]
        );
        // Assign role if needed (skipping specific role assignment logic for simplicity unless 'Examiner' role exists)

        // Create Live Exam
        $exam = \App\Models\Exam::create([
            'title' => 'Live Scholarship Test 2026',
            'duration_minutes' => 60,
            'total_marks' => 100,
            'negative_marking' => true,
            'start_time' => now()->subMinutes(10), // Started 10 mins ago
            'end_time' => now()->addHours(2),      // Ends in 2 hours
            'status' => 'PUBLISHED',
        ]);

        // Add Rules to Exam
        \App\Models\ExamRule::create([
            'exam_id' => $exam->id,
            'subject_id' => $english->id, // Use ID from created subject
            'easy' => 1,
            'medium' => 1,
            'hard' => 0,
            'total_questions' => 2
        ]);
    }

    private function createQuestion($subjectId, $topicId, $text, $type, $difficulty, $options)
    {
        $question = Question::create([
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
            'question_text' => $text,
            'question_type' => $type,
            'difficulty' => $difficulty,
            'negative_mark' => 0.25,
            'status' => 'APPROVED',
            'answer_explanation' => 'This is the correct explanation for: ' . $text
        ]);

        foreach ($options as $option) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $option['text'],
                'is_correct' => $option['correct']
            ]);
        }
    }
}
