<?php

namespace App\Filament\Widgets;

use App\Models\Logbook;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class InternLogbookTerbaru extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Logbook Terbaru';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Magang BPS') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Logbook::query()
                    ->where('user_id', Auth::id())
                    ->latest('tanggal_pengisian')
            )
            ->columns([
                TextColumn::make('tanggal_pengisian')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('deskripsi_tugas')
                    ->label('Deskripsi')
                    ->limit(60),
                TextColumn::make('nama_pegawai')
                    ->label('Pembimbing'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'selesai' => 'success',
                        'proses' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->paginated([5]);
    }
}
