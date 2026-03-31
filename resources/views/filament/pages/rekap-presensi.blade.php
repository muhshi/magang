<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ====== FILTER BULAN ====== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content px-6 py-5">
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Filter Bulan:
                    </label>
                    <select
                        wire:model.live="selectedMonth"
                        class="fi-select-input block w-56 rounded-lg border-0 bg-white py-1.5 pl-3 pr-8 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-500 dark:bg-gray-800 dark:text-white dark:ring-white/20 sm:text-sm"
                    >
                        @foreach ($this->getMonthOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ====== TABEL REKAPITULASI ====== --}}
        @php
            $result = $this->getRekapData();
            $rekap = $result['data'];
            $totalHariEfektif = $result['total_hari_efektif'];
            $holidays = $result['holidays'];

            // Hitung jumlah hari libur yang jatuh di bulan ini
            [$filterYear, $filterMonth] = explode('-', $selectedMonth);
            $holidaysThisMonth = collect($holidays)->filter(function ($date) use ($filterYear, $filterMonth) {
                return str_starts_with($date, "{$filterYear}-{$filterMonth}");
            });
        @endphp

        {{-- ====== INFO RINGKASAN BULAN ====== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content px-6 py-4">
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-calendar-days class="h-5 w-5 text-primary-500" />
                        <span class="text-gray-600 dark:text-gray-400">Hari Kerja Efektif:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $totalHariEfektif }} hari</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-flag class="h-5 w-5 text-danger-500" />
                        <span class="text-gray-600 dark:text-gray-400">Hari Libur Nasional:</span>
                        <span class="font-semibold text-danger-600 dark:text-danger-400">{{ $holidaysThisMonth->count() }} hari</span>
                    </div>
                    @if ($holidaysThisMonth->count() > 0)
                        <div class="basis-full mt-1 pl-7">
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $fullHolidays = $this->getHolidaysFull((int) $filterYear);
                                    $holidayNames = collect($fullHolidays)->keyBy('date');
                                @endphp
                                @foreach ($holidaysThisMonth as $hDate)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-danger-50 dark:bg-danger-950 px-2 py-0.5 text-xs font-medium text-danger-700 dark:text-danger-300"
                                          title="{{ $holidayNames[$hDate]['name'] ?? '' }}">
                                        {{ \Carbon\Carbon::parse($hDate)->translatedFormat('d M') }} — {{ $holidayNames[$hDate]['name'] ?? '' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ====== TABEL PESERTA ====== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-table-cells class="h-5 w-5 text-primary-500" />
                    Rekap Per Peserta — {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }}
                </h3>
            </div>
            <div class="fi-section-content overflow-x-auto">
                @if (count($rekap) === 0)
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-inbox class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600 mb-2" />
                        <p class="text-sm">Tidak ada data peserta Magang BPS / Alumni.</p>
                    </div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Hari Efektif</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Total Hadir</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Tepat Waktu</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Terlambat</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Tidak Hadir</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Sisa Magang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5" x-data="{ expandedRow: null }">
                            @forelse ($rekap as $row)
                                <tr wire:key="row-{{ $row['user_id'] }}-{{ $selectedMonth }}" 
                                    class="hover:bg-gray-50 dark:hover:bg-white/5 transition cursor-pointer" 
                                    @click="expandedRow = expandedRow === {{ $row['user_id'] }} ? null : {{ $row['user_id'] }}">
                                    
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                        <x-heroicon-o-chevron-down 
                                            class="h-4 w-4 text-gray-400 transition-transform duration-200" 
                                            x-bind:class="{ '-rotate-90': expandedRow !== {{ $row['user_id'] }} }" 
                                        />
                                        {{ $row['nama'] }}
                                    </td>

                                    {{-- Hari Efektif --}}
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-sm font-semibold text-gray-600 dark:text-gray-300">
                                            {{ $row['hari_efektif'] }}
                                        </span>
                                    </td>

                                    {{-- Total Hadir --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($row['total_hadir'] > 0)
                                            <span class="inline-flex items-center justify-center rounded-full bg-primary-50 dark:bg-primary-950 px-2.5 py-0.5 text-sm font-semibold text-primary-700 dark:text-primary-300">
                                                {{ $row['total_hadir'] }}x
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-600">–</span>
                                        @endif
                                    </td>

                                    {{-- Tepat Waktu --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($row['tepat_waktu'] > 0)
                                            <span class="inline-flex items-center justify-center rounded-full bg-success-50 dark:bg-success-950 px-2.5 py-0.5 text-sm font-semibold text-success-700 dark:text-success-300">
                                                {{ $row['tepat_waktu'] }}x
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-600">–</span>
                                        @endif
                                    </td>

                                    {{-- Terlambat --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($row['terlambat'] > 0)
                                            <span class="inline-flex items-center justify-center rounded-full bg-danger-50 dark:bg-danger-950 px-2.5 py-0.5 text-sm font-semibold text-danger-700 dark:text-danger-300">
                                                {{ $row['terlambat'] }}x
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center rounded-full bg-success-50 dark:bg-success-950 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:text-success-300">
                                                Tidak pernah
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Tidak Hadir --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($row['tidak_hadir'] > 0)
                                            <span class="inline-flex items-center justify-center rounded-full bg-warning-50 dark:bg-warning-950 px-2.5 py-0.5 text-sm font-semibold text-warning-700 dark:text-warning-300">
                                                {{ $row['tidak_hadir'] }}x
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center rounded-full bg-success-50 dark:bg-success-950 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:text-success-300">
                                                Full hadir
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Sisa Magang --}}
                                    <td class="px-4 py-3 text-center">
                                        @if ($row['sisa_label'] === 'Selesai')
                                            <span class="inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Selesai
                                            </span>
                                        @elseif ($row['sisa_label'] === '-')
                                            <span class="text-gray-400 dark:text-gray-600">–</span>
                                        @else
                                            @php
                                                $sisa = $row['sisa_hari'];
                                                $color = $sisa <= 7 ? 'danger' : ($sisa <= 14 ? 'warning' : 'info');
                                            @endphp
                                            <span class="inline-flex items-center justify-center rounded-full bg-{{ $color }}-50 dark:bg-{{ $color }}-950 px-2.5 py-0.5 text-sm font-semibold text-{{ $color }}-700 dark:text-{{ $color }}-300">
                                                {{ $row['sisa_label'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                
                                {{-- ====== DIAGRAM PER-ORANG ====== --}}
                                <tr wire:key="chart-{{ $row['user_id'] }}-{{ $selectedMonth }}" x-show="expandedRow === {{ $row['user_id'] }}" x-cloak>
                                    <td colspan="7" class="p-0 border-t border-gray-100 dark:border-white/5">
                                        <div x-show="expandedRow === {{ $row['user_id'] }}" x-collapse>
                                            <div class="px-6 py-6 bg-gray-50/50 dark:bg-white/[0.02]">
                                                <div class="w-full max-w-xl mx-auto h-56"
                                                    x-data="miniChart(
                                                        {{ $row['total_hadir'] }},
                                                        {{ $row['tepat_waktu'] }},
                                                        {{ $row['terlambat'] }},
                                                        {{ $row['tidak_hadir'] }}
                                                    )"
                                                    x-init="$watch('expandedRow', val => { if(val === {{ $row['user_id'] }}) { setTimeout(() => render(), 50); } })"
                                                >
                                                    @if($row['total_hadir'] > 0 || $row['tidak_hadir'] > 0)
                                                        <canvas x-ref="canvas"></canvas>
                                                    @else
                                                        <div class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                                            <x-heroicon-o-chart-bar class="h-8 w-8 mb-2 opacity-50" />
                                                            <span class="text-sm">Belum ada data kehadiran di bulan ini.</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-gray-400 dark:text-gray-600 text-sm">
                                        Tidak ada data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>

    {{-- ====== SCRIPT CHART.JS ALPINE ====== --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('miniChart', (hadir, tepatWaktu, terlambat, tidakHadir) => ({
                chartInstance: null,

                render() {
                    const canvas = this.$refs.canvas;
                    if (!canvas) return;
                    
                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }

                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor  = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
                    const labelColor = isDark ? '#9ca3af' : '#6b7280';

                    this.chartInstance = new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: ['Total Hadir', 'Tepat Waktu', 'Terlambat', 'Tidak Hadir'],
                            datasets: [{
                                data: [hadir, tepatWaktu, terlambat, tidakHadir],
                                backgroundColor: [
                                    'rgba(99, 102, 241, 0.75)', // Indigo
                                    'rgba(34, 197, 94, 0.75)',  // Green
                                    'rgba(239, 68, 68, 0.75)',  // Red
                                    'rgba(245, 158, 11, 0.75)'  // Amber
                                ],
                                borderColor: [
                                    'rgba(99, 102, 241, 1)',
                                    'rgba(34, 197, 94, 1)',
                                    'rgba(239, 68, 68, 1)',
                                    'rgba(245, 158, 11, 1)'
                                ],
                                borderWidth: 1,
                                borderRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                                    borderWidth: 1,
                                },
                            },
                            scales: {
                                x: {
                                    ticks: { color: labelColor, font: { size: 12 } },
                                    grid:  { display: false },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: { 
                                        color: labelColor, 
                                        stepSize: 1, 
                                        precision: 0 
                                    },
                                    grid:  { color: gridColor },
                                },
                            },
                        },
                    });
                }
            }));
        });
    </script>
    @endpush

</x-filament-panels::page>

