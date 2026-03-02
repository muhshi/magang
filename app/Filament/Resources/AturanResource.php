<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AturanResource\Pages;
use App\Models\Aturan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AturanResource extends Resource
{
    protected static ?string $model = Aturan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationGroup = 'Manajemen Presensi';
    protected static ?string $label = 'Aturan';
    protected static ?string $pluralLabel = 'Aturan';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('aturan')
                    ->label('Jenis Aturan')
                    ->options([
                        'WFA'    => 'WFA (Work From Anywhere)',
                        'Banned' => 'Banned (Diblokir)',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('selesai')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->native(false)
                    ->afterOrEqual('mulai'),

                Forms\Components\Textarea::make('alasan')
                    ->label('Alasan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('aturan')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'WFA'    => 'success',
                        'Banned' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('selesai')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('alasan')
                    ->label('Alasan')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('mulai', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('aturan')
                    ->options([
                        'WFA'    => 'WFA',
                        'Banned' => 'Banned',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAturans::route('/'),
            'create' => Pages\CreateAturan::route('/create'),
            'edit'   => Pages\EditAturan::route('/{record}/edit'),
        ];
    }
}
