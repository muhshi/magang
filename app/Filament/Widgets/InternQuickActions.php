<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class InternQuickActions extends Widget
{
    protected static ?int $sort = 2;
    protected string $view = 'filament.widgets.intern-quick-actions';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Magang BPS') ?? false;
    }
}
