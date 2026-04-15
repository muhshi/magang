<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Map extends Page
{
    use HasPageShield;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';
    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Presensi';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $label = 'Map Utama';
    protected string $view = 'filament.pages.map';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['super_admin']);
    }
}
