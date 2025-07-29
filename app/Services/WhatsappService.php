<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    //cek session dan koneksi whatsappnya
    public static function isSessionConnected(): bool
    {
        $response = Http::get(self::getBaseUrl() . '/session');

        $sessions = $response->json();

        foreach ($sessions as $session) {
            if ($session['session'] === self::getSession() && $session['status'] === 'connected') {
                return true;
            }
        }

        return false;
    }
    protected static function getBaseUrl(): string
    {
        return config('services.wa_gateway.url', env('WA_GATEWAY_URL', 'http://localhost:5001'));
    }

    protected static function getSession(): string
    {
        return config('services.wa_gateway.session', env('WA_GATEWAY_SESSION', 'magang'));
    }

    public static function sendText(string $to, string $message): bool
    {
        \Log::info("WA sendText ke {$to}: {$message}");

        $response = Http::post(self::getBaseUrl() . '/message/send-text', [
            'session' => self::getSession(),
            'to' => $to,
            'text' => $message,
        ]);

        if (!$response->successful()) {
            \Log::error("WA sendText error: " . $response->body());
        }

        return $response->successful();
    }

    public static function sendImage(string $to, string $caption, string $imageUrl): bool
    {
        $response = Http::post(self::getBaseUrl() . '/message/send-image', [
            'session' => self::getSession(),
            'to' => $to,
            'text' => $caption,
            'image_url' => $imageUrl,
        ]);

        return $response->successful();
    }

    public static function sendDocument(string $to, string $caption, string $documentUrl, string $documentName): bool
    {
        $response = Http::post(self::getBaseUrl() . '/message/send-document', [
            'session' => self::getSession(),
            'to' => $to,
            'text' => $caption,
            'document_url' => $documentUrl,
            'document_name' => $documentName,
        ]);

        return $response->successful();
    }

    public static function sendInternshipNotification(string $adminNumber, array $data): void
    {
        \Log::info('📨 Fungsi sendInternshipNotification() dipanggil');

        // if (!self::isSessionConnected()) {
        //     \Log::warning("WA session tidak aktif, notifikasi magang tidak dikirim.");
        //     return; // skip pengiriman, Laravel tetap jalan normal
        // }

        $text = "[MANGGA MUDA APP]\n"
            . "*[TIDAK PERLU DIBALAS]*\n\n"
            . "📄 *Pendaftaran Magang Baru*\n\n"
            . "*Nama:* {$data['name']}\n"
            . "*Email:* {$data['email']}\n"
            . "*Instansi:* {$data['instansi']}\n"
            . "*Durasi:* {$data['durasi']}\n"
            . "*Motivasi:* {$data['motivasi']}\n"
            . "*Keterampilan:* {$data['keterampilan']}\n"
            . "Untuk Info lebih lengkap kunjungi https://magang.bpsdemak.com/admin\n"
            . "*Ini adalah balasan otomatis dari aplikasi MANGGA MUDA BPS DEMAK tidak perlu dibalas";

        self::sendText($adminNumber, $text);

        // if (!empty($data['foto_url'])) {
        //     self::sendImage($adminNumber, '📸 Foto Pendaftar', $data['foto_url']);
        // }

        // if (!empty($data['dokumen_url']) && !empty($data['dokumen_nama'])) {
        //     self::sendDocument($adminNumber, '📎 Dokumen Pendaftar', $data['dokumen_url'], $data['dokumen_nama']);
        // }
    }
}
