<?php

namespace App\Filament\Resources\PesertaResource\Pages;

use App\Filament\Resources\PesertaResource;
use Carbon\Carbon;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPeserta extends ListRecords
{
    protected static string $resource = PesertaResource::class;

    protected static ?string $title = 'Daftar Peserta';

    public function getTabs(): array
    {
        return [
            'magang_bps' => Tab::make('Magang BPS')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereHas('roles', fn ($q) => $q->where('name', 'Magang BPS'))
                )
                ->badge(fn () =>
                    \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'Magang BPS'))
                        ->count()
                ),

            'alumni' => Tab::make('Alumni')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where(function ($q) {
                        $q->whereHas('roles', fn ($r) => $r->where('name', 'Alumni Magang'))
                          ->orWhereHas('internship', fn ($i) =>
                              $i->where('status', 'accepted')
                                ->where('end_date', '<', Carbon::today())
                          );
                    })
                )
                ->badge(fn () =>
                    \App\Models\User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Magang BPS', 'Alumni Magang']))
                        ->where(function ($q) {
                            $q->whereHas('roles', fn ($r) => $r->where('name', 'Alumni Magang'))
                              ->orWhereHas('internship', fn ($i) =>
                                  $i->where('status', 'accepted')
                                    ->where('end_date', '<', Carbon::today())
                              );
                        })->count()
                ),
        ];
    }
}
