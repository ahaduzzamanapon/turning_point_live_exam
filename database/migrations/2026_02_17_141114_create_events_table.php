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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('poster_image')->nullable(); // For event poster
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('duration_minutes')->default(60);
            $table->integer('total_marks')->default(100);
            $table->decimal('negative_marking', 4, 2)->default(0); // per wrong answer
            $table->json('prize_pool_config')->nullable(); // e.g. {"1": 500, "2": 300}
            $table->enum('status', ['UPCOMING', 'LIVE', 'COMPLETED', 'CANCELLED'])->default('UPCOMING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
