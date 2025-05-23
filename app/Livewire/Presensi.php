<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Presensi extends Component
{
    public $latitude, $longitude;
    public $isWithinRadius = false;

    public function render()
    {
        $attendance = Attendance::where('user_id', Auth::user()->id)->whereDate('created_at', date('Y-m-d'))->first();
        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        //dd($schedule);
        return view('livewire.presensi', [
            'schedule' => $schedule,
            'isWithinRadius' => $this->isWithinRadius,
            'attendance' => $attendance,
        ]);
    }

        public function store()
    {
        $this->isWithinRadius = true;
        $this->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $schedule = Schedule::where('user_id', Auth::id())->first();

        $today = Carbon::today()->format('Y-m-d');
        $approvedLeave = Leave::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();

        if($approvedLeave){
            session()->flash('error', 'Anda sedang mengambil cuti.');
            return;
        }

        if (!$schedule) {
            session()->flash('error', 'Jadwal presensi tidak ditemukan.');
            return;
        }

        if ($schedule->is_banned) {
            session()->flash('error', 'Anda diblokir dari sistem presensi. Silakan hubungi admin.');
            return;
        }

        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if (!$attendance) {
            Attendance::create([
                'user_id' => Auth::id(),
                'schedule_latitude' => $schedule->office->latitude,
                'schedule_longitude' => $schedule->office->longitude,
                'schedule_start_time' => $schedule->shift->start_time,
                'schedule_end_time' => $schedule->shift->end_time,
                'start_latitude' => $this->latitude,
                'start_longitude' => $this->longitude,
                'start_time' => now('Asia/Jakarta')->toTimeString(),
                'end_time' => now('Asia/Jakarta')->toTimeString(),
            ]);
        } else {
            $attendance->update([
                'end_latitude' => $this->latitude,
                'end_longitude' => $this->longitude,
                'end_time' => now('Asia/Jakarta')->toTimeString(),
            ]);
        }

        session()->flash('success', 'Presensi berhasil dicatat.');
        return redirect('admin/attendances');
    }
}
