<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Calon Magang', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Magang BPS', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Alumni Magang', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Pegawai BPS', 'guard_name' => 'web']);
    }
}
