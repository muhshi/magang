<?php

namespace App\Filament\Resources\InternshipResource\Pages;

use App\Filament\Resources\InternshipResource;
use App\Models\Internship;
use App\Models\User;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::user()->roles[0]->name === 'super_admin' || Auth::user()->roles[0]->name === 'Pegawai BPS') {
            return [
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

                'magang_bps' => Tab::make('Magang BPS')
                    ->modifyQueryUsing(fn(Builder $query) => 
                        $query->where('status', 'accepted')
                            ->whereHas('user.roles', fn($q) => $q->where('name', 'Magang BPS'))
                    )
                    ->badge(fn() => 
                        Internship::query()
                            ->where('status', 'accepted')
                            ->whereHas('user.roles', fn($q) => $q->where('name', 'Magang BPS'))
                            ->count()
                    )
                    ->badgeColor('success')
                    ->icon('heroicon-o-academic-cap'),

                'alumni' => Tab::make('Alumni')
                    ->modifyQueryUsing(fn(Builder $query) => 
                        $query->where('status', 'accepted')
                            ->whereHas('user.roles', fn($q) => $q->where('name', 'Alumni Magang'))
                    )
                    ->badge(fn() => 
                        Internship::query()
                            ->where('status', 'accepted')
                            ->whereHas('user.roles', fn($q) => $q->where('name', 'Alumni Magang'))
                            ->count()
                    )
                    ->badgeColor('info')
                    ->icon('heroicon-o-user-group'),

                'all' => Tab::make('Semua')
                    ->badge(Internship::query()->count())
                    ->badgeColor('primary')
                    ->icon('heroicon-o-list-bullet'),
            ];
        }

        // Jika bukan super_admin/Pegawai BPS, jangan tampilkan tab
        return [];
    }
}
