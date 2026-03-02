<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            'Abdul Khalim',
            'Ahmad Syahdi Hamid, SST',
            'Aji Wahyu Ramadhani, SST, M.Si',
            'Ani Murwani',
            'Anjung Budi Cahyono, S.M.',
            'Aris Sutikno, SE',
            'Budhi Setyono, S.Pd.',
            'Dyah Makutaning Dewi, S.Tr.Stat.',
            'Edy Budi Utomo, SE.',
            'Evi Wahyu Wardani, S.Si',
            'Henri Wagiyanto, S.Pt., M.Ec.Dev, M.A.',
            'Herwastuti Dwi Oktaviani, A.Md.',
            'Imam Sugiarto',
            'Ir. Budi Arif Nugroho, M.Si.',
            'Khusnul Khotimah, A.Md',
            'Lydia Mirna Wening Handayani, SST, M.Si',
            'M. Abdul Muhshi, SST',
            'M. Masykuri Zaen, SST',
            "Ma'ruf Susilo Yuwono",
            'Moh.Saiful Hidayah Brotowiyono, S.Si, MM.',
            'Muhamad Abdul Aziz, SST',
            'Muna, S.Si',
            "Musyafa'ah, A.Md",
            'Nunung Susanti, A.Md',
            'Nur Kurniawati, SST',
            'Paramitha Hanifia, SST',
            'Rini Astuti, SST., M.S.E.',
            'Siswo',
            'Siswo Pranyoto',
            'Sucipto, ST',
            'Sulaeman, S.Stat',
            'Supriharto',
            'Wiwi Wilujeng Kusumawati, SE, M.M.',
            'Yudia Pratidina Hasibuan, SST',
            'M Guntur Ilham S.Tr.Stat',
            'Khomarudin, SST',
            'Suko Prayogi, SP, ME',
            'Retno Dian Ika Wati, SST, MM',
            'Desi Indah Puji Lestari A.Md.Bns.',
            'Deki Andrianto',
            'Adi Bagus Utomo',
            'Saiful Mujab, S.Pd',
            'Guruh Oktasatria Rachma',
        ];

        foreach ($employees as $name) {
            Employee::firstOrCreate(['name' => $name]);
        }
    }
}
