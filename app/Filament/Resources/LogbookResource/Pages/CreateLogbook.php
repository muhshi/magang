<?php

namespace App\Filament\Resources\LogbookResource\Pages;

use App\Filament\Resources\LogbookResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLogbook extends CreateRecord
{
    protected static string $resource = LogbookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['source']  = 'manual';
        return $data;
    }

    protected function afterCreate(): void
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
