<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LogbookPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $magangRole = Role::where('name', 'Magang BPS')->first();

        if (!$magangRole) {
            $this->command->error('Role Magang BPS not found.');
            return;
        }

        $permissions = [
            'view_any_logbook',
            'view_logbook',
            'create_logbook',
            'update_logbook',
            'delete_logbook',
        ];

        foreach ($permissions as $permName) {
            $perm = Permission::firstOrCreate([
                'name'       => $permName,
                'guard_name' => 'web',
            ]);
            if (!$magangRole->hasPermissionTo($perm)) {
                $magangRole->givePermissionTo($perm);
            }
        }

        $this->command->info('Logbook permissions berhasil diberikan ke Magang BPS.');
    }
}
