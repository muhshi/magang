<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 2;

    /**
     * Tentukan fullcalendar hanya muncul sebulan penuh dan title di kiri
     */
    public function config(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'title',
                'center' => '',
                'right' => 'prev,next today',
            ],
            'themeSystem' => 'standard',
            'height' => 550,
        ];
    }

    /**
     * Method ini otomatis mengambil event dan me-rendernya di kalender
     */
    public function fetchEvents(array $fetchInfo): array
    {
        $start = Carbon::parse($fetchInfo['start']);
        $end = Carbon::parse($fetchInfo['end']);
        $events = [];

        // 1. Ambil data Libur Nasional dari API
        $yearsToFetch = range($start->year, $end->year);
        $holidays = [];

        foreach ($yearsToFetch as $year) {
            $yearHolidays = Cache::remember("holidays_{$year}_full", now()->addDays(30), function () use ($year) {
                try {
                    $response = Http::timeout(10)->get("https://libur.deno.dev/api", ['year' => $year]);
                    if ($response->successful()) {
                        return $response->json();
                    }
                } catch (\Exception $e) {
                    Log::warning("CalendarWidget gagal mengambil data libur tahun {$year}: {$e->getMessage()}");
                }
                return [];
            });

            // Ganti key date ke dalam array
            foreach ($yearHolidays as $holiday) {
                if (isset($holiday['date'])) {
                    $dateObj = Carbon::parse($holiday['date']);
                    if ($dateObj->between($start, $end)) {
                        $events[] = EventData::make()
                            ->id('libur_' . $holiday['date'])
                            ->title($holiday['name'] ?? 'Libur Nasional')
                            ->start($dateObj)
                            ->end($dateObj->copy()->endOfDay())
                            ->backgroundColor('rgb(220, 38, 38)') // tailwind danger-600
                            ->borderColor('rgb(220, 38, 38)')
                            ->textColor('white')
                            ->toArray();
                    }
                }
            }
        }

        // 2. Ambil data Cuti (Leave) yang disetujui
        $leaves = Leave::with('user')
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<', $start)
                          ->where('end_date', '>', $end);
                    });
            })
            ->get();

        foreach ($leaves as $leave) {
            $events[] = EventData::make()
                ->id('cuti_' . $leave->id)
                ->title('Cuti: ' . ($leave->user->name ?? 'User'))
                ->start(Carbon::parse($leave->start_date)->startOfDay())
                ->end(Carbon::parse($leave->end_date)->endOfDay())
                ->backgroundColor('rgb(2, 132, 199)') // tailwind sky-600 (biru)
                ->borderColor('rgb(2, 132, 199)')
                ->textColor('white')
                ->toArray();
        }

        return $events;
    }
}
