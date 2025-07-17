<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Spatie\Permission\Models\Role;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;


class ListUsers extends ListRecords
{
    use HasResizableColumn;
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Method untuk membuat Tabs berdasarkan Role.
     */
    public function getTabs(): array
    {
        // PERBAIKAN: Tentukan peran mana saja yang ingin ditampilkan
        $rolesToDisplay = ['Calon Magang', 'Magang BPS', 'Pegawai BPS'];

        // $tabs = [
        //     // Tab 'Semua' sekarang juga tidak akan menampilkan super_admin
        //     'all' => Tab::make('Semua Pengguna')
        //         ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn(Builder $query) => $query->whereIn('name', $rolesToDisplay)))
        //         ->badge(User::role($rolesToDisplay)->count()),
        // ];

        // Buat satu tab untuk setiap role yang sudah ditentukan
        foreach ($rolesToDisplay as $roleName) {
            $tabs[$roleName] = Tab::make($roleName)
                ->modifyQueryUsing(function (Builder $query) use ($roleName) {
                    // Filter pengguna yang memiliki role ini
                    $query->whereHas('roles', function ($query) use ($roleName) {
                        $query->where('name', $roleName);
                    });
                })
                ->badge(User::role($roleName)->count())
                ->badgeColor('primary');
        }

        return $tabs;
    }
}
