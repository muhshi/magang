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
            'ViewAny:Attendance',
            'View:Attendance',
            'Create:Attendance',

            // Cuti
            'ViewAny:Leave',
            'View:Leave',
            'Create:Leave',
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
