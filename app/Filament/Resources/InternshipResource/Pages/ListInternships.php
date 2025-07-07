<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use App\Models\Internship;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
// PERBAIKAN: Impor class Tab yang benar untuk halaman ListRecords.
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

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
     * Method inilah yang akan membuat Tabs di atas tabel.
     */
    public function getTabs(): array
    {
        return [

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(Internship::query()->where('status', 'pending')->count())
                ->icon('heroicon-o-clock')
                ->badgeColor('warning'),

            'accepted' => Tab::make('Diterima')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'accepted'))
                ->badge(Internship::query()->where('status', 'accepted')->count())
                ->icon('heroicon-o-check-circle')
                ->badgeColor('success'),

            'rejected' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected'))
                ->badge(Internship::query()->where('status', 'rejected')->count())
                ->icon('heroicon-o-x-circle')
                ->badgeColor('danger'),

            'all' => Tab::make('Semua')
                ->badge(Internship::query()->count())
                ->badgeColor('primary'),
        ];
    }
}
