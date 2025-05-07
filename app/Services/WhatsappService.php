<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected static string $baseUrl = 'http://localhost:5001';
    protected static string $session = 'mysession';

    public static function sendText(string $to, string $message): bool
    {
        $response = Http::post(self::$baseUrl . '/message/send-text', [
            'session' => self::$session,
            'to' => $to,
            'text' => $message,
        ]);

        return $response->successful();
    }

    public static function sendImage(string $to, string $caption, string $imageUrl): bool
    {
        $response = Http::post(self::$baseUrl . '/message/send-image', [
            'session' => self::$session,
            'to' => $to,
            'text' => $caption,
            'image_url' => $imageUrl,
        ]);

        return $response->successful();
    }

    public static function sendDocument(string $to, string $caption, string $documentUrl, string $documentName): bool
    {
        $response = Http::post(self::$baseUrl . '/message/send-document', [
            'session' => self::$session,
            'to' => $to,
            'text' => $caption,
            'document_url' => $documentUrl,
            'document_name' => $documentName,
        ]);

        return $response->successful();
    }

    // Helper untuk kirim lengkap semua pesan pendaftaran
    public static function sendInternshipNotification(string $adminNumber, array $data): void
    {
        // Kirim teks
        $text = "📄 *Pendaftaran Magang Baru*\n\n"
            . "*Nama:* {$data['name']}\n"
            . "*Email:* {$data['email']}\n"
            . "*Instansi:* {$data['instansi']}\n"
            . "*Durasi:* {$data['durasi']}\n"
            . "*Motivasi:* {$data['motivasi']}\n"
            . "*Keterampilan:* {$data['keterampilan']}\n"
            . "Untuk Info lebih lengkap kunjungi https://pcku.muhshi.my.id/admin";

        self::sendText($adminNumber, $text);

        // Kirim foto
        if (!empty($data['foto_url'])) {
            self::sendImage($adminNumber, '📸 Foto Pendaftar', $data['foto_url']);
        }

        // Kirim file PDF
        if (!empty($data['dokumen_url']) && !empty($data['dokumen_nama'])) {
            self::sendDocument($adminNumber, '📎 Dokumen Pendaftar', $data['dokumen_url'], $data['dokumen_nama']);
        }
    }
}
