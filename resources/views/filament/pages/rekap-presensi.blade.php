<x-filament-panels::page>
    <div class="space-y-4">

        {{-- ====== FILTER BULAN ====== --}}
        <x-filament::section>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold">Filter Bulan:</span>
                <select
                    wire:model.live="selectedMonth"
                    class="block w-48 rounded-lg border border-gray-300 bg-white py-1.5 pl-3 pr-8 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
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
                <span>Hari Kerja Efektif: <strong>{{ $totalHariEfektif }} hari</strong></span>
                <span class="hidden h-4 w-px bg-gray-300 dark:bg-gray-600 sm:block"></span>
                <span>Libur Nasional: <strong class="text-danger-600 dark:text-danger-400">{{ $holidaysThisMonth->count() }} hari</strong></span>

                @if ($holidaysThisMonth->count() > 0)
                    @php
                        $fullHolidays = $this->getHolidaysFull((int) $filterYear);
                        $holidayNames = collect($fullHolidays)->keyBy('date');
                    @endphp
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($holidaysThisMonth as $hDate)
                            <x-filament::badge color="danger" size="sm">
                                {{ \Carbon\Carbon::parse($hDate)->translatedFormat('d M') }} — {{ $holidayNames[$hDate]['name'] ?? '' }}
                            </x-filament::badge>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- ====== TABEL PESERTA ====== --}}
        @foreach (['aktif' => 'Peserta Aktif', 'selesai' => 'Peserta Selesai'] as $key => $title)
            @if (count($rekap[$key]) > 0)
                <x-filament::section>
                    {{-- Section heading --}}
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            {{ $title }}
                            <x-filament::badge :color="$key === 'aktif' ? 'primary' : 'gray'" size="sm">
                                {{ count($rekap[$key]) }} orang
                            </x-filament::badge>
                        </div>
                    </x-slot>

                    {{-- Table (flush to section edges via -mx-6 and border-t) --}}
                    <div class="-mx-6 overflow-x-auto border-t border-gray-200 dark:border-white/10">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th scope="col" class="py-3 pl-6 pr-3 text-left font-semibold text-gray-700 dark:text-gray-300">Nama</th>
                                    <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Hari Efektif</th>
                                    <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Total Hadir</th>
                                    <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Tepat Waktu</th>
                                    <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Terlambat</th>
                                    <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Cuti</th>
                                    <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Tanpa Izin</th>
                                    <th scope="col" class="py-3 pl-3 pr-6 text-center font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Sisa Magang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white dark:divide-white/5 dark:bg-transparent"
                                   x-data="{ expandedRow: null }">
                                @foreach ($rekap[$key] as $row)
                                    {{-- Data row --}}
                                    <tr wire:key="row-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                        class="cursor-pointer transition-colors duration-100 hover:bg-gray-50 dark:hover:bg-white/[0.02]"
                                        @click="expandedRow = expandedRow === {{ $row['user_id'] }} ? null : {{ $row['user_id'] }}">

                                        <td class="py-4 pl-6 pr-3 font-medium text-gray-900 dark:text-white">
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
                                                <x-filament::badge color="primary" size="sm">{{ $row['total_hadir'] }}x</x-filament::badge>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['tepat_waktu'] > 0)
                                                <x-filament::badge color="success" size="sm">{{ $row['tepat_waktu'] }}x</x-filament::badge>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['terlambat'] > 0)
                                                <x-filament::badge color="danger" size="sm">{{ $row['terlambat'] }}x</x-filament::badge>
                                            @else
                                                <x-filament::badge color="success" size="sm">Tepat Waktu</x-filament::badge>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['cuti'] > 0)
                                                <x-filament::badge color="info" size="sm">{{ $row['cuti'] }}x</x-filament::badge>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-4 text-center">
                                            @if ($row['tanpa_izin'] > 0)
                                                <x-filament::badge color="warning" size="sm">{{ $row['tanpa_izin'] }}x</x-filament::badge>
                                            @else
                                                <x-filament::badge color="success" size="sm">Hadir Penuh</x-filament::badge>
                                            @endif
                                        </td>

                                        <td class="py-4 pl-3 pr-6 text-center">
                                            @if ($row['sisa_label'] === 'Selesai')
                                                <x-filament::badge color="gray" size="sm">Selesai</x-filament::badge>
                                            @elseif ($row['sisa_label'] === '-')
                                                <span class="text-gray-400">—</span>
                                            @else
                                                @php
                                                    $sisa = $row['sisa_hari'];
                                                    $bColor = $sisa <= 7 ? 'danger' : ($sisa <= 14 ? 'warning' : 'info');
                                                @endphp
                                                <x-filament::badge :color="$bColor" size="sm">{{ $row['sisa_label'] }}</x-filament::badge>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Chart row --}}
                                    <tr wire:key="chart-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                        x-show="expandedRow === {{ $row['user_id'] }}"
                                        x-cloak style="display:none">
                                        <td colspan="8" class="bg-gray-50/60 dark:bg-white/[0.01]">
                                            <div x-show="expandedRow === {{ $row['user_id'] }}" x-collapse>
                                                <div class="border-t border-gray-100 px-6 py-6 dark:border-white/5">
                                                    <div class="mx-auto h-48 w-full max-w-lg"
                                                         x-data="{
                                                             chartInstance: null,
                                                             render() {
                                                                 const cv = this.$refs.canvas;
                                                                 if (!cv || typeof Chart === 'undefined') return;
                                                                 if (this.chartInstance) this.chartInstance.destroy();
                                                                 const dk = document.documentElement.classList.contains('dark');
                                                                 this.chartInstance = new Chart(cv, {
                                                                     type: 'bar',
                                                                     data: {
                                                                         labels: ['Total Hadir','Tepat Waktu','Terlambat','Cuti','Tanpa Izin'],
                                                                         datasets: [{ data: [{{ $row['total_hadir'] }},{{ $row['tepat_waktu'] }},{{ $row['terlambat'] }},{{ $row['cuti'] }},{{ $row['tanpa_izin'] }}], backgroundColor: ['#6366f1','#22c55e','#ef4444','#0ea5e9','#f59e0b'], borderRadius: 4, barThickness: 22 }]
                                                                     },
                                                                     options: {
                                                                         responsive: true, maintainAspectRatio: false,
                                                                         plugins: { legend: { display: false }, tooltip: { backgroundColor: dk?'#1f2937':'#fff', borderColor: dk?'rgba(255,255,255,0.1)':'rgba(0,0,0,0.08)', borderWidth:1, padding:10 } },
                                                                         scales: {
                                                                             x: { ticks:{ color: dk?'#6b7280':'#9ca3af', font:{size:11} }, grid:{display:false}, border:{display:false} },
                                                                             y: { beginAtZero:true, ticks:{ color: dk?'#6b7280':'#9ca3af', stepSize:1, font:{size:11} }, grid:{ color: dk?'rgba(255,255,255,0.05)':'rgba(0,0,0,0.04)' }, border:{display:false} }
                                                                         }
                                                                     }
                                                                 });
                                                             }
                                                         }"
                                                         x-init="$watch('expandedRow', val => { if (val === {{ $row['user_id'] }}) $nextTick(() => render()); })">
                                                        @if ($row['total_hadir'] > 0 || $row['tanpa_izin'] > 0 || $row['cuti'] > 0)
                                                            <canvas x-ref="canvas"></canvas>
                                                        @else
                                                            <div class="flex h-full items-center justify-center rounded-xl border-2 border-dashed border-gray-200 dark:border-white/10">
                                                                <p class="text-sm text-gray-400">Belum ada data kehadiran di bulan ini.</p>
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
                </x-filament::section>
            @endif
        @endforeach

        {{-- Empty state --}}
        @if (count($rekap['aktif']) === 0 && count($rekap['selesai']) === 0)
            <x-filament::section>
                <div class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                    Tidak ada data peserta Magang BPS / Alumni untuk periode ini.
                </div>
            </x-filament::section>
        @endif

    </div>

    {{-- Chart.js — loaded inline so it's available before Alpine parses x-data --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</x-filament-panels::page>
