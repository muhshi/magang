<?php

namespace App\Filament\Resources\PesertaResource\Pages;

use App\Filament\Resources\PesertaResource;
use Filament\Resources\Pages\ListRecords;

class ListPeserta extends ListRecords
{
    protected static string $resource = PesertaResource::class;

    protected static ?string $title = 'Daftar Peserta';
}
