<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class RenameUserToStudentMenuSeeder extends Seeder
{
    public function run()
    {
        $menu = Menu::where('route', 'admin.users.index')->first();
        if ($menu) {
            $menu->update([
                'title' => 'Student Management',
                'icon' => 'bi bi-people-fill'
            ]);
        }
    }
}
