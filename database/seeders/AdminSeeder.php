<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // Assign Super Admin role
        $superAdminRole = \App\Models\Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $admin->roles()->attach($superAdminRole->id);
        }
    }
}
