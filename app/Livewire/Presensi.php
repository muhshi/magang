<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Schedule;
use App\Settings\SystemSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Presensi extends Component
{
    public $latitude, $longitude;
    public $isWithinRadius = false;

    /**
     * Resolve data presensi: Schedule (jika ada office/shift) → SystemSettings (default).
     */
    private function resolvePresensiData(): array
    {
        $settings = app(SystemSettings::class);
        $schedule = Schedule::where('user_id', Auth::id())->first();

        // Default dari SystemSettings
        $officeName = $settings->default_office_name;
        $officeLat  = $settings->default_office_lat;
        $officeLng  = $settings->default_office_lng;
        $radius     = $settings->default_geofence_radius_m;
        $workStart  = $settings->default_work_start;
        $workEnd    = $settings->default_work_end;
        $shiftName  = 'Default';

        $isWfa     = false;
        $isBanned  = false;

        // Override dari Schedule jika ada office & shift
        if ($schedule) {
            $isWfa    = (bool) $schedule->is_wfa;
            $isBanned = (bool) $schedule->is_banned;

            if ($schedule->office_id && $schedule->office) {
                $officeName = $schedule->office->name;
                $officeLat  = $schedule->office->latitude;
                $officeLng  = $schedule->office->longitude;
                $radius     = $schedule->office->radius;
            }
            if ($schedule->shift_id && $schedule->shift) {
                $workStart = $schedule->shift->start_time;
                $workEnd   = $schedule->shift->end_time;
                $shiftName = $schedule->shift->name;
            }
        }

        return compact(
            'officeName', 'officeLat', 'officeLng', 'radius',
            'workStart', 'workEnd', 'shiftName',
            'schedule', 'isWfa', 'isBanned'
        );
    }

    public function render()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        $data = $this->resolvePresensiData();

        return view('livewire.presensi', [
            'presensiData'   => $data,
            'isWithinRadius' => $this->isWithinRadius,
            'attendance'     => $attendance,
        ])->layout('components.layouts.app');
    }

    /**
     * Hitung jarak dua koordinat (dalam meter) menggunakan Haversine Formula.
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    public function store()
    {
        $this->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $data = $this->resolvePresensiData();

        if ($data['isBanned']) {
            session()->flash('error', 'Akun Anda sedang dibatasi untuk melakukan presensi.');
            return;
        }

        // Server-side Geofence check jika bukan WFA
        if (!$data['isWfa']) {
            $distance = $this->calculateDistance(
                (float) $this->latitude,
                (float) $this->longitude,
                (float) $data['officeLat'],
                (float) $data['officeLng']
            );

            if ($distance > (float) $data['radius']) {
                $this->isWithinRadius = false;
                $this->addError('latitude', 'Anda berada di luar radius kantor (' . round($distance) . 'm dari batas ' . $data['radius'] . 'm).');
                return;
            }
        }

        $this->isWithinRadius = true;

        // Cek cuti
        $today = Carbon::today()->format('Y-m-d');
        $approvedLeave = Leave::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();

        if ($approvedLeave) {
            session()->flash('error', 'Anda sedang mengambil cuti.');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $attendance = Attendance::where('user_id', Auth::id())
                ->whereDate('created_at', now()->toDateString())
                ->lockForUpdate()
                ->first();

            if (!$attendance) {
                Attendance::create([
                    'user_id'             => Auth::id(),
                    'schedule_latitude'   => $data['officeLat'],
                    'schedule_longitude'  => $data['officeLng'],
                    'schedule_start_time' => $data['workStart'],
                    'schedule_end_time'   => $data['workEnd'],
                    'start_latitude'      => $this->latitude,
                    'start_longitude'     => $this->longitude,
                    'start_time'          => now('Asia/Jakarta')->toTimeString(),
                    'end_time'            => now('Asia/Jakarta')->toTimeString(),
                ]);
            } else {
                $attendance->update([
                    'end_latitude'  => $this->latitude,
                    'end_longitude' => $this->longitude,
                    'end_time'      => now('Asia/Jakarta')->toTimeString(),
                ]);
            }
        });

        session()->flash('success', 'Presensi berhasil dicatat.');
        return redirect('admin/attendances');
    }
}
