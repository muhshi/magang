<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Map extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Attendance Management';
    protected static string $view = 'filament.pages.map';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['super_admin']);
    }
}
