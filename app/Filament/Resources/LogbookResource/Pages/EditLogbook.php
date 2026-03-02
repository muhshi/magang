<?php

namespace App\Filament\Resources\LogbookResource\Pages;

use App\Filament\Resources\LogbookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogbook extends EditRecord
{
    protected static string $resource = LogbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () =>
                    $this->record->source === 'manual' &&
                    $this->record->user_id === auth()->id() &&
                    !auth()->user()->hasRole('Alumni Magang')
                ),
        ];
    }

    protected function afterSave(): void
    {
        // Sync assignees ke pivot table
        $assignees = $this->data['assignees'] ?? [];
        $this->record->assignees()->sync($assignees);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
