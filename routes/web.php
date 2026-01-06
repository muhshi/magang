<?php

use App\Exports\AttendanceExport;
use App\Http\Controllers\CertificateController;
use App\Livewire\Presensi;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('landing');
});

Route::get('/old-home', function () {
    return view('home');
});

Route::get('/home', function () {
    return view('landing');
});
Route::get('/debug-log-config', function () {
    // Dump and Die: Tampilkan konfigurasi logging dan hentikan eksekusi
    dd(config('logging'));
});
Route::get('/test-write', function () {
    $filePath = storage_path('logs/test-tulis.log');
    $content = 'Tes tulis file langsung pada ' . now();

    // Mencoba menulis file menggunakan fungsi dasar PHP
    // Simbol @ untuk menekan warning standar agar kita bisa tangani sendiri
    $result = @file_put_contents($filePath, $content . PHP_EOL, FILE_APPEND);

    if ($result === false) {
        $error = error_get_last();
        return "<h2>GAGAL TOTAL MENULIS FILE</h2>"
            . "<p>Ini masalah di level PHP/MAMP, bukan Laravel.</p>"
            . "<p>Pesan error PHP: " . ($error['message'] ?? 'Tidak ada pesan error spesifik.') . "</p>";
    } else {
        return "<h2>BERHASIL!</h2>"
            . "<p>File berhasil ditulis langsung menggunakan PHP.</p>"
            . "<p>Bytes ditulis: " . $result . "</p>"
            . "<p>Cek file: " . $filePath . "</p>";
    }
});
Route::get('/test-log', function () {
    $logMessage = '✅ TES LOG: Route /test-log berhasil diakses pada ' . now();

    try {
        Log::info($logMessage);
        return 'Log berhasil ditulis! Cek file storage/logs/laravel.log untuk pesan: "' . $logMessage . '"';
    } catch (\Exception $e) {
        return 'GAGAL menulis log. Error: ' . $e->getMessage();
    }
});

Route::get('/info', function () {
    return phpinfo();
});

Route::get('/admin/internships/{internship}/certificate', [CertificateController::class, 'generate'])
    ->middleware('auth') // Pastikan hanya user yang login bisa akses
    ->name('certificate.generate');

Route::get('/login', function () {
    return redirect('admin/login');
})->name('login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('presensi', Presensi::class)->name('presensi');
    Route::get('attendance/export', function () {
        return Excel::download(new AttendanceExport, 'presensi.xlsx');
    })->name('attendance-export');
});

// Google SSO Routes
use App\Http\Controllers\SocialiteController;
Route::get('auth/google', [SocialiteController::class, 'redirect'])->name('auth.google');
Route::get('auth/google/callback', [SocialiteController::class, 'callback']);
