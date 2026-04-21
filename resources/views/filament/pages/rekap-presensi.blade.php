<x-filament-panels::page>
    <div class="space-y-4">

        {{-- ====== FILTER BULAN ====== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content-ctn">
                <div class="fi-section-content px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-gray-950 dark:text-white">Filter Bulan:</span>
                        <select
                            wire:model.live="selectedMonth"
                            class="fi-select-input block w-48 rounded-lg border-0 bg-white py-1.5 pl-3 pr-8 text-gray-950 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-500 dark:bg-white/5 dark:text-white dark:ring-white/20 sm:text-sm"
                        >
                            @foreach ($this->getMonthOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== KOMPUTASI DATA ====== --}}
        @php
            $result     = $this->getRekapData();
            $rekap      = $result['data'];
            $totalHariEfektif = $result['total_hari_efektif'];
            $holidays   = $result['holidays'];

            [$filterYear, $filterMonth] = explode('-', $selectedMonth);
            $holidaysThisMonth = collect($holidays)->filter(
                fn($date) => str_starts_with($date, "{$filterYear}-{$filterMonth}")
            );
        @endphp

        {{-- ====== RINGKASAN BULAN ====== --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content-ctn">
                <div class="fi-section-content px-6 py-4">
                    <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Hari Kerja Efektif:</span>
                            <span class="text-sm font-bold text-gray-950 dark:text-white">{{ $totalHariEfektif }} hari</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Libur Nasional:</span>
                            <span class="text-sm font-bold text-danger-600 dark:text-danger-400">{{ $holidaysThisMonth->count() }} hari</span>
                        </div>

                        @if ($holidaysThisMonth->count() > 0)
                            @php
                                $fullHolidays = $this->getHolidaysFull((int) $filterYear);
                                $holidayNames = collect($fullHolidays)->keyBy('date');
                            @endphp
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($holidaysThisMonth as $hDate)
                                    <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-danger bg-danger-50 text-danger-600 ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30">
                                        {{ \Carbon\Carbon::parse($hDate)->translatedFormat('d M') }} — {{ $holidayNames[$hDate]['name'] ?? '' }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== TABEL PESERTA ====== --}}
        @foreach(['aktif' => 'Peserta Aktif', 'selesai' => 'Peserta Selesai'] as $key => $title)
            @if (count($rekap[$key]) > 0)
                <div class="fi-ta-ctn overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

                    {{-- Table Header Bar --}}
                    <div class="fi-ta-header-ctn flex items-center justify-between gap-x-4 px-4 py-3 sm:px-6">
                        <div class="flex items-center gap-2">
                            <h3 class="fi-ta-header-heading text-base font-semibold text-gray-950 dark:text-white">
                                {{ $title }}
                            </h3>
                            <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1
                                {{ $key === 'aktif'
                                    ? 'fi-color-primary bg-primary-50 text-primary-600 ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30'
                                    : 'fi-color-gray bg-gray-100 text-gray-600 ring-gray-600/10 dark:bg-white/5 dark:text-gray-400 dark:ring-white/10' }}">
                                {{ count($rekap[$key]) }} orang
                            </span>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="fi-ta overflow-x-auto">
                        <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-white/10 text-sm">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-sm font-semibold text-gray-950 dark:text-white text-left w-[200px]">
                                        Nama
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-sm font-semibold text-gray-950 dark:text-white text-center">
                                        Hari Efektif
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-sm font-semibold text-gray-950 dark:text-white text-center">
                                        Total Hadir
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-sm font-semibold text-gray-950 dark:text-white text-center">
                                        Tepat Waktu
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-sm font-semibold text-gray-950 dark:text-white text-center">
                                        Terlambat
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-sm font-semibold text-gray-950 dark:text-white text-center">
                                        Cuti
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-sm font-semibold text-gray-950 dark:text-white text-center">
                                        Tanpa Izin
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-sm font-semibold text-gray-950 dark:text-white text-center">
                                        Sisa Magang
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="fi-ta-body divide-y divide-gray-200 dark:divide-white/10 whitespace-nowrap"
                                   x-data="{ expandedRow: null }">
                                @foreach ($rekap[$key] as $row)
                                    {{-- Data Row --}}
                                    <tr wire:key="row-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                        class="fi-ta-row cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                        @click="expandedRow = expandedRow === {{ $row['user_id'] }} ? null : {{ $row['user_id'] }}">

                                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] leading-none text-gray-400 transition-transform duration-200 inline-block"
                                                      x-bind:style="expandedRow === {{ $row['user_id'] }} ? '' : 'transform:rotate(-90deg)'">▼</span>
                                                <span class="font-medium">{{ $row['nama'] }}</span>
                                            </div>
                                        </td>

                                        <td class="fi-ta-cell px-3 py-4 text-sm text-center">
                                            <span class="font-semibold text-gray-950 dark:text-white">{{ $row['hari_efektif'] }}</span>
                                        </td>

                                        <td class="fi-ta-cell px-3 py-4 text-sm text-center">
                                            @if ($row['total_hadir'] > 0)
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-primary bg-primary-50 text-primary-600 ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                                    {{ $row['total_hadir'] }}x
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @endif
                                        </td>

                                        <td class="fi-ta-cell px-3 py-4 text-sm text-center">
                                            @if ($row['tepat_waktu'] > 0)
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-success bg-success-50 text-success-600 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                                    {{ $row['tepat_waktu'] }}x
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @endif
                                        </td>

                                        <td class="fi-ta-cell px-3 py-4 text-sm text-center">
                                            @if ($row['terlambat'] > 0)
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-danger bg-danger-50 text-danger-600 ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30">
                                                    {{ $row['terlambat'] }}x
                                                </span>
                                            @else
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-success bg-success-50 text-success-600 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                                    Tepat Waktu
                                                </span>
                                            @endif
                                        </td>

                                        <td class="fi-ta-cell px-3 py-4 text-sm text-center">
                                            @if ($row['cuti'] > 0)
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-info bg-info-50 text-info-600 ring-info-600/10 dark:bg-info-400/10 dark:text-info-400 dark:ring-info-400/30">
                                                    {{ $row['cuti'] }}x
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @endif
                                        </td>

                                        <td class="fi-ta-cell px-3 py-4 text-sm text-center">
                                            @if ($row['tanpa_izin'] > 0)
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-warning bg-warning-50 text-warning-600 ring-warning-600/10 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30">
                                                    {{ $row['tanpa_izin'] }}x
                                                </span>
                                            @else
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-success bg-success-50 text-success-600 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                                    Hadir Penuh
                                                </span>
                                            @endif
                                        </td>

                                        <td class="fi-ta-cell px-3 py-4 text-sm text-center">
                                            @if ($row['sisa_label'] === 'Selesai')
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 fi-color-gray bg-gray-100 text-gray-600 ring-gray-600/10 dark:bg-white/5 dark:text-gray-400 dark:ring-white/10">
                                                    Selesai
                                                </span>
                                            @elseif ($row['sisa_label'] === '-')
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @else
                                                @php
                                                    $sisa = $row['sisa_hari'];
                                                    if ($sisa <= 7) {
                                                        $bc = 'bg-danger-50 text-danger-600 ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30';
                                                    } elseif ($sisa <= 14) {
                                                        $bc = 'bg-warning-50 text-warning-600 ring-warning-600/10 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30';
                                                    } else {
                                                        $bc = 'bg-info-50 text-info-600 ring-info-600/10 dark:bg-info-400/10 dark:text-info-400 dark:ring-info-400/30';
                                                    }
                                                @endphp
                                                <span class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-h-6 py-1 {{ $bc }}">
                                                    {{ $row['sisa_label'] }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Chart Row --}}
                                    <tr wire:key="chart-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                        x-show="expandedRow === {{ $row['user_id'] }}" x-cloak>
                                        <td colspan="8" class="p-0 bg-gray-50/50 dark:bg-white/[0.01]">
                                            <div x-show="expandedRow === {{ $row['user_id'] }}" x-collapse>
                                                <div class="px-6 py-6 border-t border-gray-200 dark:border-white/10">
                                                    <div class="w-full max-w-lg mx-auto h-48"
                                                        x-data="miniChart(
                                                            {{ $row['total_hadir'] }},
                                                            {{ $row['tepat_waktu'] }},
                                                            {{ $row['terlambat'] }},
                                                            {{ $row['cuti'] }},
                                                            {{ $row['tanpa_izin'] }}
                                                        )"
                                                        x-init="$watch('expandedRow', val => {
                                                            if (val === {{ $row['user_id'] }}) { setTimeout(() => render(), 100); }
                                                        })"
                                                    >
                                                        @if ($row['total_hadir'] > 0 || $row['tanpa_izin'] > 0 || $row['cuti'] > 0)
                                                            <canvas x-ref="canvas"></canvas>
                                                        @else
                                                            <div class="flex items-center justify-center h-full border-2 border-dashed border-gray-200 dark:border-white/10 rounded-xl">
                                                                <p class="text-sm text-gray-400 dark:text-gray-600">Belum ada data kehadiran di bulan ini.</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @endif
        @endforeach

        {{-- Empty State --}}
        @if (count($rekap['aktif']) === 0 && count($rekap['selesai']) === 0)
            <div class="fi-ta-ctn overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="px-6 py-16 text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada data peserta Magang BPS / Alumni untuk periode ini.</p>
                </div>
            </div>
        @endif

    </div>

    {{-- ====== CHART.JS ====== --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('miniChart', (hadir, tepatWaktu, terlambat, cuti, tanpaIzin) => ({
                chartInstance: null,
                render() {
                    const canvas = this.$refs.canvas;
                    if (!canvas) return;
                    if (this.chartInstance) this.chartInstance.destroy();

                    const isDark = document.documentElement.classList.contains('dark');
                    const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
                    const labelColor = isDark ? '#6b7280' : '#9ca3af';

                    this.chartInstance = new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: ['Total Hadir', 'Tepat Waktu', 'Terlambat', 'Cuti', 'Tanpa Izin'],
                            datasets: [{
                                data: [hadir, tepatWaktu, terlambat, cuti, tanpaIzin],
                                backgroundColor: ['#6366f1','#22c55e','#ef4444','#0ea5e9','#f59e0b'],
                                borderRadius: 4,
                                barThickness: 20,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: isDark ? '#1f2937' : '#fff',
                                    titleColor: isDark ? '#f9fafb' : '#111827',
                                    bodyColor: isDark ? '#9ca3af' : '#6b7280',
                                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                                    borderWidth: 1,
                                    padding: 10,
                                },
                            },
                            scales: {
                                x: {
                                    ticks: { color: labelColor, font: { size: 11 } },
                                    grid: { display: false },
                                    border: { display: false },
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: { color: labelColor, stepSize: 1, font: { size: 11 } },
                                    grid: { color: gridColor },
                                    border: { display: false },
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
