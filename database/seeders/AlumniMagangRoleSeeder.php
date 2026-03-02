<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AlumniMagangRoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'Alumni Magang',
            'guard_name' => 'web',
        ]);

        $this->command->info('Role Alumni Magang berhasil dibuat.');
    }
}
