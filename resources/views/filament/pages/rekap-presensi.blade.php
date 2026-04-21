<x-filament-panels::page>
    <div style="display:flex;flex-direction:column;gap:1rem;">


        {{-- ====== FILTER BULAN ====== --}}
        <x-filament::section>
            <div class="flex flex-wrap items-center gap-3">
                <span style="font-size:0.875rem;font-weight:600;">Filter Bulan:</span>
                <select
                    wire:model.live="selectedMonth"
                    style="display:block;width:12rem;padding:0.375rem 2rem 0.375rem 0.75rem;font-size:0.875rem;border:1px solid #d1d5db;border-radius:0.5rem;background:#fff;appearance:auto;"
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
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:1.5rem 2rem;font-size:0.875rem;">
                <div>
                    <span style="color:#6b7280;">Hari Kerja Efektif</span>
                    <span style="display:block;font-size:1.5rem;font-weight:700;line-height:1.2;margin-top:2px;">{{ $totalHariEfektif }} <span style="font-size:0.875rem;font-weight:400;color:#6b7280;">hari</span></span>
                </div>

                <div style="width:1px;height:2.5rem;background:#e5e7eb;flex-shrink:0;"></div>

                <div>
                    <span style="color:#6b7280;">Libur Nasional</span>
                    <span style="display:block;font-size:1.5rem;font-weight:700;line-height:1.2;margin-top:2px;color:#dc2626;">{{ $holidaysThisMonth->count() }} <span style="font-size:0.875rem;font-weight:400;color:#6b7280;">hari</span></span>
                </div>

                @if ($holidaysThisMonth->count() > 0)
                    @php
                        $fullHolidays = $this->getHolidaysFull((int) $filterYear);
                        $holidayNames = collect($fullHolidays)->keyBy('date');
                    @endphp
                    <div style="flex:1;min-width:0;">
                        <span style="display:block;font-size:0.75rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Daftar Libur</span>
                        <div style="display:flex;flex-wrap:wrap;gap:0.375rem;">
                            @foreach ($holidaysThisMonth as $hDate)
                                <x-filament::badge color="danger" size="sm">
                                    {{ \Carbon\Carbon::parse($hDate)->translatedFormat('d M') }} — {{ $holidayNames[$hDate]['name'] ?? '' }}
                                </x-filament::badge>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- ====== TABEL PESERTA ====== --}}
        @foreach (['aktif' => 'Peserta Aktif', 'selesai' => 'Peserta Selesai'] as $key => $title)
            @if (count($rekap[$key]) > 0)
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display:inline-flex;align-items:center;gap:0.5rem;">
                            <span>{{ $title }}</span>
                            <x-filament::badge :color="$key === 'aktif' ? 'primary' : 'gray'" size="sm">
                                {{ count($rekap[$key]) }} orang
                            </x-filament::badge>
                        </div>
                    </x-slot>

                    {{-- Table flush to card edges --}}
                    <div style="margin:-1.5rem -1.5rem -1.5rem;overflow-x:auto;border-top:1px solid #f3f4f6;">
                        <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                            <thead style="background:#f9fafb;">
                                <tr>
                                    <th style="padding:0.75rem 0.75rem 0.75rem 1.5rem;text-align:left;font-weight:600;color:#374151;white-space:nowrap;border-bottom:1px solid #e5e7eb;">Nama</th>
                                    <th style="padding:0.75rem;text-align:center;font-weight:600;color:#374151;white-space:nowrap;border-bottom:1px solid #e5e7eb;">Hari Efektif</th>
                                    <th style="padding:0.75rem;text-align:center;font-weight:600;color:#374151;white-space:nowrap;border-bottom:1px solid #e5e7eb;">Total Hadir</th>
                                    <th style="padding:0.75rem;text-align:center;font-weight:600;color:#374151;white-space:nowrap;border-bottom:1px solid #e5e7eb;">Tepat Waktu</th>
                                    <th style="padding:0.75rem;text-align:center;font-weight:600;color:#374151;white-space:nowrap;border-bottom:1px solid #e5e7eb;">Terlambat</th>
                                    <th style="padding:0.75rem;text-align:center;font-weight:600;color:#374151;white-space:nowrap;border-bottom:1px solid #e5e7eb;">Cuti</th>
                                    <th style="padding:0.75rem;text-align:center;font-weight:600;color:#374151;white-space:nowrap;border-bottom:1px solid #e5e7eb;">Tanpa Izin</th>
                                    <th style="padding:0.75rem 1.5rem 0.75rem 0.75rem;text-align:center;font-weight:600;color:#374151;white-space:nowrap;border-bottom:1px solid #e5e7eb;">Sisa Magang</th>
                                </tr>
                            </thead>
                            <tbody x-data="{ expandedRow: null }">
                                @foreach ($rekap[$key] as $index => $row)
                                    {{-- Data row --}}
                                    <tr wire:key="row-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                        style="cursor:pointer;transition:background 0.1s;border-bottom:1px solid #f3f4f6;"
                                        onmouseover="this.style.background='#f9fafb'"
                                        onmouseout="this.style.background=''"
                                        @click="expandedRow = expandedRow === {{ $row['user_id'] }} ? null : {{ $row['user_id'] }}">

                                        <td style="padding:0.875rem 0.75rem 0.875rem 1.5rem;font-weight:500;color:#111827;white-space:nowrap;">
                                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                                <span style="font-size:0.6rem;color:#9ca3af;transition:transform 0.2s;display:inline-block;"
                                                      x-bind:style="expandedRow === {{ $row['user_id'] }} ? '' : 'transform:rotate(-90deg)'">▼</span>
                                                {{ $row['nama'] }}
                                            </div>
                                        </td>

                                        <td style="padding:0.875rem 0.75rem;text-align:center;color:#6b7280;">
                                            {{ $row['hari_efektif'] }}
                                        </td>

                                        <td style="padding:0.875rem 0.75rem;text-align:center;">
                                            @if ($row['total_hadir'] > 0)
                                                <x-filament::badge color="primary" size="sm">{{ $row['total_hadir'] }}x</x-filament::badge>
                                            @else
                                                <span style="color:#d1d5db;">—</span>
                                            @endif
                                        </td>

                                        <td style="padding:0.875rem 0.75rem;text-align:center;">
                                            @if ($row['tepat_waktu'] > 0)
                                                <x-filament::badge color="success" size="sm">{{ $row['tepat_waktu'] }}x</x-filament::badge>
                                            @else
                                                <span style="color:#d1d5db;">—</span>
                                            @endif
                                        </td>

                                        <td style="padding:0.875rem 0.75rem;text-align:center;">
                                            @if ($row['terlambat'] > 0)
                                                <x-filament::badge color="danger" size="sm">{{ $row['terlambat'] }}x</x-filament::badge>
                                            @else
                                                <x-filament::badge color="success" size="sm">Tepat Waktu</x-filament::badge>
                                            @endif
                                        </td>

                                        <td style="padding:0.875rem 0.75rem;text-align:center;">
                                            @if ($row['cuti'] > 0)
                                                <x-filament::badge color="info" size="sm">{{ $row['cuti'] }}x</x-filament::badge>
                                            @else
                                                <span style="color:#d1d5db;">—</span>
                                            @endif
                                        </td>

                                        <td style="padding:0.875rem 0.75rem;text-align:center;">
                                            @if ($row['tanpa_izin'] > 0)
                                                <x-filament::badge color="warning" size="sm">{{ $row['tanpa_izin'] }}x</x-filament::badge>
                                            @else
                                                <x-filament::badge color="success" size="sm">Hadir Penuh</x-filament::badge>
                                            @endif
                                        </td>

                                        <td style="padding:0.875rem 1.5rem 0.875rem 0.75rem;text-align:center;">
                                            @if ($row['sisa_label'] === 'Selesai')
                                                <x-filament::badge color="gray" size="sm">Selesai</x-filament::badge>
                                            @elseif ($row['sisa_label'] === '-')
                                                <span style="color:#d1d5db;">—</span>
                                            @else
                                                @php
                                                    $sisa = $row['sisa_hari'];
                                                    $bc = $sisa <= 7 ? 'danger' : ($sisa <= 14 ? 'warning' : 'info');
                                                @endphp
                                                <x-filament::badge :color="$bc" size="sm">{{ $row['sisa_label'] }}</x-filament::badge>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Chart row --}}
                                    <tr wire:key="chart-{{ $row['user_id'] }}-{{ $selectedMonth }}"
                                        x-show="expandedRow === {{ $row['user_id'] }}"
                                        x-cloak style="display:none">
                                        <td colspan="8" style="padding:0;background:#fafafa;border-bottom:1px solid #f3f4f6;">
                                            <div x-show="expandedRow === {{ $row['user_id'] }}" x-collapse>
                                                <div style="padding:1.5rem;border-top:1px solid #f3f4f6;">
                                                    <div style="width:100%;max-width:36rem;height:11rem;margin:0 auto;"
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
                                                                         datasets: [{ data: [{{ $row['total_hadir'] }},{{ $row['tepat_waktu'] }},{{ $row['terlambat'] }},{{ $row['cuti'] }},{{ $row['tanpa_izin'] }}], backgroundColor: ['#6366f1','#22c55e','#ef4444','#0ea5e9','#f59e0b'], borderRadius: 4, barThickness: 24 }]
                                                                     },
                                                                     options: {
                                                                         responsive: true, maintainAspectRatio: false,
                                                                         plugins: { legend: { display: false }, tooltip: { backgroundColor: dk?'#1f2937':'#fff', titleColor: dk?'#f9fafb':'#111827', bodyColor: dk?'#9ca3af':'#6b7280', borderColor: dk?'rgba(255,255,255,0.1)':'rgba(0,0,0,0.08)', borderWidth:1, padding:10 } },
                                                                         scales: {
                                                                             x: { ticks:{ color:'#9ca3af', font:{size:11} }, grid:{display:false}, border:{display:false} },
                                                                             y: { beginAtZero:true, ticks:{ color:'#9ca3af', stepSize:1, font:{size:11} }, grid:{ color:'rgba(0,0,0,0.04)' }, border:{display:false} }
                                                                         }
                                                                     }
                                                                 });
                                                             }
                                                         }"
                                                         x-init="$watch('expandedRow', val => { if (val === {{ $row['user_id'] }}) $nextTick(() => render()); })">
                                                        @if ($row['total_hadir'] > 0 || $row['tanpa_izin'] > 0 || $row['cuti'] > 0)
                                                            <canvas x-ref="canvas"></canvas>
                                                        @else
                                                            <div style="display:flex;align-items:center;justify-content:center;height:100%;border:2px dashed #e5e7eb;border-radius:0.75rem;">
                                                                <span style="font-size:0.875rem;color:#9ca3af;">Belum ada data kehadiran di bulan ini.</span>
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
                <div style="padding:2.5rem 0;text-align:center;font-size:0.875rem;color:#6b7280;">
                    Tidak ada data peserta Magang BPS / Alumni untuk periode ini.
                </div>
            </x-filament::section>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</x-filament-panels::page>
