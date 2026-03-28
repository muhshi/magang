<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class RekapPresensi extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Rekapitulasi Presensi';
    protected static ?string $title = 'Rekapitulasi Presensi';
    protected static ?string $slug = 'rekap-presensi';
    protected static ?string $navigationGroup = 'Manajemen Presensi';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.rekap-presensi';

    // Filter bulan (format: Y-m)
    public string $selectedMonth;

    public function mount(): void
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
    }

    /**
     * Ambil data rekapitulasi berdasarkan bulan yang dipilih.
     */
    public function getRekapData(): array
    {
        [$year, $month] = explode('-', $this->selectedMonth);

        // Ambil semua user dengan role Magang BPS
        $users = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Magang BPS', 'Alumni Magang']))
            ->with(['internship'])
            ->get();

        $rekap = [];

        foreach ($users as $user) {
            $attendances = Attendance::where('user_id', $user->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get(); // returns Attendance model collection

            $totalHadir     = $attendances->count();
            $totalTerlambat = $attendances->filter(fn(Attendance $a) => $a->isLate())->count();
            $tepatWaktu     = $totalHadir - $totalTerlambat;

            // Sisa magang
            $sisaHari = null;
            $sisaLabel = '-';
            if ($user->internship && $user->internship->end_date) {
                $endDate = Carbon::parse($user->internship->end_date);
                $today   = Carbon::today();
                if ($endDate->isFuture() || $endDate->isToday()) {
                    $sisaHari  = $today->diffInDays($endDate);
                    $sisaLabel = $sisaHari . ' hari';
                } else {
                    $sisaLabel = 'Selesai';
                }
            }

            $rekap[] = [
                'user_id'        => $user->id,
                'nama'           => $user->name,
                'total_hadir'    => $totalHadir,
                'tepat_waktu'    => $tepatWaktu,
                'terlambat'      => $totalTerlambat,
                'sisa_hari'      => $sisaHari,
                'sisa_label'     => $sisaLabel,
            ];
        }

        // Urutkan: yang paling banyak hadir di atas
        usort($rekap, fn($a, $b) => $b['total_hadir'] <=> $a['total_hadir']);

        return $rekap;
    }

    /**
     * Daftar bulan 12 bulan terakhir untuk dropdown
     */
    public function getMonthOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $options[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }
        return $options;
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        return $user->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
