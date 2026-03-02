<?php

namespace App\Filament\Resources\LogbookResource\Pages;

use App\Filament\Resources\LogbookResource;
use Filament\Resources\Pages\ListRecords;

class ListLogbooks extends ListRecords
{
    protected static string $resource = LogbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->visible(fn () => auth()->user()->hasRole(['Magang BPS'])),
        ];
    }
}
