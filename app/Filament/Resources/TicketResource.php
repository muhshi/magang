<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Epic;
use Illuminate\Support\Facades\Auth;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $navigationGroup = 'Manajemen Tugas';

    protected static ?string $label = 'Tugas';
    protected static ?int $navigationSort = 10;


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->hasRole(['super_admin'])) {
            $query->where(function ($query) {
                $query->whereHas('assignees', function ($query) {
                    $query->where('users.id', auth()->id());
                })
                    ->orWhere('created_by', auth()->id())
                    ->orWhereHas('project.members', function ($query) {
                        $query->where('users.id', auth()->id());
                    });
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        $projectId = request()->query('project_id') ?? request()->input('project_id');
        $statusId = request()->query('ticket_status_id') ?? request()->input('ticket_status_id');

        return $form
            ->schema([
                // Hidden project field (for filtering)
                Forms\Components\Hidden::make('project_id')
                    ->default($projectId),

                Forms\Components\Grid::make(2)
                    ->schema([
                        // A. Pembuat/Creator
                        Forms\Components\Select::make('created_by')
                            ->label('Pembuat/Creator')
                            ->relationship('creator', 'name')
                            ->default(auth()->id())
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

                Forms\Components\Grid::make(2)
                    ->schema([
                        // G. Assignee (Multi-select)
                        Forms\Components\Select::make('assignees')
                            ->label('Assignee')
                            ->multiple()
                            ->relationship(
                                name: 'assignees',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih anak magang yang ditugaskan'),

                        // H. Status
                        Forms\Components\Select::make('ticket_status_id')
                            ->label('Status')
                            ->options([
                                'belum' => 'Belum',
                                'proses' => 'Proses',
                                'revisi' => 'Revisi',
                                'selesai' => 'Selesai',
                            ])
                            ->default('belum')
                            ->required(),
                    ]),

                // I. Attachment
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
                // 1. No (Row number - auto)
                Tables\Columns\TextColumn::make('id')
                    ->label('No')
                    ->rowIndex()
                    ->sortable(),

                // 2. Tanggal Pengisian
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Pengisian')
                    ->date('d/m/Y')
                    ->sortable(),

                // 3. Pembuat
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Pembuat')
                    ->sortable()
                    ->searchable()
                    ->limit(15),

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
                    ->label('Target')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),

                // 8. Persetujuan (approval status badge)
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
                    }),

                // 9. Assignee (multi-badge)
                Tables\Columns\TextColumn::make('assignees.name')
                    ->label('Assignee')
                    ->badge()
                    ->separator(',')
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->searchable(),

                // 10. Status
                Tables\Columns\TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                // 11. Attachment (icon link)
                Tables\Columns\IconColumn::make('attachment')
                    ->label('📎')
                    ->icon(fn ($state) => $state ? 'heroicon-o-paper-clip' : null)
                    ->url(fn ($record) => $record->attachment ? asset('storage/' . $record->attachment) : null)
                    ->openUrlInNewTab()
                    ->tooltip(fn ($record) => $record->attachment ? 'Download File' : 'No Attachment'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Project')
                    ->options(function () {
                        if (auth()->user()->hasRole(['super_admin'])) {
                            return Project::pluck('name', 'id')->toArray();
                        }

                        return auth()->user()->projects()->pluck('name', 'projects.id')->toArray();
                    })
                    ->searchable()
                    ->preload(),

                // Priority Filter
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options([
                        'urgent' => '🔴 Mendesak',
                        'important' => '🟡 Penting',
                        'flexible' => '🟢 Fleksibel',
                    ]),

                // Approval Status Filter
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Persetujuan')
                    ->options([
                        'pending' => '⏳ Pending',
                        'approved' => '✅ Approved',
                    ]),

                Tables\Filters\SelectFilter::make('ticket_status_id')
                    ->label('Status')
                    ->options(function () {
                        $projectId = request()->input('tableFilters.project_id');

                        if (!$projectId) {
                            return [];
                        }

                        return TicketStatus::where('project_id', $projectId)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),

                // Filter by assignees
                Tables\Filters\SelectFilter::make('assignees')
                    ->label('Assignee')
                    ->relationship('assignees', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                // Filter by creator
                Tables\Filters\SelectFilter::make('created_by')
                    ->label('Created By')
                    ->relationship('creator', 'name')
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
            ->actions([
                // Approval Toggle Action (for Pembimbing & Super Admin)
                Tables\Actions\Action::make('toggleApproval')
                    ->label(fn ($record) => $record->approval_status === 'approved' ? 'Batalkan' : 'Approve')
                    ->icon(fn ($record) => $record->approval_status === 'approved' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->approval_status === 'approved' ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->approval_status === 'approved' ? 'Batalkan Approval?' : 'Approve Tugas?')
                    ->modalDescription(fn ($record) => $record->approval_status === 'approved' 
                        ? 'Apakah Anda yakin ingin membatalkan approval tugas ini?' 
                        : 'Apakah Anda yakin ingin meng-approve tugas ini?')
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
                    ->visible(fn () => auth()->user()->hasRole(['super_admin', 'pembimbing'])),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(auth()->user()->hasRole(['super_admin'])),

                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('ticket_status_id')
                                ->label('Status')
                                ->options(function () {
                                    $firstTicket = Ticket::find(request('records')[0] ?? null);
                                    if (!$firstTicket) {
                                        return [];
                                    }

                                    return TicketStatus::where('project_id', $firstTicket->project_id)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'ticket_status_id' => $data['ticket_status_id'],
                                ]);
                            }
                        }),

                    // New bulk action for assigning users
                    Tables\Actions\BulkAction::make('assignUsers')
                        ->label('Assign Users')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('assignees')
                                ->label('Assignees')
                                ->multiple()
                                ->options(function () {
                                    $firstTicket = Ticket::find(request('records')[0] ?? null);
                                    if (!$firstTicket) {
                                        return [];
                                    }

                                    $project = $firstTicket->project;
                                    if (!$project) {
                                        return [];
                                    }

                                    return $project->members()
                                        ->select('users.id', 'users.name')
                                        ->pluck('users.name', 'users.id')
                                        ->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Radio::make('assignment_mode')
                                ->label('Assignment Mode')
                                ->options([
                                    'replace' => 'Replace existing assignees',
                                    'add' => 'Add to existing assignees',
                                ])
                                ->default('add')
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                if ($data['assignment_mode'] === 'replace') {
                                    $record->assignees()->sync($data['assignees']);
                                } else {
                                    $record->assignees()->syncWithoutDetaching($data['assignees']);
                                }
                            }
                        }),
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
}
