<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Exports\AttendanceExport;
use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

class ListAttendances extends ListRecords
{
    use HasResizableColumn;
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_data')
                ->label('Download Data')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    if (Attendance::count() === 0) {
                        Notification::make()
                            ->title('Data presensi masih kosong')
                            ->body('Belum ada data presensi yang bisa didownload.')
                            ->warning()
                            ->send();
                        return;
                    }

                    return response()->streamDownload(function () {
                        echo Excel::raw(new AttendanceExport, ExcelFormat::XLSX);
                    }, 'presensi.xlsx', [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment; filename="presensi.xlsx"',
                    ]);
                }),

            Action::make('presensi')
                ->label('Halaman Presensi')
                ->url(route('presensi'))
                ->color('warning'),

            Actions\CreateAction::make(),
        ];
    }
}
