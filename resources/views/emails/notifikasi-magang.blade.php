{{-- Ganti $internship->name menjadi $internship->full_name --}}
<p>Halo {{ $internship->full_name }},</p>

<p>Terima kasih telah melakukan pendaftaran magang di BPS Kabupaten Demak. Melalui email ini, kami memberitahukan status
    pendaftaran Anda:</p>

@if ($internship->status === 'accepted')
    <p>Selamat! Pendaftaran magang Anda telah <strong>DITERIMA</strong>.</p>

    {{-- Tambahkan info tentang lampiran surat --}}
    <p>Silakan unduh Surat Penerimaan resmi yang terlampir pada email ini untuk informasi lebih lanjut.</p>
@elseif ($internship->status === 'rejected')
    <p>Setelah melalui proses peninjauan, dengan berat hati kami sampaikan bahwa pendaftaran magang Anda
        <strong>DITOLAK</strong>.</p>
@endif

{{-- (Saran) Tampilkan catatan dari admin jika ada --}}
@if (!empty($internship->note))
    <p><strong>Catatan dari Admin:</strong><br>
        <em>{{ $internship->note }}</em>
    </p>
@endif

<p>Terima kasih atas partisipasi dan minat Anda terhadap BPS Kabupaten Demak.</p>
<br>
<p>Hormat kami,</p>
<p>Tim Manajemen Magang<br>BPS Kabupaten Demak</p>
