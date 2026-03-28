<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Exports\AttendanceExport;
use App\Filament\Resources\AttendanceResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
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
                    return Excel::download(new AttendanceExport, 'presensi.xlsx');
                }),
            Action::make('presensi')
                ->url(route('presensi'))
                ->color('warning'),
            Actions\CreateAction::make(),
        ];
    }
}
