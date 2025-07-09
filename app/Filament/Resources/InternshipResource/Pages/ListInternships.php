<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use App\Models\Internship;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; // <-- Import Auth

class ListInternships extends ListRecords
{
    use HasResizableColumn;
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Method untuk membuat Tabs.
     */
    public function getTabs(): array
    {
        // PERBAIKAN: Hanya tampilkan tabs jika user adalah super_admin
        if (Auth::user()->roles[0]->name === 'super_admin' || Auth::user()->roles[0]->name === 'Pegawai BPS') {
            return [
                'all' => Tab::make('Semua')
                    ->badge(Internship::query()->count())
                    ->badgeColor('primary'),

                'pending' => Tab::make('Pending')
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                    ->badge(Internship::query()->where('status', 'pending')->count())
                    ->badgeColor('warning')
                    ->icon('heroicon-o-clock'),

                'accepted' => Tab::make('Diterima')
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'accepted'))
                    ->badge(Internship::query()->where('status', 'accepted')->count())
                    ->badgeColor('success')
                    ->icon('heroicon-o-check-circle'),

                'rejected' => Tab::make('Ditolak')
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected'))
                    ->badge(Internship::query()->where('status', 'rejected')->count())
                    ->badgeColor('danger')
                    ->icon('heroicon-o-x-circle'),
            ];
        }

        // Jika bukan super_admin, jangan tampilkan tab sama sekali
        return [];
    }
}
