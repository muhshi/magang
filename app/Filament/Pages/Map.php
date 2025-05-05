<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Map extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Attendance Management';
    protected static string $view = 'filament.pages.map';

    public static function canAccess(): bool
    {
        return !auth()->user()->hasRole(['Calon Magang']);
    }

}
