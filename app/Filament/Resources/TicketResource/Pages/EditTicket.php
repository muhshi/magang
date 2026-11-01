<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Logbook;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['assignee_ids'] = $this->record->assignees->pluck('id')->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $newAssigneeIds = $this->data['assignee_ids'] ?? [];
        $oldAssigneeIds = $this->record->assignees->pluck('id')->toArray();

        $this->record->assignees()->sync($newAssigneeIds);

        $addedIds   = array_diff($newAssigneeIds, $oldAssigneeIds);
        $removedIds = array_diff($oldAssigneeIds, $newAssigneeIds);

        foreach ($removedIds as $userId) {
            $logbook = Logbook::where('ticket_id', $this->record->id)
                ->where('user_id', $userId)
                ->where('source', 'system')
                ->first();
            if ($logbook) {
                $logbook->assignees()->detach();
                $logbook->delete();
            }
        }

        foreach ($addedIds as $userId) {
            $logbook = Logbook::create([
                'user_id'           => $userId,
                'ticket_id'         => $this->record->id,
                'source'            => 'system',
                'tanggal_pengisian' => $this->record->created_at ? $this->record->created_at->toDateString() : now()->toDateString(),
                'nama_pegawai'      => $this->record->employee?->name ?? '-',
                'deskripsi_tugas'   => $this->record->name,
                'status'            => $this->record->status ?? 'belum',
                'lampiran'          => $this->record->attachment,
            ]);
            $logbook->assignees()->sync($newAssigneeIds);
        }

        foreach (array_intersect($newAssigneeIds, $oldAssigneeIds) as $userId) {
            $logbook = Logbook::where('ticket_id', $this->record->id)
                ->where('user_id', $userId)
                ->where('source', 'system')
                ->first();
            if ($logbook) {
                $logbook->update([
                    'status' => $this->record->status ?? 'belum',
                    'deskripsi_tugas' => $this->record->name,
                    'lampiran' => $this->record->attachment,
                    'nama_pegawai' => $this->record->employee?->name ?? '-',
                ]);
                $logbook->assignees()->sync($newAssigneeIds);
            }
        }
    }

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