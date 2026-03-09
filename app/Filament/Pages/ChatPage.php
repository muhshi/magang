<?php

namespace App\Filament\Pages;

use App\Models\ChatConversation;
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

    public static function getNavigationBadge(): ?string
    {
        $count = ChatConversation::where('is_read_by_admin', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }
}
