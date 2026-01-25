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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('duration_minutes');
            $table->integer('total_marks');
            $table->boolean('negative_marking')->default(false);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'COMPLETED'])->default('DRAFT');
            $table->timestamps();
        });

        Schema::create('exam_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('total_questions')->default(0);
            $table->integer('easy')->default(0);
            $table->integer('medium')->default(0);
            $table->integer('hard')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_rules');
        Schema::dropIfExists('exams');
    }
};
