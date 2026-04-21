<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ====== FILTER BULAN ====== --}}
        <x-filament::section>
            <div class="flex flex-wrap items-center gap-4">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter Bulan:</label>
                <div class="w-64">
                    <x-filament::input.select wire:model.live="selectedMonth">
                        @foreach ($this->getMonthOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </div>
            </div>
        </x-filament::section>

        {{-- ====== DATA KOMPUTASI ====== --}}
        @php
            $result = $this->getRekapData();
            $rekap = $result['data'];
            $totalHariEfektif = $result['total_hari_efektif'];
            $holidays = $result['holidays'];

            [$filterYear, $filterMonth] = explode('-', $selectedMonth);
            $holidaysThisMonth = collect($holidays)->filter(function ($date) use ($filterYear, $filterMonth) {
                return str_starts_with($date, "{$filterYear}-{$filterMonth}");
            });
        @endphp

        {{-- ====== INFO RINGKASAN BULAN ====== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Card: Hari Efektif --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-950 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hari Kerja Efektif</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalHariEfektif }} <span class="text-base font-normal text-gray-500">hari</span></p>
                </div>
            </div>

            {{-- Card: Libur Nasional --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-danger-50 dark:bg-danger-950 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Libur Nasional</p>
                    <p class="text-2xl font-bold text-danger-600 dark:text-danger-400">{{ $holidaysThisMonth->count() }} <span class="text-base font-normal text-gray-500">hari</span></p>
                    @if ($holidaysThisMonth->count() > 0)
                        @php
                            $fullHolidays = $this->getHolidaysFull((int) $filterYear);
                            $holidayNames = collect($fullHolidays)->keyBy('date');
                        @endphp
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach ($holidaysThisMonth as $hDate)
                                <span class="inline-flex items-center rounded-md bg-danger-50 dark:bg-danger-900/30 px-2 py-0.5 text-xs font-medium text-danger-700 dark:text-danger-300 ring-1 ring-inset ring-danger-200 dark:ring-danger-700">
                                    {{ \Carbon\Carbon::parse($hDate)->translatedFormat('d M') }} — {{ $holidayNames[$hDate]['name'] ?? '' }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ====== TABEL PESERTA ====== --}}
        <div class="space-y-6">
            @foreach(['aktif' => ['label' => 'Peserta Aktif', 'color' => 'primary'], 'selesai' => ['label' => 'Peserta Selesai', 'color' => 'gray']] as $key => $meta)
                @if (count($rekap[$key]) > 0)
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-sm overflow-hidden">
                        {{-- Table Header --}}
                        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-white/10">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $meta['label'] }} — {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F Y') }}
                            </h3>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $key === 'aktif' ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 ring-1 ring-primary-200 dark:ring-primary-700' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 ring-1 ring-gray-200 dark:ring-white/10' }}">
                                {{ count($rekap[$key]) }} orang
                            </span>
                        </div>

                        {{-- Tabel --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10 text-sm">
                                <thead class="bg-gray-50 dark:bg-white/[0.03]">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hari Efektif</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Hadir</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tepat Waktu</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terlambat</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cuti</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanpa Izin</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sisa Magang</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-white/5">
                                    @foreach ($rekap[$key] as $row)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors duration-150">
                                            {{-- Nama --}}
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ $row['nama'] }}
                                            </td>

                                            {{-- Hari Efektif --}}
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-white/5 px-2 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-200 dark:ring-white/10">
                                                    {{ $row['hari_efektif'] }}
                                                </span>
                                            </td>

                                            {{-- Total Hadir --}}
                                            <td class="px-4 py-3 text-center">
                                                @if ($row['total_hadir'] > 0)
                                                    <span class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-900/30 px-2 py-0.5 text-xs font-medium text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-200 dark:ring-primary-700">
                                                        {{ $row['total_hadir'] }}x
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-600">–</span>
                                                @endif
                                            </td>

                                            {{-- Tepat Waktu --}}
                                            <td class="px-4 py-3 text-center">
                                                @if ($row['tepat_waktu'] > 0)
                                                    <span class="inline-flex items-center rounded-md bg-success-50 dark:bg-success-900/30 px-2 py-0.5 text-xs font-medium text-success-700 dark:text-success-300 ring-1 ring-inset ring-success-200 dark:ring-success-700">
                                                        {{ $row['tepat_waktu'] }}x
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-600">–</span>
                                                @endif
                                            </td>

                                            {{-- Terlambat --}}
                                            <td class="px-4 py-3 text-center">
                                                @if ($row['terlambat'] > 0)
                                                    <span class="inline-flex items-center rounded-md bg-danger-50 dark:bg-danger-900/30 px-2 py-0.5 text-xs font-medium text-danger-700 dark:text-danger-300 ring-1 ring-inset ring-danger-200 dark:ring-danger-700">
                                                        {{ $row['terlambat'] }}x
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-md bg-success-50 dark:bg-success-900/30 px-2 py-0.5 text-xs font-medium text-success-700 dark:text-success-300 ring-1 ring-inset ring-success-200 dark:ring-success-700">
                                                        Tepat Waktu
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Cuti --}}
                                            <td class="px-4 py-3 text-center">
                                                @if ($row['cuti'] > 0)
                                                    <span class="inline-flex items-center rounded-md bg-info-50 dark:bg-info-900/30 px-2 py-0.5 text-xs font-medium text-info-700 dark:text-info-300 ring-1 ring-inset ring-info-200 dark:ring-info-700">
                                                        {{ $row['cuti'] }}x
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-600">–</span>
                                                @endif
                                            </td>

                                            {{-- Tanpa Izin --}}
                                            <td class="px-4 py-3 text-center">
                                                @if ($row['tanpa_izin'] > 0)
                                                    <span class="inline-flex items-center rounded-md bg-warning-50 dark:bg-warning-900/30 px-2 py-0.5 text-xs font-medium text-warning-700 dark:text-warning-300 ring-1 ring-inset ring-warning-200 dark:ring-warning-700">
                                                        {{ $row['tanpa_izin'] }}x
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-md bg-success-50 dark:bg-success-900/30 px-2 py-0.5 text-xs font-medium text-success-700 dark:text-success-300 ring-1 ring-inset ring-success-200 dark:ring-success-700">
                                                        Hadir Penuh
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Sisa Magang --}}
                                            <td class="px-4 py-3 text-center">
                                                @if ($row['sisa_label'] === 'Selesai')
                                                    <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-white/5 px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400 ring-1 ring-inset ring-gray-200 dark:ring-white/10">
                                                        Selesai
                                                    </span>
                                                @elseif ($row['sisa_label'] === '-')
                                                    <span class="text-gray-400 dark:text-gray-600">–</span>
                                                @else
                                                    @php
                                                        $sisa = $row['sisa_hari'];
                                                        if ($sisa <= 7) {
                                                            $badgeClass = 'bg-danger-50 dark:bg-danger-900/30 text-danger-700 dark:text-danger-300 ring-danger-200 dark:ring-danger-700';
                                                        } elseif ($sisa <= 14) {
                                                            $badgeClass = 'bg-warning-50 dark:bg-warning-900/30 text-warning-700 dark:text-warning-300 ring-warning-200 dark:ring-warning-700';
                                                        } else {
                                                            $badgeClass = 'bg-info-50 dark:bg-info-900/30 text-info-700 dark:text-info-300 ring-info-200 dark:ring-info-700';
                                                        }
                                                    @endphp
                                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badgeClass }}">
                                                        {{ $row['sisa_label'] }}
                                                    </span>
                                                @endif
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
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.1 13.177a2.25 2.25 0 0 0-.1.661Z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada data peserta Magang BPS / Alumni untuk bulan ini.</p>
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>
