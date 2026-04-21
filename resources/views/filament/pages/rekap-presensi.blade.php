<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ====== FILTER BULAN ====== --}}
        <x-filament::section>
            <div class="flex items-center gap-4">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Filter Bulan:
                </label>
                <div class="w-64">
                    <x-filament::input.select wire:model.live="selectedMonth">
                        @foreach ($this->getMonthOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </div>
            </div>
        </x-filament::section>

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
        <x-filament::section>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="flex items-center gap-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Hari Kerja Efektif</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $totalHariEfektif }} Hari</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Libur Nasional</p>
                        <p class="text-xl font-bold text-danger-600 dark:text-danger-400">{{ $holidaysThisMonth->count() }} Hari</p>
                    </div>
                </div>
            </div>

            @if ($holidaysThisMonth->count() > 0)
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/10">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">Daftar Hari Libur:</p>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $fullHolidays = $this->getHolidaysFull((int) $filterYear);
                            $holidayNames = collect($fullHolidays)->keyBy('date');
                        @endphp
                        @foreach ($holidaysThisMonth as $hDate)
                            <x-filament::badge color="danger" size="sm">
                                {{ \Carbon\Carbon::parse($hDate)->translatedFormat('d M') }} — {{ $holidayNames[$hDate]['name'] ?? '' }}
                            </x-filament::badge>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-filament::section>

        {{-- ====== TABEL PESERTA ====== --}}
        <div class="space-y-8">
            @foreach(['aktif' => 'Peserta Aktif', 'selesai' => 'Peserta Selesai'] as $key => $title)
                @if (count($rekap[$key]) > 0)
                    <x-filament::section>
                        <x-slot name="heading">
                            <span>{{ $title }} — {{ \Carbon\Carbon::createFromFormat('Y-m-d', $selectedMonth . '-01')->translatedFormat('F Y') }}</span>
                        </x-slot>

                        <x-slot name="headerEnd">
                            <x-filament::badge :color="$key === 'aktif' ? 'primary' : 'gray'">
                                {{ count($rekap[$key]) }} orang
                            </x-filament::badge>
                        </x-slot>

                        <div class="overflow-x-auto -mx-6 mt-4">
                            <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-white/10">
                                <thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th class="px-6 py-3 font-semibold text-gray-900 dark:text-white">Nama</th>
                                        <th class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-center">Hari Efektif</th>
                                        <th class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-center">Total Hadir</th>
                                        <th class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-center">Tepat Waktu</th>
                                        <th class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-center">Terlambat</th>
                                        <th class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-center">Cuti</th>
                                        <th class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-center">Tanpa Izin</th>
                                        <th class="px-4 py-3 font-semibold text-gray-900 dark:text-white text-center">Sisa Magang</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5" x-data="{ expandedRow: null }">
                                    @foreach ($rekap[$key] as $row)
                                        <tr wire:key="row-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                            class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer"
                                            @click="expandedRow = expandedRow === {{ $row['user_id'] }} ? null : {{ $row['user_id'] }}">

                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-gray-400 text-xs transition-transform duration-200 inline-block"
                                                          x-bind:style="expandedRow === {{ $row['user_id'] }} ? 'transform:rotate(0deg)' : 'transform:rotate(-90deg)'">▼</span>
                                                    {{ $row['nama'] }}
                                                </div>
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                <x-filament::badge color="gray" size="sm">{{ $row['hari_efektif'] }}</x-filament::badge>
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                @if ($row['total_hadir'] > 0)
                                                    <x-filament::badge color="primary" size="sm">{{ $row['total_hadir'] }}x</x-filament::badge>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-600">–</span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                @if ($row['tepat_waktu'] > 0)
                                                    <x-filament::badge color="success" size="sm">{{ $row['tepat_waktu'] }}x</x-filament::badge>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-600">–</span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                @if ($row['terlambat'] > 0)
                                                    <x-filament::badge color="danger" size="sm">{{ $row['terlambat'] }}x</x-filament::badge>
                                                @else
                                                    <x-filament::badge color="success" size="sm">Tepat Waktu</x-filament::badge>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                @if ($row['cuti'] > 0)
                                                    <x-filament::badge color="info" size="sm">{{ $row['cuti'] }}x</x-filament::badge>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-600">–</span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                @if ($row['tanpa_izin'] > 0)
                                                    <x-filament::badge color="warning" size="sm">{{ $row['tanpa_izin'] }}x</x-filament::badge>
                                                @else
                                                    <x-filament::badge color="success" size="sm">Hadir Penuh</x-filament::badge>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-center">
                                                @if ($row['sisa_label'] === 'Selesai')
                                                    <x-filament::badge color="gray" size="sm">Selesai</x-filament::badge>
                                                @elseif ($row['sisa_label'] === '-')
                                                    <span class="text-gray-400 dark:text-gray-600">–</span>
                                                @else
                                                    @php
                                                        $sisa = $row['sisa_hari'];
                                                        $color = $sisa <= 7 ? 'danger' : ($sisa <= 14 ? 'warning' : 'info');
                                                    @endphp
                                                    <x-filament::badge :color="$color" size="sm">{{ $row['sisa_label'] }}</x-filament::badge>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- ====== DIAGRAM PER-ORANG ====== --}}
                                        <tr wire:key="chart-{{ $row['user_id'] }}-{{ $selectedMonth }}" x-show="expandedRow === {{ $row['user_id'] }}" x-cloak>
                                            <td colspan="8" class="p-0 border-t border-gray-100 dark:border-white/5">
                                                <div x-show="expandedRow === {{ $row['user_id'] }}" x-collapse>
                                                    <div class="px-6 py-6 bg-gray-50/50 dark:bg-white/[0.02]">
                                                        <div class="w-full max-w-xl mx-auto h-56"
                                                            x-data="miniChart(
                                                                {{ $row['total_hadir'] }},
                                                                {{ $row['tepat_waktu'] }},
                                                                {{ $row['terlambat'] }},
                                                                {{ $row['cuti'] }},
                                                                {{ $row['tanpa_izin'] }}
                                                            )"
                                                            x-init="$watch('expandedRow', val => { if(val === {{ $row['user_id'] }}) { setTimeout(() => render(), 50); } })"
                                                        >
                                                            @if($row['total_hadir'] > 0 || $row['tanpa_izin'] > 0 || $row['cuti'] > 0)
                                                                <canvas x-ref="canvas"></canvas>
                                                            @else
                                                                <div class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                                                    <span class="text-3xl mb-2">📊</span>
                                                                    <span class="text-sm">Belum ada data kehadiran di bulan ini.</span>
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

            @if (count($rekap['aktif']) === 0 && count($rekap['selesai']) === 0)
                <x-filament::section>
                    <div class="py-12 text-center text-gray-500 dark:text-gray-400">
                        <p class="text-3xl mb-3">📭</p>
                        <p class="text-sm">Tidak ada data peserta Magang BPS / Alumni.</p>
                    </div>
                </x-filament::section>
            @endif
        </div>

    </div>

    {{-- ====== SCRIPT CHART.JS ALPINE ====== --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('miniChart', (hadir, tepatWaktu, terlambat, cuti, tanpaIzin) => ({
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
                            labels: ['Total Hadir', 'Tepat Waktu', 'Terlambat', 'Cuti', 'Tanpa Izin'],
                            datasets: [{
                                data: [hadir, tepatWaktu, terlambat, cuti, tanpaIzin],
                                backgroundColor: [
                                    'rgba(99, 102, 241, 0.75)',
                                    'rgba(34, 197, 94, 0.75)',
                                    'rgba(239, 68, 68, 0.75)',
                                    'rgba(14, 165, 233, 0.75)',
                                    'rgba(245, 158, 11, 0.75)'
                                ],
                                borderColor: [
                                    'rgba(99, 102, 241, 1)',
                                    'rgba(34, 197, 94, 1)',
                                    'rgba(239, 68, 68, 1)',
                                    'rgba(14, 165, 233, 1)',
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
