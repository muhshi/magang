<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ChatPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.chat-page';

    protected static ?string $navigationLabel = 'Chat';

    protected static ?string $title = 'Chat';

    protected static ?string $slug = 'chat';

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
