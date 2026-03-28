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
        @php $rekap = $this->getRekapData(); @endphp
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
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Total Hadir</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Tepat Waktu</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Terlambat</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 text-center">Sisa Magang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse ($rekap as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ $row['nama'] }}
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
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-400 dark:text-gray-600 text-sm">
                                        Tidak ada data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- ====== CHART ====== --}}
        @php $chartData = $this->getChartData(); @endphp
        @if (count($chartData['labels']) > 0)
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-chart-bar class="h-5 w-5 text-primary-500" />
                    Diagram Kehadiran — {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Hanya menampilkan peserta yang memiliki data kehadiran di bulan ini.</p>
            </div>
            <div class="fi-section-content px-6 py-5">
                <canvas id="rekapChart" height="90"></canvas>
            </div>
        </div>
        @else
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                <x-heroicon-o-chart-bar class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600 mb-2" />
                <p class="text-sm">Belum ada data kehadiran di bulan ini untuk ditampilkan dalam diagram.</p>
            </div>
        </div>
        @endif

    </div>

    {{-- ====== SCRIPT CHART.JS ====== --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        let rekapChart = null;

        function isDarkMode() {
            return document.documentElement.classList.contains('dark');
        }

        function renderChart(chartData) {
            const canvas = document.getElementById('rekapChart');
            if (!canvas) return;

            const isDark = isDarkMode();
            const gridColor  = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
            const labelColor = isDark ? '#9ca3af' : '#6b7280';

            if (rekapChart) rekapChart.destroy();

            rekapChart = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Total Hadir',
                            data: chartData.hadir,
                            backgroundColor: 'rgba(99, 102, 241, 0.75)',
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                        },
                        {
                            label: 'Tepat Waktu',
                            data: chartData.tepatWaktu,
                            backgroundColor: 'rgba(34, 197, 94, 0.75)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                        },
                        {
                            label: 'Terlambat',
                            data: chartData.terlambat,
                            backgroundColor: 'rgba(239, 68, 68, 0.75)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: labelColor,
                                font: { size: 12 },
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                            },
                        },
                        tooltip: {
                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                            borderWidth: 1,
                        },
                    },
                    scales: {
                        x: {
                            ticks: { color: labelColor, font: { size: 11 } },
                            grid:  { color: gridColor },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: labelColor,
                                stepSize: 1,
                                precision: 0,
                                font: { size: 11 },
                            },
                            grid: { color: gridColor },
                        },
                    },
                },
            });
        }

        // Initial render setelah DOM siap
        document.addEventListener('DOMContentLoaded', function () {
            renderChart(@json($chartData));
        });

        // Re-render setelah Livewire update (bulan berubah → Livewire kirim event)
        document.addEventListener('rekap-chart-updated', function (e) {
            setTimeout(() => renderChart(e.detail), 100);
        });
    </script>
    @endpush

</x-filament-panels::page>
