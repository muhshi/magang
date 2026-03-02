<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternPenugasanAktif extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Penugasan Aktif';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Magang BPS') ?? false;
    }

    public function table(Table $table): Table
    {
        $userId = Auth::id();

        return $table
            ->query(
                Ticket::query()
                    ->whereHas('assignees', function (Builder $q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                    ->where(function ($q) {
                        $q->whereNull('status')
                          ->orWhereNotIn('status', ['selesai', 'ditolak']);
                    })
                    ->latest()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Tugas')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'urgent' => 'Mendesak',
                        'important' => 'Penting',
                        'flexible' => 'Fleksibel',
                        default => $state ?? '-',
                    })
                    ->color(fn ($state) => match($state) {
                        'urgent' => 'danger',
                        'important' => 'warning',
                        'flexible' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('due_date')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->color(fn ($state) => $state && $state < now() ? 'danger' : 'gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->default('Baru'),
            ])
            ->paginated([5]);
    }
}
