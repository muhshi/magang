<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Calon Magang =====
        // Bisa daftar magang, lihat pendaftarannya sendiri, dan lihat sertifikat
        $calonMagang = Role::firstOrCreate(['name' => 'Calon Magang', 'guard_name' => 'web']);
        
        $calonMagangPermissions = [
            'ViewAny:Internship',
            'View:Internship',
            'Create:Internship',
            'ViewAny:Certificate',
            'View:Certificate',
        ];

        foreach ($calonMagangPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $calonMagang->syncPermissions($calonMagangPermissions);

        // ===== Magang BPS =====
        // Peserta magang aktif: presensi, cuti, lihat sertifikat + generate PDF
        $magangBps = Role::firstOrCreate(['name' => 'Magang BPS', 'guard_name' => 'web']);
        
        $magangBpsPermissions = [
            // Presensi
            'ViewAny:Attendance',
            'View:Attendance',
            // Cuti
            'ViewAny:Leave',
            'View:Leave',
            'Create:Leave',
            // Sertifikat
            'ViewAny:Certificate',
            'View:Certificate',
        ];

        foreach ($magangBpsPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $magangBps->syncPermissions($magangBpsPermissions);

        // ===== Alumni Magang =====
        // Alumni: lihat data dan sertifikat
        $alumni = Role::firstOrCreate(['name' => 'Alumni Magang', 'guard_name' => 'web']);
        
        $alumniPermissions = [
            'ViewAny:Internship',
            'View:Internship',
            'ViewAny:Certificate',
            'View:Certificate',
        ];

        foreach ($alumniPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $alumni->syncPermissions($alumniPermissions);

        $this->command->info('✅ Permission untuk Calon Magang, Magang BPS, dan Alumni Magang berhasil di-assign.');
    }
}
