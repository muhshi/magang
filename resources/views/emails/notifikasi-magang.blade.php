<p>Halo {{ $internship->name }},</p>

@if ($internship->status === 'accepted')
    <p>Selamat! Pendaftaran magang kamu telah <strong>DITERIMA</strong>.</p>
@elseif ($internship->status === 'rejected')
    <p>Mohon maaf, pendaftaran magang kamu <strong>DITOLAK</strong>.</p>
@else
    <p>Status pendaftaran kamu saat ini: {{ ucfirst($internship->status) }}</p>
    <p>Untuk Informasi Lebih lanjut akan diberitahukan melalui whatsapp</p>
    <p>Silahkan Kunjungi magang.bpsdemak.com untuk melihat status magang kamu.
@endif

<p>Terima kasih telah mendaftar di BPS Kabupaten Demak.</p>
