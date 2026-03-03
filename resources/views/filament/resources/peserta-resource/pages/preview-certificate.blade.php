<x-filament-panels::page>
    @if($certificate)
        <div class="space-y-6">
            {{-- Info Peserta --}}
            <x-filament::section heading="Data Peserta">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nama Lengkap</p>
                        <p class="font-semibold">{{ $record->internship->full_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Universitas</p>
                        <p class="font-semibold">{{ $record->internship->school_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Program Studi</p>
                        <p class="font-semibold">{{ $certificate->program_studi }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Fakultas</p>
                        <p class="font-semibold">{{ $certificate->fakultas }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">NIM</p>
                        <p class="font-semibold">{{ $certificate->nim }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Periode Magang</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($record->internship->start_date)->translatedFormat('d F Y') }}
                            -
                            {{ \Carbon\Carbon::parse($record->internship->end_date)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
            </x-filament::section>

            {{-- Data Sertifikat --}}
            <x-filament::section heading="Data Sertifikat">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nomor Sertifikat</p>
                        <p class="font-semibold">{{ $certificate->certificate_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Predikat</p>
                        <p class="font-semibold text-amber-600">{{ $certificate->predikat }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Sertifikat</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($certificate->certificate_date)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">UUID Verifikasi</p>
                        <p class="font-mono text-xs">{{ $certificate->uuid }}</p>
                    </div>
                </div>
            </x-filament::section>
        </div>
    @else
        <x-filament::section>
            <div class="text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">Sertifikat belum dibuat untuk peserta ini.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
