<?php

namespace App\Filament\Resources\AturanResource\Pages;

use App\Filament\Resources\AturanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAturan extends EditRecord
{
    protected static string $resource = AturanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
