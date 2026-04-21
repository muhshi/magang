<x-filament-panels::page>
    <div class="space-y-4">

        {{-- ====== FILTER BULAN ====== --}}
        <x-filament::section>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-950 dark:text-white">Filter Bulan:</span>
                <select
                    wire:model.live="selectedMonth"
                    class="block w-48 rounded-lg border-0 bg-white py-1.5 pl-3 pr-8 text-sm text-gray-950 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-500 dark:bg-white/5 dark:text-white dark:ring-white/20"
                >
                    @foreach ($this->getMonthOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </x-filament::section>

        {{-- ====== KOMPUTASI DATA ====== --}}
        @php
            $result           = $this->getRekapData();
            $rekap            = $result['data'];
            $totalHariEfektif = $result['total_hari_efektif'];
            $holidays         = $result['holidays'];

            [$filterYear, $filterMonth] = explode('-', $selectedMonth);
            $holidaysThisMonth = collect($holidays)
                ->filter(fn($d) => str_starts_with($d, "{$filterYear}-{$filterMonth}"));
        @endphp

        {{-- ====== RINGKASAN BULAN ====== --}}
        <x-filament::section>
            <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-gray-600 dark:text-gray-400">Hari Kerja Efektif:</span>
                    <span class="font-bold text-gray-950 dark:text-white">{{ $totalHariEfektif }} hari</span>
                </div>

                <span class="hidden sm:block h-4 w-px bg-gray-200 dark:bg-white/10"></span>

                <div class="flex items-center gap-2">
                    <span class="text-gray-600 dark:text-gray-400">Libur Nasional:</span>
                    <span class="font-bold text-danger-600 dark:text-danger-400">{{ $holidaysThisMonth->count() }} hari</span>
                </div>

                @if ($holidaysThisMonth->count() > 0)
                    @php
                        $fullHolidays = $this->getHolidaysFull((int) $filterYear);
                        $holidayNames = collect($fullHolidays)->keyBy('date');
                    @endphp
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($holidaysThisMonth as $hDate)
                            <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30">
                                {{ \Carbon\Carbon::parse($hDate)->translatedFormat('d M') }} — {{ $holidayNames[$hDate]['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- ====== TABEL PER GRUP ====== --}}
        @foreach (['aktif' => 'Peserta Aktif', 'selesai' => 'Peserta Selesai'] as $key => $title)
            @if (count($rekap[$key]) > 0)

                {{-- Table Card --}}
                <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

                    {{-- Card heading --}}
                    <div class="flex items-center gap-2 border-b border-gray-200 px-4 py-3 dark:border-white/10 sm:px-6">
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">{{ $title }}</h3>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset
                            {{ $key === 'aktif'
                                ? 'bg-primary-50 text-primary-700 ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30'
                                : 'bg-gray-100 text-gray-600 ring-gray-500/10 dark:bg-white/5 dark:text-gray-400 dark:ring-white/10' }}">
                            {{ count($rekap[$key]) }} orang
                        </span>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="py-3.5 pl-6 pr-3 text-left font-semibold text-gray-950 dark:text-white">Nama</th>
                                    <th class="px-3 py-3.5 text-center font-semibold text-gray-950 dark:text-white">Hari Efektif</th>
                                    <th class="px-3 py-3.5 text-center font-semibold text-gray-950 dark:text-white">Total Hadir</th>
                                    <th class="px-3 py-3.5 text-center font-semibold text-gray-950 dark:text-white">Tepat Waktu</th>
                                    <th class="px-3 py-3.5 text-center font-semibold text-gray-950 dark:text-white">Terlambat</th>
                                    <th class="px-3 py-3.5 text-center font-semibold text-gray-950 dark:text-white">Cuti</th>
                                    <th class="px-3 py-3.5 text-center font-semibold text-gray-950 dark:text-white">Tanpa Izin</th>
                                    <th class="py-3.5 pl-3 pr-6 text-center font-semibold text-gray-950 dark:text-white">Sisa Magang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900"
                                   x-data="{ expandedRow: null }">
                                @foreach ($rekap[$key] as $row)
                                    {{-- Data row --}}
                                    <tr wire:key="row-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                        class="cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-white/[0.025]"
                                        @click="expandedRow = expandedRow === {{ $row['user_id'] }} ? null : {{ $row['user_id'] }}">

                                        <td class="py-4 pl-6 pr-3 font-medium text-gray-950 dark:text-white">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-block text-[10px] leading-none text-gray-400 transition-transform duration-200"
                                                      x-bind:class="expandedRow === {{ $row['user_id'] }} ? '' : '-rotate-90'">▼</span>
                                                {{ $row['nama'] }}
                                            </div>
                                        </td>

                                        <td class="px-3 py-4 text-center text-gray-600 dark:text-gray-400">
                                            {{ $row['hari_efektif'] }}
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['total_hadir'] > 0)
                                                <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                                    {{ $row['total_hadir'] }}x
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['tepat_waktu'] > 0)
                                                <span class="inline-flex items-center rounded-md bg-success-50 px-2 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                                    {{ $row['tepat_waktu'] }}x
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['terlambat'] > 0)
                                                <span class="inline-flex items-center rounded-md bg-danger-50 px-2 py-1 text-xs font-medium text-danger-700 ring-1 ring-inset ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30">
                                                    {{ $row['terlambat'] }}x
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-success-50 px-2 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                                    Tepat Waktu
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['cuti'] > 0)
                                                <span class="inline-flex items-center rounded-md bg-info-50 px-2 py-1 text-xs font-medium text-info-700 ring-1 ring-inset ring-info-600/10 dark:bg-info-400/10 dark:text-info-400 dark:ring-info-400/30">
                                                    {{ $row['cuti'] }}x
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['tanpa_izin'] > 0)
                                                <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-700 ring-1 ring-inset ring-warning-600/10 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30">
                                                    {{ $row['tanpa_izin'] }}x
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-success-50 px-2 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                                    Hadir Penuh
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-4 pl-3 pr-6 text-center">
                                            @if ($row['sisa_label'] === 'Selesai')
                                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-white/5 dark:text-gray-400 dark:ring-white/10">
                                                    Selesai
                                                </span>
                                            @elseif ($row['sisa_label'] === '-')
                                                <span class="text-gray-400 dark:text-gray-600">—</span>
                                            @else
                                                @php
                                                    $sisa = $row['sisa_hari'];
                                                    if ($sisa <= 7) {
                                                        $bc = 'bg-danger-50 text-danger-700 ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/30';
                                                    } elseif ($sisa <= 14) {
                                                        $bc = 'bg-warning-50 text-warning-700 ring-warning-600/10 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30';
                                                    } else {
                                                        $bc = 'bg-info-50 text-info-700 ring-info-600/10 dark:bg-info-400/10 dark:text-info-400 dark:ring-info-400/30';
                                                    }
                                                @endphp
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $bc }}">
                                                    {{ $row['sisa_label'] }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Chart row (expandable) --}}
                                    <tr wire:key="chart-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                        x-show="expandedRow === {{ $row['user_id'] }}"
                                        x-cloak
                                        style="display:none">
                                        <td colspan="8" class="bg-gray-50/50 dark:bg-white/[0.01]">
                                            <div x-show="expandedRow === {{ $row['user_id'] }}" x-collapse>
                                                <div class="border-t border-gray-200 px-6 py-6 dark:border-white/10">
                                                    <div class="mx-auto h-48 w-full max-w-lg"
                                                         x-data="{
                                                             chartInstance: null,
                                                             render() {
                                                                 const canvas = this.$refs.canvas;
                                                                 if (!canvas || typeof Chart === 'undefined') return;
                                                                 if (this.chartInstance) this.chartInstance.destroy();
                                                                 const isDark = document.documentElement.classList.contains('dark');
                                                                 const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
                                                                 const lblColor  = isDark ? '#6b7280' : '#9ca3af';
                                                                 this.chartInstance = new Chart(canvas, {
                                                                     type: 'bar',
                                                                     data: {
                                                                         labels: ['Total Hadir','Tepat Waktu','Terlambat','Cuti','Tanpa Izin'],
                                                                         datasets: [{ data: [{{ $row['total_hadir'] }},{{ $row['tepat_waktu'] }},{{ $row['terlambat'] }},{{ $row['cuti'] }},{{ $row['tanpa_izin'] }}], backgroundColor: ['#6366f1','#22c55e','#ef4444','#0ea5e9','#f59e0b'], borderRadius: 4, barThickness: 22 }]
                                                                     },
                                                                     options: {
                                                                         responsive: true, maintainAspectRatio: false,
                                                                         plugins: { legend: { display: false }, tooltip: { backgroundColor: isDark ? '#1f2937' : '#fff', titleColor: isDark ? '#f9fafb' : '#111827', bodyColor: isDark ? '#9ca3af' : '#6b7280', borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)', borderWidth: 1, padding: 10 } },
                                                                         scales: { x: { ticks: { color: lblColor, font: { size: 11 } }, grid: { display: false }, border: { display: false } }, y: { beginAtZero: true, ticks: { color: lblColor, stepSize: 1, font: { size: 11 } }, grid: { color: gridColor }, border: { display: false } } }
                                                                     }
                                                                 });
                                                             }
                                                         }"
                                                         x-init="$watch('expandedRow', val => { if (val === {{ $row['user_id'] }}) { $nextTick(() => render()); } })">
                                                        @if ($row['total_hadir'] > 0 || $row['tanpa_izin'] > 0 || $row['cuti'] > 0)
                                                            <canvas x-ref="canvas"></canvas>
                                                        @else
                                                            <div class="flex h-full items-center justify-center rounded-xl border-2 border-dashed border-gray-200 dark:border-white/10">
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

        {{-- Empty state --}}
        @if (count($rekap['aktif']) === 0 && count($rekap['selesai']) === 0)
            <div class="overflow-hidden rounded-xl bg-white px-6 py-16 text-center shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Tidak ada data peserta Magang BPS / Alumni untuk periode ini.
                </p>
            </div>
        @endif

    </div>

    {{-- Chart.js CDN — loaded before Alpine processes x-data so Chart is available --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</x-filament-panels::page>
