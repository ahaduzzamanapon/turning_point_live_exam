<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class EventMenuSeeder extends Seeder
{
    public function run()
    {
        // Check if already exists to avoid duplicates
        if (Menu::where('title', 'Event Management (New)')->exists()) {
            return;
        }

        Menu::create([
            'title' => 'Event Management (New)',
            'route' => 'admin.events.index',
            'icon' => 'fas fa-calendar-alt', // Using FontAwesome icon
            'order' => 6, // Adjust order as needed
        ]);
    }
}
