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
        $calonMagang = Role::findByName('Calon Magang');
        $calonMagang->syncPermissions([
            'view_any_internship',
            'view_internship',
            'create_internship',
            'view_any_certificate',
            'view_certificate',
        ]);

        // ===== Magang BPS =====
        // Peserta magang aktif: presensi, cuti, lihat sertifikat + generate PDF
        $magangBps = Role::findByName('Magang BPS');
        $magangBps->syncPermissions([
            // Presensi
            'view_any_attendance',
            'view_attendance',
            'page_Presensi',
            // Cuti
            'view_any_leave',
            'view_leave',
            'create_leave',
            // Sertifikat (hanya view, create oleh super_admin)
            'view_any_certificate',
            'view_certificate',
        ]);

        // ===== Alumni Magang =====
        // Alumni: lihat data dan sertifikat
        $alumni = Role::findByName('Alumni Magang');
        $alumni->syncPermissions([
            'view_any_internship',
            'view_internship',
            'view_any_certificate',
            'view_certificate',
        ]);

        $this->command->info('✅ Permission untuk Calon Magang, Magang BPS, dan Alumni Magang berhasil di-assign.');
    }
}
