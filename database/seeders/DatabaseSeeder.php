<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Internship;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RoleSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
        ]);
        $admin->assignRole('super_admin');
        User::factory()->create([
            'name' => 'Muhshi',
            'email' => 'amuhshi@gmail.com',
            'password' => bcrypt('muhshi'),
        ]);
        User::factory()->create([
            'name' => 'Masykuri Zaen',
            'email' => 'zaen@gmail.com',
            'password' => bcrypt('zaen'),
        ]);

        $this->call(OfficeSeeder::class);
        $this->call(ShiftSeeder::class);

        // Dummy Data Peserta Diterima (Test Observer)
        $testUser = User::factory()->create([
            'name' => 'Peserta Test',
            'email' => 'peserta@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $internship = Internship::create([
            'user_id' => $testUser->id,
            'full_name' => 'Peserta Test',
            'birth_place' => 'Jakarta',
            'birth_date' => '2000-01-01',
            'gender' => 'L',
            'address' => 'Jl. Test No. 1',
            'phone' => '08123456789',
            'email' => 'peserta@gmail.com',
            'school_name' => 'Univ Test',
            'education_level' => 'S1',
            'start_date' => '2023-01-01',
            'end_date' => '2023-02-01',
            'letter_file' => 'test.pdf',
            'photo_file' => 'test.jpg',
            'status' => 'pending',
        ]);

        // Update status ke 'accepted' untuk memicu InternshipObserver
        $internship->update(['status' => 'accepted']);
    }
}
