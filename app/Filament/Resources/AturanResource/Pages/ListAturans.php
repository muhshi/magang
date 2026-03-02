<?php

namespace App\Filament\Resources\AturanResource\Pages;

use App\Filament\Resources\AturanResource;
use Filament\Resources\Pages\ListRecords;

class ListAturans extends ListRecords
{
    protected static string $resource = AturanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
