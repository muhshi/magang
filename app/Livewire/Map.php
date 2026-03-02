<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Settings\SystemSettings;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Map extends Component
{
    public function render()
    {
        $settings   = app(SystemSettings::class);
        $attendance = Attendance::with('user')->get();

        return view('livewire.map', [
            'officeLat'  => $settings->default_office_lat,
            'officeLng'  => $settings->default_office_lng,
            'radius'     => $settings->default_geofence_radius_m,
            'attendance' => $attendance,
        ]);
    }
}
