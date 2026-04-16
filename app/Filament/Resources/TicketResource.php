<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Employee;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Penugasan';

    // Root level - no group
    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Penugasan';

    protected static ?string $label = 'Tugas';
    protected static ?string $pluralLabel = 'Penugasan';
    protected static ?int $navigationSort = 2;


    public static function canAccess(): bool
    {
        return !auth()->user()->hasRole(['Calon Magang', 'Alumni Magang']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        // A. Pembuat (dari tabel employees)
                        Forms\Components\Select::make('employee_id')
                            ->label('Pembuat')
                            ->relationship('employee', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        // B. Tanggal Pengisian
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Tanggal Pengisian')
                            ->default(now())
                            ->required(),
                    ]),

                // C. Deskripsi Tugas
                Forms\Components\Textarea::make('name')
                    ->label('Deskripsi Tugas')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\Grid::make(3)
                    ->schema([
                        // D. Prioritas
                        Forms\Components\Select::make('priority')
                            ->label('Prioritas')
                            ->options([
                                'urgent' => '🔴 Penting mendesak',
                                'important' => '🟡 Penting tidak mendesak',
                                'flexible' => '🟢 Fleksibel',
                            ])
                            ->default('flexible')
                            ->required(),

                        // E. Mulai
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Mulai'),

                        // F. Target Selesai
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Target Selesai'),
                    ]),

                // G. Attachment
                Forms\Components\FileUpload::make('attachment')
                    ->label('Attachment')
                    ->directory('task-attachments')
                    ->preserveFilenames()
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Tanggal Pengisian
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Pengisian')
                    ->date('d/m/Y')
                    ->sortable(),

                // 3. Pembuat (dari tabel employees)
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable()
                    ->limit(20),

                // 4. Deskripsi Tugas
                Tables\Columns\TextColumn::make('name')
                    ->label('Deskripsi Tugas')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->name),

                // 5. Prioritas (colored badge)
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'urgent' => '🔴 Mendesak',
                        'important' => '🟡 Penting',
                        'flexible' => '🟢 Fleksibel',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match($state) {
                        'urgent' => 'danger',
                        'important' => 'warning',
                        'flexible' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                // 6. Mulai
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                // 7. Target Selesai
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Selesai')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                // 8. Persetujuan (clickable toggle - Pembimbing & Super Admin)
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Persetujuan')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'approved' => '✅ Approved',
                        'pending' => '⏳ Pending',
                        default => '⏳ Pending',
                    })
                    ->color(fn (?string $state): string => match($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        default => 'warning',
                    })
                    ->action(
                        \Filament\Actions\Action::make('toggleApproval')
                            ->requiresConfirmation()
                            ->modalHeading(fn ($record) => $record->approval_status === 'approved' ? 'Batalkan Approval?' : 'Approve Tugas?')
                            ->action(function ($record) {
                                if ($record->approval_status === 'approved') {
                                    $record->update([
                                        'approval_status' => 'pending',
                                        'approved_by' => null,
                                        'approved_at' => null,
                                    ]);
                                } else {
                                    $record->update([
                                        'approval_status' => 'approved',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now(),
                                    ]);
                                }
                            })
                            ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing']))
                    ),

                // 9. Assignee (clickable - peserta magang)
                Tables\Columns\TextColumn::make('assignees.name')
                    ->label('Yang Ditugaskan')
                    ->badge()
                    ->separator(',')
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->searchable()
                    ->placeholder('Belum diset')
                    ->action(
                        \Filament\Actions\Action::make('setAssignee')
                            ->form([
                                Forms\Components\Select::make('assignee_ids')
                                    ->label('Peserta Magang')
                                    ->multiple()
                                    ->options(function () {
                                        $acceptedUserIds = Internship::where('status', 'accepted')->pluck('user_id');
                                        return User::where(function ($query) use ($acceptedUserIds) {
                                            $query->whereHas('roles', fn ($q) => $q->where('name', 'Magang BPS'))
                                                  ->orWhereIn('id', $acceptedUserIds);
                                        })
                                        ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'Alumni Magang'))
                                        ->pluck('name', 'id')->toArray();
                                    })
                                    ->default(fn ($record) => $record->assignees->pluck('id')->toArray())
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->action(function ($record, array $data) {
                                $newAssigneeIds = $data['assignee_ids'] ?? [];
                                $oldAssigneeIds = $record->assignees->pluck('id')->toArray();
                                $record->assignees()->sync($newAssigneeIds);

                                // Auto-create/update logbook untuk setiap assignee baru
                                $addedIds = array_diff($newAssigneeIds, $oldAssigneeIds);
                                $removedIds = array_diff($oldAssigneeIds, $newAssigneeIds);

                                // Hapus logbook untuk assignee yang dihapus
                                foreach ($removedIds as $userId) {
                                    $logbook = Logbook::where('ticket_id', $record->id)
                                        ->where('user_id', $userId)
                                        ->where('source', 'system')
                                        ->first();
                                    if ($logbook) {
                                        $logbook->assignees()->detach();
                                        $logbook->delete();
                                    }
                                }

                                // Buat logbook untuk assignee baru
                                foreach ($addedIds as $userId) {
                                    $logbook = Logbook::create([
                                        'user_id'           => $userId,
                                        'ticket_id'         => $record->id,
                                        'source'            => 'system',
                                        'tanggal_pengisian' => $record->created_at->toDateString(),
                                        'nama_pegawai'      => $record->employee?->name ?? '-',
                                        'deskripsi_tugas'   => $record->name,
                                        'status'            => $record->status ?? 'belum',
                                        'lampiran'          => $record->attachment,
                                    ]);
                                    $logbook->assignees()->sync($newAssigneeIds);
                                }

                                // Update status & assignees di logbook yang sudah ada
                                foreach (array_intersect($newAssigneeIds, $oldAssigneeIds) as $userId) {
                                    $logbook = Logbook::where('ticket_id', $record->id)
                                        ->where('user_id', $userId)
                                        ->where('source', 'system')
                                        ->first();
                                    if ($logbook) {
                                        $logbook->assignees()->sync($newAssigneeIds);
                                    }
                                }
                            })
                            ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing']))
                    ),

                // 10. Status (clickable - Pembimbing & Super Admin)
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'belum' => 'Belum',
                        'proses' => 'Proses',
                        'revisi' => 'Revisi',
                        'selesai' => 'Selesai',
                        default => $state ?? 'Belum',
                    })
                    ->color(fn (?string $state): string => match($state) {
                        'belum' => 'gray',
                        'proses' => 'info',
                        'revisi' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->sortable()
                    ->action(
                        \Filament\Actions\Action::make('setStatus')
                            ->form([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'belum' => 'Belum Dikerjakan',
                                        'proses' => 'Sedang Proses',
                                        'revisi' => 'Perlu Revisi',
                                        'selesai' => 'Selesai',
                                    ])
                                    ->default(fn ($record) => $record->status)
                                    ->required(),
                            ])
                            ->action(function ($record, array $data) {
                                $record->update(['status' => $data['status']]);
                            })
                            ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing', 'Magang BPS']))
                    ),

                // Lampiran (preview)
                Tables\Columns\ViewColumn::make('attachment')
                    ->label('Lampiran')
                    ->view('filament.tables.columns.attachment-preview'),
            ])
            ->filters([
                // Priority Filter
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options([
                        'urgent' => '🔴 Mendesak',
                        'important' => '🟡 Penting',
                        'flexible' => '🟢 Fleksibel',
                    ]),

                // Status Filter (static)
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'belum' => 'Belum Dikerjakan',
                        'proses' => 'Sedang Proses',
                        'revisi' => 'Perlu Revisi',
                        'selesai' => 'Selesai',
                    ]),

                // Approval Status Filter
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Persetujuan')
                    ->options([
                        'pending' => '⏳ Pending',
                        'approved' => '✅ Approved',
                    ]),

                // Filter by intern group
                Tables\Filters\SelectFilter::make('internGroups')
                    ->label('Kelompok')
                    ->relationship('internGroups', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                // Filter by pembuat (employee)
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Pembuat')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('due_date')
                    ->form([
                        Forms\Components\DatePicker::make('due_from'),
                        Forms\Components\DatePicker::make('due_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['due_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('due_date', '>=', $date),
                            )
                            ->when(
                                $data['due_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('due_date', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make('penugasan')
                            ->fromTable()
                            ->withColumns([
                                Column::make('created_at')
                                    ->heading('Tgl Pengisian')
                                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '-'),
                                Column::make('employee.name')
                                    ->heading('Nama Pegawai'),
                                Column::make('name')
                                    ->heading('Deskripsi Tugas'),
                                Column::make('priority')
                                    ->heading('Prioritas')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'urgent'    => 'Mendesak',
                                        'important' => 'Penting',
                                        'flexible'  => 'Fleksibel',
                                        default     => $state ?? '-',
                                    }),
                                Column::make('start_date')
                                    ->heading('Mulai')
                                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '-'),
                                Column::make('due_date')
                                    ->heading('Selesai')
                                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '-'),
                                Column::make('approval_status')
                                    ->heading('Persetujuan')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'approved' => 'Approved',
                                        'pending'  => 'Pending',
                                        default    => 'Pending',
                                    }),
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
                                        default   => $state ?? '-',
                                    }),
                                Column::make('attachment')
                                    ->heading('Lampiran')
                                    ->formatStateUsing(fn ($state) => $state ? asset('storage/' . $state) : '-'),
                            ])
                            ->withFilename('penugasan-' . now()->format('Y-m-d')),
                    ]),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()->label('Detail'),
                \Filament\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing', 'Pegawai BPS'])),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->visible(auth()->user()->hasRole(['super_admin'])),

                    // Bulk update status
                    \Filament\Actions\BulkAction::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'belum' => 'Belum Dikerjakan',
                                    'proses' => 'Sedang Proses',
                                    'revisi' => 'Perlu Revisi',
                                    'selesai' => 'Selesai',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }
                        })
                        ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing'])),

                    // Bulk assign peserta magang
                    \Filament\Actions\BulkAction::make('assignUsers')
                        ->label('Assign Peserta')
                        ->icon('heroicon-o-user-group')
                        ->form([
                            Forms\Components\Select::make('assignee_ids')
                                ->label('Peserta Magang')
                                ->multiple()
                                ->options(function () {
                                    $acceptedUserIds = Internship::where('status', 'accepted')->pluck('user_id');
                                    return User::where(function ($query) use ($acceptedUserIds) {
                                        $query->whereHas('roles', fn ($q) => $q->where('name', 'Magang BPS'))
                                              ->orWhereIn('id', $acceptedUserIds);
                                    })
                                    ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'Alumni Magang'))
                                    ->pluck('name', 'id')->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Radio::make('assignment_mode')
                                ->label('Mode')
                                ->options([
                                    'replace' => 'Ganti peserta',
                                    'add' => 'Tambah peserta',
                                ])
                                ->default('add')
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                $newAssigneeIds = $data['assignee_ids'];
                                $oldAssigneeIds = $record->assignees->pluck('id')->toArray();

                                if ($data['assignment_mode'] === 'replace') {
                                    $record->assignees()->sync($newAssigneeIds);
                                    $addedIds   = array_diff($newAssigneeIds, $oldAssigneeIds);
                                    $removedIds = array_diff($oldAssigneeIds, $newAssigneeIds);
                                } else {
                                    $record->assignees()->syncWithoutDetaching($newAssigneeIds);
                                    $addedIds   = array_diff($newAssigneeIds, $oldAssigneeIds);
                                    $removedIds = [];
                                }

                                $finalAssigneeIds = $record->fresh()->assignees->pluck('id')->toArray();

                                // Hapus logbook assignee yang dihapus
                                foreach ($removedIds as $userId) {
                                    $logbook = Logbook::where('ticket_id', $record->id)
                                        ->where('user_id', $userId)
                                        ->where('source', 'system')
                                        ->first();
                                    if ($logbook) {
                                        $logbook->assignees()->detach();
                                        $logbook->delete();
                                    }
                                }

                                // Buat logbook untuk assignee baru
                                foreach ($addedIds as $userId) {
                                    $logbook = Logbook::create([
                                        'user_id'           => $userId,
                                        'ticket_id'         => $record->id,
                                        'source'            => 'system',
                                        'tanggal_pengisian' => $record->created_at->toDateString(),
                                        'nama_pegawai'      => $record->employee?->name ?? '-',
                                        'deskripsi_tugas'   => $record->name,
                                        'status'            => $record->status ?? 'belum',
                                        'lampiran'          => $record->attachment,
                                    ]);
                                    $logbook->assignees()->sync($finalAssigneeIds);
                                }

                                // Update assignees di logbook yang sudah ada
                                foreach (array_intersect($finalAssigneeIds, $oldAssigneeIds) as $userId) {
                                    $logbook = Logbook::where('ticket_id', $record->id)
                                        ->where('user_id', $userId)
                                        ->where('source', 'system')
                                        ->first();
                                    if ($logbook) {
                                        $logbook->assignees()->sync($finalAssigneeIds);
                                    }
                                }
                            }
                        })
                        ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::getEloquentQuery();

        return $query->count();
    }

    // Hanya Pembimbing, Pegawai BPS, dan Super Admin yang bisa create
    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'pembimbing', 'Pegawai BPS']);
    }
}
