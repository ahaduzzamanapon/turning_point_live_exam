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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('set null');
            $table->text('question_text');
            $table->enum('question_type', ['SINGLE', 'MULTIPLE'])->default('SINGLE');
            $table->enum('difficulty', ['EASY', 'MEDIUM', 'HARD'])->default('MEDIUM');
            $table->decimal('negative_mark', 5, 2)->default(0.00);
            $table->string('exam_tag')->nullable();
            $table->enum('status', ['PENDING', 'APPROVED'])->default('PENDING');
            $table->text('answer_explanation')->nullable();
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
    }
};
