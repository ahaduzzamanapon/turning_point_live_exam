<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['ONGOING', 'COMPLETED', 'ABANDONED'])->default('ONGOING');
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('percentile', 5, 2)->nullable();
            $table->integer('rank')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->json('selected_options')->nullable(); // Stores array of option IDs
            $table->timestamps();
        });

        // Results table might be redundant if we store score in exam_attempts, 
        // but keeping it as per requirement for clarity or separation if needed.
        // For now, I'll incorporate result fields into exam_attempts as it's 1:1, 
        // but if a separate results table is strictly required I can add it. 
        // Given the prompt, I'll add a view or keeping it simple in attempts is better for now. 
        // Let's create a separate table if user explicitly asked for 'results' table.

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('score', 8, 2);
            $table->decimal('percentile', 5, 2)->nullable();
            $table->integer('rank')->nullable();
            $table->enum('status', ['PASS', 'FAIL'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
    }
};
