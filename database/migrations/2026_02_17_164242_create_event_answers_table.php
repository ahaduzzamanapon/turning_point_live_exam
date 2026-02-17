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
        Schema::create('event_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_participant_id')->constrained('event_participants')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->json('selected_options')->nullable(); // Array of option IDs
            $table->boolean('is_correct')->default(false);
            $table->decimal('marks_obtained', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_answers');
    }
};
