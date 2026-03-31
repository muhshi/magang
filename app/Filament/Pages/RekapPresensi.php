<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * Ambil daftar tanggal libur nasional dari API untuk tahun tertentu.
     * Hasilnya di-cache selama 30 hari agar tidak memanggil API terus-menerus.
     *
     * @return array<string> Array berisi tanggal libur format 'Y-m-d'
     */
    private function getHolidays(int $year): array
    {
        return Cache::remember("holidays_{$year}", now()->addDays(30), function () use ($year) {
            try {
                $response = Http::timeout(10)->get("https://libur.deno.dev/api", [
                    'year' => $year,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // Simpan juga data lengkap (nama libur) di cache terpisah
                    Cache::put("holidays_{$year}_full", $data, now()->addDays(30));

                    return collect($data)->pluck('date')->toArray();
                }
            } catch (\Exception $e) {
                Log::warning("Gagal mengambil data hari libur dari API: {$e->getMessage()}");
            }

            return []; // fallback: tidak ada data libur
        });
    }

    /**
     * Ambil data lengkap hari libur (termasuk nama) untuk tahun tertentu.
     */
    public function getHolidaysFull(int $year): array
    {
        // Panggil getHolidays dulu untuk memastikan cache terisi
        $this->getHolidays($year);

        return Cache::get("holidays_{$year}_full", []);
    }

    /**
     * Hitung jumlah hari kerja efektif antara dua tanggal.
     * Mengecualikan hari Sabtu, Minggu, dan tanggal libur nasional.
     */
    private function countWorkingDays(Carbon $start, Carbon $end, array $holidays): int
    {
        if ($start->greaterThan($end)) {
            return 0;
        }

        $count = 0;
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            // Lewati akhir pekan (Sabtu = 6, Minggu = 0)
            if ($date->isWeekend()) {
                continue;
            }
            // Lewati hari libur nasional
            if (in_array($date->format('Y-m-d'), $holidays)) {
                continue;
            }
            $count++;
        }

        return $count;
    }

    /**
     * Ambil data rekapitulasi berdasarkan bulan yang dipilih.
     */
    public function getRekapData(): array
    {
        [$year, $month] = explode('-', $this->selectedMonth);

        // Ambil data hari libur nasional untuk tahun ini
        $holidays = $this->getHolidays((int) $year);

        // Rentang bulan yang dipilih
        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $monthEnd   = $monthStart->copy()->endOfMonth();
        $today      = Carbon::today();

        // Batas akhir untuk perhitungan (jangan melebihi hari ini jika bulan berjalan)
        $effectiveEnd = $monthEnd->lessThanOrEqualTo($today) ? $monthEnd : $today;

        // Hitung total hari kerja efektif untuk bulan penuh (sebelum clamp per user)
        $totalHariEfektifBulan = $this->countWorkingDays($monthStart, $effectiveEnd, $holidays);

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

            // Hitung hari efektif per-user (clamp berdasarkan tanggal magang)
            $userStart = $monthStart->copy();
            $userEnd   = $effectiveEnd->copy();

            if ($user->internship) {
                if ($user->internship->start_date) {
                    $internStart = Carbon::parse($user->internship->start_date);
                    if ($internStart->greaterThan($userStart)) {
                        $userStart = $internStart;
                    }
                }
                if ($user->internship->end_date) {
                    $internEnd = Carbon::parse($user->internship->end_date);
                    if ($internEnd->lessThan($userEnd)) {
                        $userEnd = $internEnd;
                    }
                }
            }

            $hariEfektifUser = $this->countWorkingDays($userStart, $userEnd, $holidays);

            // Ambil data cuti yang disetujui untuk user ini pada bulan berjalan
            $leaves = Leave::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where(function ($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('start_date', [$monthStart, $monthEnd])
                      ->orWhereBetween('end_date', [$monthStart, $monthEnd])
                      ->orWhere(function ($q2) use ($monthStart, $monthEnd) {
                          $q2->where('start_date', '<', $monthStart)
                             ->where('end_date', '>', $monthEnd);
                      });
                })->get();

            $totalCuti = 0;
            foreach ($leaves as $leave) {
                // Batasi rentang cuti hanya di dalam rentang waktu user di bulan tsb
                $leaveStart = Carbon::parse($leave->start_date)->max($userStart);
                $leaveEnd   = Carbon::parse($leave->end_date)->min($userEnd);

                if ($leaveStart->lessThanOrEqualTo($leaveEnd)) {
                    $totalCuti += $this->countWorkingDays($leaveStart, $leaveEnd, $holidays);
                }
            }

            // Hitung Tanpa Izin (Total Hadir + Cuti)
            $tanpaIzin = max(0, $hariEfektifUser - ($totalHadir + $totalCuti));

            // Sisa magang
            $sisaHari = null;
            $sisaLabel = '-';
            if ($user->internship && $user->internship->end_date) {
                $endDate = Carbon::parse($user->internship->end_date);
                if ($endDate->isFuture() || $endDate->isToday()) {
                    $sisaHari  = $today->diffInDays($endDate);
                    $sisaLabel = $sisaHari . ' hari';
                } else {
                    $sisaLabel = 'Selesai';
                }
            }

            $rekap[] = [
                'user_id'         => $user->id,
                'nama'            => $user->name,
                'total_hadir'     => $totalHadir,
                'tepat_waktu'     => $tepatWaktu,
                'terlambat'       => $totalTerlambat,
                'cuti'            => $totalCuti,
                'tanpa_izin'      => $tanpaIzin,
                'hari_efektif'    => $hariEfektifUser,
                'sisa_hari'       => $sisaHari,
                'sisa_label'      => $sisaLabel,
            ];
        }

        // Urutkan: yang paling banyak hadir di atas
        usort($rekap, fn($a, $b) => $b['total_hadir'] <=> $a['total_hadir']);

        return [
            'data'               => $rekap,
            'total_hari_efektif' => $totalHariEfektifBulan,
            'holidays'           => $holidays,
        ];
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

