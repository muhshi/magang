<?php

namespace App\Filament\Resources\CertificateResource\Pages;

use App\Filament\Resources\CertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCertificates extends ManageRecords
{
    protected static string $resource = CertificateResource::class;

    /**
     * PERBAIKAN: Hapus tombol "Create" dari header halaman.
     */
    protected function getHeaderActions(): array
    {
        // Kembalikan array kosong agar tidak ada tombol apa pun.
        return [];
    }
}
