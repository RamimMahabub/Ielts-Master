<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage users',
            'approve instructors',
            'manage question bank',
            'manage mock tests',
            'attempt mock tests',
            'evaluate submissions',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = Role::findOrCreate('admin');
        $instructor = Role::findOrCreate('instructor');
        $student = Role::findOrCreate('student');

        $admin->syncPermissions($permissions);
        $instructor->syncPermissions(['manage question bank', 'manage mock tests', 'evaluate submissions']);
        $student->syncPermissions(['attempt mock tests']);
    }
}
