<?php

use App\Exports\AttendanceExport;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\CertificateController;
use App\Livewire\Presensi;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;


Route::get('/', function () {
    return view('home');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/admin/certificates/{certificate}/generate', [CertificateController::class, 'generate'])
    ->middleware('auth')
    ->name('certificate.generate');

// Halaman publik verifikasi sertifikat (tanpa auth)
Route::get('/sertifikat/{uuid}', [CertificateController::class, 'verify'])
    ->name('certificate.verify');

Route::get('/sertifikat/{uuid}/download', [CertificateController::class, 'download'])
    ->name('certificate.download');

// SIPETRA SSO Routes
Route::get('/auth/sipetra/redirect', [SsoController::class, 'redirect'])->name('sipetra.login');
Route::get('/auth/sipetra/callback', [SsoController::class, 'callback']);

Route::get('/login', function () {
    return redirect('admin/login');
})->name('login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('presensi', Presensi::class)->name('presensi');
    Route::get('attendance/export', function () {
        return response()->streamDownload(function () {
            echo Excel::raw(new AttendanceExport, \Maatwebsite\Excel\Excel::XLSX);
        }, 'presensi.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    })->name('attendance-export');
});
