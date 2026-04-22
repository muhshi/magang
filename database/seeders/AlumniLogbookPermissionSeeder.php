<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AlumniLogbookPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'Alumni Magang']);

        // Permission yang boleh (hanya lihat)
        $permissions = [
            'ViewAny:Logbook',
            'View:Logbook',
        ];

        foreach ($permissions as $permName) {
            $perm = Permission::firstOrCreate([
                'name'       => $permName,
                'guard_name' => 'web',
            ]);
            if (!$role->hasPermissionTo($perm)) {
                $role->givePermissionTo($perm);
            }
        }

        $this->command->info('Logbook permissions (view only) diberikan ke Alumni Magang.');
    }
}
