<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create Super Admin role with all permissions
        $superAdmin = Role::updateOrCreate(
            ['name' => 'Super Admin'],
            ['name' => 'Super Admin']
        );
        
        $allPermissions = Permission::all();
        $superAdmin->permissions()->sync($allPermissions->pluck('id'));

        // Create Admin role with most permissions (exclude sensitive ones)
        $admin = Role::updateOrCreate(
            ['name' => 'Admin'],
            ['name' => 'Admin']
        );
        
        $adminPermissions = Permission::whereNotIn('name', [
            'roles.delete',
            'permissions.add',
            'permissions.edit',
            'permissions.delete',
        ])->get();
        
        $admin->permissions()->sync($adminPermissions->pluck('id'));

        // Create Editor role with limited permissions
        $editor = Role::updateOrCreate(
            ['name' => 'Editor'],
            ['name' => 'Editor']
        );
        
        $editorPermissions = Permission::whereIn('name', [
            'users.browse',
            'menus.browse',
            'crud-builder.browse',
        ])->get();
        
        $editor->permissions()->sync($editorPermissions->pluck('id'));
    }
}
