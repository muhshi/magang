<h2>Pendaftaran Magang Baru</h2>

<ul>
    <li><strong>Nama:</strong> {{ $internship->full_name }}</li>
    <li><strong>Email:</strong> {{ $internship->email }}</li>
    <li><strong>Asal Instansi:</strong> {{ $internship->school_name }}</li>
    <li><strong>Durasi:</strong> {{ \Carbon\Carbon::parse($internship->start_date)->format('d M Y') }} 
        - {{ \Carbon\Carbon::parse($internship->end_date)->format('d M Y') }}</li>
    <li><strong>Motivasi:</strong> {{ $internship->motivation }}</li>
    <li><strong>Keterampilan:</strong> {{ $internship->skills }}</li>
</ul>

<p>Surat pengantar dan foto peserta terlampir.</p>