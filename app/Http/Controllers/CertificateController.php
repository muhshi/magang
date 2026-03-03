<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Settings\SystemSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    /**
     * Generate dan tampilkan sertifikat PDF di browser.
     */
    public function generate(Certificate $certificate)
    {
        $internship = $certificate->internship;
        $settings = app(SystemSettings::class);

        // Siapkan data teks
        Carbon::setLocale('id');
        $tanggalMulai = Carbon::parse($internship->start_date)->translatedFormat('d F Y');
        $tanggalSelesai = Carbon::parse($internship->end_date)->translatedFormat('d F Y');
        $tanggalSertifikat = Carbon::parse($certificate->certificate_date)->translatedFormat('d F Y');

        // Generate QR Code sebagai Base64 Data URI
        $verifyUrl = route('certificate.verify', $certificate->uuid);
        $qrCodeSvg = QrCode::format('svg')
            ->size(120)
            ->margin(0)
            ->generate($verifyUrl);
        $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $data = [
            'nama' => $internship->full_name,
            'nomor' => $certificate->certificate_number,
            'programStudi' => $certificate->program_studi,
            'fakultas' => $certificate->fakultas,
            'universitas' => $internship->school_name,
            'nim' => $certificate->nim,
            'periodeMulai' => $tanggalMulai,
            'periodeSelesai' => $tanggalSelesai,
            'predikat' => $certificate->predikat,
            'tanggalSertifikat' => $tanggalSertifikat,
            'kepalaBpsName' => $settings->kepala_bps_name,
            'kepalaBpsNip' => $settings->kepala_bps_nip,
            'qrCodeDataUri' => $qrCodeDataUri,
        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('sertifikat-' . Str::slug($internship->full_name) . '.pdf');
    }

    /**
     * Halaman publik verifikasi sertifikat.
     */
    public function verify(string $uuid)
    {
        $certificate = Certificate::where('uuid', $uuid)->firstOrFail();
        $internship = $certificate->internship;
        $settings = app(SystemSettings::class);

        $tanggalMulai = Carbon::parse($internship->start_date)->translatedFormat('d F Y');
        $tanggalSelesai = Carbon::parse($internship->end_date)->translatedFormat('d F Y');
        $tanggalSertifikat = Carbon::parse($certificate->certificate_date)->translatedFormat('d F Y');

        return view('certificates.verify', [
            'certificate' => $certificate,
            'internship' => $internship,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'tanggalSertifikat' => $tanggalSertifikat,
            'kepalaBpsName' => $settings->kepala_bps_name,
        ]);
    }

    /**
     * Download sertifikat PDF dari halaman publik.
     */
    public function download(string $uuid)
    {
        $certificate = Certificate::where('uuid', $uuid)->firstOrFail();
        $internship = $certificate->internship;
        $settings = app(SystemSettings::class);

        Carbon::setLocale('id');
        $tanggalMulai = Carbon::parse($internship->start_date)->translatedFormat('d F Y');
        $tanggalSelesai = Carbon::parse($internship->end_date)->translatedFormat('d F Y');
        $tanggalSertifikat = Carbon::parse($certificate->certificate_date)->translatedFormat('d F Y');

        $verifyUrl = route('certificate.verify', $certificate->uuid);
        $qrCodeSvg = QrCode::format('svg')
            ->size(120)
            ->margin(0)
            ->generate($verifyUrl);
        $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $data = [
            'nama' => $internship->full_name,
            'nomor' => $certificate->certificate_number,
            'programStudi' => $certificate->program_studi,
            'fakultas' => $certificate->fakultas,
            'universitas' => $internship->school_name,
            'nim' => $certificate->nim,
            'periodeMulai' => $tanggalMulai,
            'periodeSelesai' => $tanggalSelesai,
            'predikat' => $certificate->predikat,
            'tanggalSertifikat' => $tanggalSertifikat,
            'kepalaBpsName' => $settings->kepala_bps_name,
            'kepalaBpsNip' => $settings->kepala_bps_nip,
            'qrCodeDataUri' => $qrCodeDataUri,
        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat-' . Str::slug($internship->full_name) . '.pdf');
    }
}
