<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            'users' => ['browse', 'add', 'edit', 'delete'],
            'roles' => ['browse', 'add', 'edit', 'delete'],
            'permissions' => ['browse', 'add', 'edit', 'delete'],
            'menus' => ['browse', 'add', 'edit', 'delete'],
            'theme' => ['browse', 'edit'],
            'settings' => ['browse', 'edit'],
            'crud-builder' => ['browse', 'add'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::updateOrCreate(
                    ['name' => "{$module}.{$action}"],
                    ['name' => "{$module}.{$action}"]
                );
            }
        }
    }
}
