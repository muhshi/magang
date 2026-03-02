<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('Detail'),
            Actions\DeleteAction::make()
                ->modalHeading('Hapus Tugas?')
                ->modalDescription('Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Tugas berhasil dihapus')
                        ->body('Penugasan telah berhasil dihapus.')
                ),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tugas berhasil diperbarui')
            ->body('Perubahan pada penugasan telah berhasil disimpan.');
    }
}