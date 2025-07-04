<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Filament\Resources\LeaveResource\RelationManagers;
use App\Models\Leave;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-c-arrow-left-end-on-rectangle';

    protected static ?string $navigationGroup = 'Manajemen Presensi';

    protected static ?string $label = 'Cuti';

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        if (Carbon::parse($data['end_date'])->lt(Carbon::parse($data['start_date']))) {
            throw ValidationException::withMessages([
                'end_date' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            ]);
        }

        $data['user_id'] = Auth::user()->id;
        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        if (Carbon::parse($data['end_date'])->lt(Carbon::parse($data['start_date']))) {
            throw ValidationException::withMessages([
                'end_date' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            ]);
        }

        return $data;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->label('Tanggal Mulai')
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->label('Tanggal Selesai')
                            ->minDate(fn(callable $get) => $get('start_date'))
                            ->native(false),
                        Forms\Components\Textarea::make('reason')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make()
                    ->schema(
                        [
                            Forms\Components\Select::make('status')
                                ->options([
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                ]),
                            Forms\Components\Textarea::make('note')->columnSpanFull(),
                        ]
                    )
                    ->hidden(Auth::user()->roles[0]->name !== 'super_admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                //if(Auth::user()->hasRole('super_admin')) -> pakai ini bisa tapi terdeteksi error sama intelephense
                if (Auth::user()->roles[0]->name != 'super_admin') {
                    $query->where('user_id', Auth::user()->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(Leave $record): string => match ($record->status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->description(fn(Leave $record) => $record->note)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->disabled(),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->disabled(),

                        Forms\Components\Textarea::make('note')
                            ->label('Catatan')
                            ->disabled(),
                    ]),
                Tables\Actions\EditAction::make()
                    ->visible(function (Leave $record): bool {
                        return $record->status === 'pending' || Auth::user()->roles[0]->name === 'super_admin';
                    }),
                // Tables\Actions\DeleteAction::make()
                // ->hidden(Auth::user()->roles[0]->name !== 'super_admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                    //Tables\Actions\ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            //'view' => Pages\ViewLeave::route('/{record}'),
            //'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
