<x-filament-widgets::widget>
    <x-filament::section heading="Aksi Cepat">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('presensi') }}" target="_blank"
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #2563eb; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                🕐 Presensi Sekarang
            </a>

            <a href="{{ url('/admin/leaves/create') }}"
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #f59e0b; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                ✋ Ajukan Cuti
            </a>

            <a href="{{ url('/admin/logbooks/create') }}"
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #10b981; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                📖 Isi Logbook
            </a>

            <a href="{{ url('/admin/sertifikat-saya') }}"
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #8b5cf6; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                🎓 Sertifikat
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
