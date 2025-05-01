<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Map extends Component
{


    public function render()
    {
        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        $attendance = Attendance::with('user')->get();
        //dd($schedule);
        return view('livewire.map', [
            'schedule' => $schedule,
            'attendance' => $attendance,
        ]);
    }
}
