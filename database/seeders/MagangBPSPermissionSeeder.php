<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MagangBPSPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'Magang BPS', 'guard_name' => 'web']);

        $permissions = [
            // Presensi
            'view_any_attendance',
            'view_attendance',

            // Cuti
            'view_any_leave',
            'view_leave',
            'create_leave',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);

            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }

        $this->command->info('Permission Presensi & Cuti berhasil diberikan ke role Magang BPS.');
    }
}
