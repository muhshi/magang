<?php

namespace App\Observers;

use App\Models\Internship;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Office;

class InternshipObserver
{
    public function updated(Internship $internship): void
    {
        // script mengecek status peserta (diterima/tidak)
        if ($internship->isDirty('status') && $internship->status === 'accepted') {

            // cari data user terkait
            $user = $internship->user;

            // mencari shift jam 8 (ex: shift pertama atau based on nama/jam)
            $shift = Shift::where('start_time', '08:00:00')->first();
        
            // ambil default apabila tidak ada yang pas
            if(!$shift) $shift = Shift::first();
        
            // cari office default
            $office = Office::first();
            
            // cek apakah peserta sudah punya jadwal (menghindari duplikat)
            $existingSchedule = Schedule::where('user_id', $user->id)->first();

            if (!$existingSchedule && $shift && $office) {
                Schedule::create([
                    'user_id' => $user->id,
                    'shift_id' => $shift->id,
                    'office_id' => $office->id,
                    'is_wfa' => false,
                    'is_banned'=> false,
                ]);
            }
        }
    }
}