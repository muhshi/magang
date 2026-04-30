<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogbookResource\Pages;
use App\Models\Employee;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class LogbookResource extends Resource
{
    protected static ?string $model = Logbook::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';

    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Penugasan';

    protected static ?string $navigationLabel = 'Logbook';

    protected static ?string $modelLabel = 'Logbook';

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'pembimbing', 'Magang BPS', 'Alumni Magang']);
    }

    // Helper: get intern users for dropdown
    private static function getInternOptions(): array
    {
        $acceptedUserIds = Internship::where('status', 'accepted')->pluck('user_id');
        return User::where(function ($query) use ($acceptedUserIds) {
            $query->whereHas('roles', fn ($q) => $q->where('name', 'Magang BPS'))
                  ->orWhereIn('id', $acceptedUserIds);
        })
        ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'Alumni Magang'))
        ->pluck('name', 'id')
        ->toArray();
    }

    public static function form(Schema $form): Schema
    {
        $isAdmin = auth()->user()->hasRole(['super_admin', 'pembimbing']);

        return $form->schema([
            \Filament\Schemas\Components\Grid::make(2)->schema([
                Forms\Components\DatePicker::make('tanggal_pengisian')
                    ->label('Tanggal Pengisian')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('nama_pegawai')
                    ->label('Nama Pegawai (Pemberi Tugas)')
                    ->options(fn () => Employee::pluck('name', 'name')->toArray())
                    ->searchable()
                    ->required(),
            ]),

            Forms\Components\Textarea::make('deskripsi_tugas')
                ->label('Deskripsi Tugas')
                ->required()
                ->rows(4)
                ->columnSpanFull(),

            Forms\Components\Select::make('assignees')
                ->label('Yang Ditugaskan')
                ->multiple()
                ->options(fn () => static::getInternOptions())
                ->searchable()
                ->preload()
                ->required()
                ->default(fn () => [Auth::id()])
                ->columnSpanFull(),

            \Filament\Schemas\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'belum'   => 'Belum Dikerjakan',
                        'proses'  => 'Sedang Proses',
                        'revisi'  => 'Perlu Revisi',
                        'selesai' => 'Selesai',
                    ])
                    ->required()
                    ->default('belum'),

                Forms\Components\FileUpload::make('lampiran')
                    ->label('Lampiran')
                    ->nullable()
                    ->imagePreviewHeight('50')
                    ->disk('public')
                    ->directory('logbook-attachments')
                    ->preserveFilenames(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = auth()->user()->hasRole(['super_admin', 'pembimbing']);

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($isAdmin) {
                if (!$isAdmin) {
                    // Magang BPS & Alumni Magang hanya lihat logbook milik sendiri
                    $query->where('user_id', Auth::id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_pengisian')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_pegawai')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deskripsi_tugas')
                    ->label('Deskripsi Tugas')
                    ->limit(60)
                    ->tooltip(fn (Logbook $record) => $record->deskripsi_tugas)
                    ->searchable(),

                Tables\Columns\TextColumn::make('assignees.name')
                    ->label('Yang Ditugaskan')
                    ->badge()
                    ->listWithLineBreaks(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'belum'   => 'Belum Dikerjakan',
                        'proses'  => 'Sedang Proses',
                        'revisi'  => 'Perlu Revisi',
                        'selesai' => 'Selesai',
                        default   => '-',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'belum'   => 'gray',
                        'proses'  => 'info',
                        'revisi'  => 'warning',
                        'selesai' => 'success',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'system' => 'Sistem',
                        'manual' => 'Manual',
                        default  => '-',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'system' => 'primary',
                        'manual' => 'gray',
                        default  => 'gray',
                    }),

                Tables\Columns\ViewColumn::make('lampiran')
                    ->label('Lampiran')
                    ->view('filament.tables.columns.logbook-attachment-preview'),
            ])
            ->filters([
                // Filter by Yang Ditugaskan (untuk admin/pembimbing)
                Tables\Filters\SelectFilter::make('assignees')
                    ->label('Yang Ditugaskan')
                    ->relationship('assignees', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing'])),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'belum'   => 'Belum Dikerjakan',
                        'proses'  => 'Sedang Proses',
                        'revisi'  => 'Perlu Revisi',
                        'selesai' => 'Selesai',
                    ]),

                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber')
                    ->options([
                        'system' => 'Sistem',
                        'manual' => 'Manual',
                    ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make('logbook')
                            ->fromTable()
                            ->withColumns([
                                Column::make('tanggal_pengisian')
                                    ->heading('Tanggal')
                                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d M Y') : '-'),
                                Column::make('nama_pegawai')
                                    ->heading('Nama Pegawai'),
                                Column::make('deskripsi_tugas')
                                    ->heading('Deskripsi Tugas'),
                                Column::make('assignees.name')
                                    ->heading('Yang Ditugaskan')
                                    ->formatStateUsing(fn ($record) => $record->assignees->pluck('name')->join(', ')),
                                Column::make('status')
                                    ->heading('Status')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'belum'   => 'Belum Dikerjakan',
                                        'proses'  => 'Sedang Proses',
                                        'revisi'  => 'Perlu Revisi',
                                        'selesai' => 'Selesai',
                                        default   => '-',
                                    }),
                                Column::make('source')
                                    ->heading('Sumber')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'system' => 'Sistem',
                                        'manual' => 'Manual',
                                        default  => '-',
                                    }),
                                Column::make('lampiran')
                                    ->heading('Lampiran')
                                    ->formatStateUsing(fn ($state) => $state ? asset('storage/' . $state) : '-'),
                            ])
                            ->withFilename('logbook-' . now()->format('Y-m-d')),
                    ]),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                    ->visible(fn (Logbook $record) => $record->source === 'system'),
                \Filament\Actions\EditAction::make()
                    ->visible(fn (Logbook $record) =>
                        $record->source === 'manual' &&
                        $record->user_id === Auth::id() &&
                        !auth()->user()->hasRole('Alumni Magang')
                    ),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn (Logbook $record) =>
                        $record->source === 'manual' &&
                        $record->user_id === Auth::id() &&
                        !auth()->user()->hasRole('Alumni Magang')
                    ),
            ])
            ->defaultSort('tanggal_pengisian', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLogbooks::route('/'),
            'create' => Pages\CreateLogbook::route('/create'),
            'edit'   => Pages\EditLogbook::route('/{record}/edit'),
            'view'   => Pages\ViewLogbook::route('/{record}'),
        ];
    }
}
