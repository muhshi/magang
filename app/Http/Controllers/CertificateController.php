<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Barryvdh\DomPDF\Facade\Pdf; // <-- Import class PDF
use Carbon\Carbon; // <-- Import Carbon untuk format tanggal
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Import Storage untuk URL foto

class CertificateController extends Controller
{
    /**
     * Method ini akan men-generate dan menampilkan sertifikat PDF.
     */
    public function generate(Internship $internship)
    {
        // 1. Validasi: Pastikan hanya pendaftar yang diterima yang bisa dibuatkan sertifikat
        if ($internship->status !== 'accepted') {
            // Jika tidak, kembalikan error atau redirect
            abort(403, 'Sertifikat hanya bisa dibuat untuk peserta yang diterima.');
        }

        // 2. Siapkan semua data yang dibutuhkan untuk dicetak
        $namaLengkap = $internship->full_name;
        $universitas = $internship->school_name;

        // Ambil foto dari profil user yang terkait dengan pendaftaran ini
        $photoPath = $internship->user->profile?->photo;
        $photoUrl = $photoPath ? Storage::url($photoPath) : null;

        // Format periode magang
        $tanggalMulai = Carbon::parse($internship->start_date)->translatedFormat('d F Y');
        $tanggalSelesai = Carbon::parse($internship->end_date)->translatedFormat('d F Y');
        $periode = "{$tanggalMulai} - {$tanggalSelesai}";

        // Gabungkan semua data ke dalam satu array
        $data = [
            'nama' => $namaLengkap,
            'universitas' => $universitas,
            'periode' => $periode,
            'photoUrl' => $photoUrl,
            // Tambahkan data lain jika perlu, misal: nomor sertifikat, nilai, dll.
        ];

        // 3. Load view Blade, masukkan data, dan atur spesifikasi PDF
        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('a4', 'landscape'); // Atur ukuran kertas A4 landscape

        // 4. Tampilkan PDF di browser
        // ->stream() akan menampilkan tanpa men-download
        // ->download('nama-file.pdf') akan langsung men-download
        return $pdf->stream('sertifikat-' . str_slug($namaLengkap) . '.pdf');
    }
}
