<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Dashboard
        Menu::create([
            'title' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'bi bi-speedometer2',
            'order' => 1,
        ]);

        // User Management
        Menu::create([
            'title' => 'User Management',
            'route' => 'admin.users.index',
            'icon' => 'bi bi-people',
            'order' => 2,
        ]);

        // Roles & Permissions
        $rolePermission = Menu::create([
            'title' => 'Roles & Permissions',
            'icon' => 'bi bi-shield-lock',
            'order' => 3,
        ]);

        Menu::create([
            'title' => 'Roles',
            'route' => 'admin.roles.index',
            'parent_id' => $rolePermission->id,
            'order' => 1,
        ]);

        Menu::create([
            'title' => 'Permissions',
            'route' => 'admin.permissions.index',
            'parent_id' => $rolePermission->id,
            'order' => 2,
        ]);

        // Configurations
        $configurations = Menu::create([
            'title' => 'Configurations',
            'icon' => 'fa-solid fa-house-flag',
            'order' => 4,
        ]);

        Menu::create([
            'title' => 'Menu Builder',
            'route' => 'admin.menus.index',
            'parent_id' => $configurations->id,
            'order' => 1,
        ]);

        Menu::create([
            'title' => 'CRUD Builder',
            'route' => 'crud-builder.index',
            'parent_id' => $configurations->id,
            'order' => 2,
        ]);

        Menu::create([
            'title' => 'Settings',
            'route' => 'admin.settings.index',
            'parent_id' => $configurations->id,
            'order' => 3,
        ]);

        Menu::create([
            'title' => 'Theme Settings',
            'route' => 'admin.theme.index',
            'parent_id' => $configurations->id,
            'order' => 4,
        ]);
    }
}
