<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Internship;
use App\Models\Leave;
use App\Models\Logbook;
use App\Models\Ticket;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternDashboard extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Magang BPS') ?? false;
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        // Data internship
        $internship = Internship::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$internship) {
            return [
                Stat::make('Status', 'Belum Terdaftar')
                    ->description('Tidak ada data magang aktif')
                    ->color('danger'),
            ];
        }

        // Hitung sisa hari magang
        $endDate = Carbon::parse($internship->end_date);
        $sisaHari = Carbon::today()->diffInDays($endDate, false);
        $totalHari = Carbon::parse($internship->start_date)->diffInDays($endDate);
        $hariTerlewat = $totalHari - max($sisaHari, 0);

        // Statistik presensi bulan ini
        $bulanIni = Carbon::now()->startOfMonth();
        $hariKerja = Carbon::now()->startOfMonth()->diffInWeekdays(Carbon::now()); // Approx hari kerja
        $presensiCount = Attendance::where('user_id', $user->id)
            ->where('start_time', '>=', $bulanIni)
            ->count();

        // Cuti/izin bulan ini
        $cutiBulanIni = Leave::where('user_id', $user->id)
            ->where('start_date', '>=', $bulanIni)
            ->where('status', 'approved')
            ->count();

        // Alpha = hari kerja - hadir - cuti
        $alpha = max($hariKerja - $presensiCount - $cutiBulanIni, 0);

        // Penugasan aktif
        $ticketAktif = DB::table('tickets')
            ->join('ticket_users', 'tickets.id', '=', 'ticket_users.ticket_id')
            ->where('ticket_users.user_id', $user->id)
            ->where(function ($q) {
                $q->whereNull('tickets.status')
                  ->orWhereNotIn('tickets.status', ['selesai', 'ditolak']);
            })
            ->count();

        // Logbook bulan ini
        $logbookCount = Logbook::where('user_id', $user->id)
            ->where('tanggal_pengisian', '>=', $bulanIni)
            ->count();

        return [
            Stat::make('Sisa Magang', max($sisaHari, 0) . ' hari')
                ->description($internship->school_name . ' • ' . Carbon::parse($internship->start_date)->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color($sisaHari <= 7 ? 'danger' : ($sisaHari <= 30 ? 'warning' : 'success')),

            Stat::make('Hadir', $presensiCount . ' hari')
                ->description('Presensi bulan ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Izin/Cuti', $cutiBulanIni . ' hari')
                ->description('Cuti disetujui bulan ini')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('warning'),

            Stat::make('Alpha', $alpha . ' hari')
                ->description('Tidak hadir tanpa izin')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($alpha > 0 ? 'danger' : 'success'),

            Stat::make('Penugasan Aktif', $ticketAktif . ' tugas')
                ->description('Tugas yang belum selesai')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color($ticketAktif > 5 ? 'danger' : ($ticketAktif > 0 ? 'warning' : 'success')),

            Stat::make('Progress Magang', round(($hariTerlewat / max($totalHari, 1)) * 100) . '%')
                ->description($hariTerlewat . ' dari ' . $totalHari . ' hari')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
}
