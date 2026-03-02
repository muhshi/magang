<?php

namespace Database\Seeders;

use App\Models\InternGroup;
use Illuminate\Database\Seeder;

class InternGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'SMKN 1 Demak',
            'UNNES',
            'USM',
            'STIC',
            'Firna',
            'UNISSULA',
        ];

        foreach ($groups as $name) {
            InternGroup::firstOrCreate(['name' => $name]);
        }
    }
}
