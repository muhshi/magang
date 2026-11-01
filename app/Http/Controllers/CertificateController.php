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
     * Helper untuk merender PDF sertifikat dan mengembalikan response stream/download.
     */
    private function buildCertificatePdfResponse(Certificate $certificate, string $disposition = 'inline')
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
            'certificateTemplatePath' => $settings->certificate_template_path ?? 'images/TEMPLATE.png',
        ];

        $pdf = Pdf::loadView('certificates.template', $data)
            ->setPaper('a4', 'landscape');

        // Apply PDF owner password protection (prevents editing, allows viewing & printing)
        $ownerPassword = $settings->certificate_pdf_password ?? 'demak3321';
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        if (method_exists($canvas, 'get_cpdf')) {
            $canvas->get_cpdf()->setEncryption('', $ownerPassword, ['print', 'copy']);
        }

        $filename = 'sertifikat-' . Str::slug($internship->full_name) . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    /**
     * Generate dan tampilkan sertifikat PDF di browser.
     */
    public function generate(Certificate $certificate)
    {
        return $this->buildCertificatePdfResponse($certificate, 'inline');
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

        return $this->buildCertificatePdfResponse($certificate, 'attachment');
    }
}
