<x-filament-panels::page>
    @if($certificate)
        <div class="space-y-6">
            {{-- PDF Preview --}}
            <x-filament::section heading="Preview Sertifikat">
                <div style="width: 100%; height: 500px; border: 1px solid rgba(128,128,128,0.3); border-radius: 8px; overflow: hidden;">
                    <iframe
                        src="{{ $pdfUrl }}"
                        width="100%"
                        height="100%"
                        style="border: none;"
                    ></iframe>
                </div>

                <div class="mt-6 flex justify-center">
                    <a href="{{ $downloadUrl }}" target="_blank"
                       class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50"
                       style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);">
                        <svg class="fi-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download PDF
                    </a>
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
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Sertifikat</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($certificate->certificate_date)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Periode Magang</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($internship->start_date)->translatedFormat('d F Y') }}
                            -
                            {{ \Carbon\Carbon::parse($internship->end_date)->translatedFormat('d F Y') }}
                        </p>
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
            <div class="text-center py-12">
                <div class="mb-4">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">Sertifikat belum tersedia</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Sertifikat akan tersedia setelah admin membuatkan untuk Anda.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
