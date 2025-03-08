<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CoursePermissionSeeder extends Seeder
{
    public function run()
    {
        // Course izinlerini tanımla
        $permissions = [
            'course.view',
            'course.list',
            'course.create',
            'course.edit',
            'course.delete',
            'course.toggle-status',
            'course.toggle-featured',
            'course.update-order',
        ];

        // İzinleri oluştur
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Super Admin rolüne tüm izinleri ata
        $superAdminRole = Role::findByName('super-admin');
        $superAdminRole->givePermissionTo($permissions);

        // Admin rolüne bazı izinleri ata
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo([
            'course.view',
            'course.list',
            'course.create',
            'course.edit',
            'course.toggle-status',
            'course.toggle-featured',
            'course.update-order',
        ]);

        // Editor rolüne sınırlı izinler
        $editorRole = Role::findByName('editor');
        $editorRole->givePermissionTo([
            'course.view',
            'course.list',
            'course.create',
            'course.edit',
        ]);
    }
}