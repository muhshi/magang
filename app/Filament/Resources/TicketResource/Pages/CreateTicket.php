<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Logbook;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $newAssigneeIds = $this->data['assignee_ids'] ?? [];
        if (!empty($newAssigneeIds)) {
            $this->record->assignees()->sync($newAssigneeIds);

            foreach ($newAssigneeIds as $userId) {
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
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tugas berhasil dibuat')
            ->body('Penugasan baru telah berhasil ditambahkan.');
    }
}