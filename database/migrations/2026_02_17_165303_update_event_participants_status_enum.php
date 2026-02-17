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
        Schema::table('event_participants', function (Blueprint $table) {
            // We use raw statement for ENUM change in MySQL - adding COMPLETED
            \DB::statement("ALTER TABLE event_participants MODIFY COLUMN status ENUM('REGISTERED', 'ATTEMPTED', 'DISQUALIFIED', 'COMPLETED') DEFAULT 'REGISTERED'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_participants', function (Blueprint $table) {
            \DB::statement("ALTER TABLE event_participants MODIFY COLUMN status ENUM('REGISTERED', 'ATTEMPTED', 'DISQUALIFIED') DEFAULT 'REGISTERED'");
        });
    }
};
