<?php

use App\Exports\AttendanceExport;
use App\Livewire\Presensi;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('home');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/info', function () {
    return phpinfo();
});

Route::get('/login', function () {
    return redirect('admin/login');
})->name('login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('presensi', Presensi::class)->name('presensi');
    Route::get('attendance/export', function () {
        return Excel::download(new AttendanceExport, 'presensi.xlsx');
    })->name('attendance-export');
});
