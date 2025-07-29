<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * Method ini akan men-generate dan menampilkan sertifikat PDF.
     */
    public function generate(Internship $internship)
    {
        // 1. Validasi status.
        if ($internship->status !== 'accepted') {
            abort(403, 'Sertifikat hanya bisa dibuat untuk peserta yang diterima.');
        }

        // 2. Siapkan data teks.
        $namaLengkap = $internship->full_name;
        $profile = $internship->user->profile;
        $universitas = $profile?->school_name ?? 'Universitas tidak ditemukan';

        $tanggalMulai = Carbon::parse($internship->start_date)->translatedFormat('d F Y');
        $tanggalSelesai = Carbon::parse($internship->end_date)->translatedFormat('d F Y');
        $periode = "{$tanggalMulai} - {$tanggalSelesai}";

        // 3. PERBAIKAN UTAMA: Siapkan gambar sebagai Data URI (Base64).
        // Ini adalah cara paling andal untuk menampilkan gambar di PDF.
        $photoDataUri = null;
        $photoPath = $profile?->photo;

        if ($photoPath && Storage::disk('public')->exists($photoPath)) {
            $fileContent = Storage::disk('public')->get($photoPath);
            $mimeType = Storage::disk('public')->mimeType($photoPath);
            $photoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
        }

        // 4. Gabungkan semua data untuk dikirim ke view.
        $data = [
            'nama' => $namaLengkap,
            'universitas' => $universitas,
            'periode' => $periode,
            'photoDataUri' => $photoDataUri, // Kirim data URI gambar ke view
        ];

        // 5. Load view dan generate PDF.
        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('a4', 'landscape');

        // 6. Tampilkan PDF di browser.
        return $pdf->stream('sertifikat-' . Str::slug($namaLengkap) . '.pdf');
    }
}
