<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use App\Models\Internship;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CertificateResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Certificate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Sertifikat';
    protected static ?string $slug = 'sertifikat';

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Select::make('internship_id')
                ->label('Peserta Magang')
                ->relationship('internship', 'full_name')
                ->options(function (?Certificate $record) {
                    $query = Internship::where('status', 'accepted');

                    // Pada mode create: hanya tampilkan yang belum punya sertifikat
                    // Pada mode edit: tampilkan juga internship milik record ini
                    if ($record) {
                        $query->where(function ($q) use ($record) {
                            $q->whereDoesntHave('certificate')
                              ->orWhere('id', $record->internship_id);
                        });
                    } else {
                        $query->whereDoesntHave('certificate');
                    }

                    return $query->pluck('full_name', 'id');
                })
                ->searchable()
                ->required()
                ->disabledOn('edit')
                ->columnSpanFull(),

            TextInput::make('certificate_number')
                ->label('Nomor Sertifikat')
                ->required(),

            TextInput::make('program_studi')
                ->label('Program Studi')
                ->required(),

            TextInput::make('fakultas')
                ->label('Fakultas')
                ->required(),

            TextInput::make('nim')
                ->label('NIM')
                ->required(),

            Select::make('predikat')
                ->label('Predikat/Hasil')
                ->options([
                    'SANGAT BAIK' => 'SANGAT BAIK',
                    'BAIK' => 'BAIK',
                    'CUKUP' => 'CUKUP',
                    'KURANG' => 'KURANG',
                ])
                ->required(),

            DatePicker::make('certificate_date')
                ->label('Tanggal Sertifikat')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('internship.full_name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('certificate_number')
                    ->label('Nomor Sertifikat')
                    ->searchable(),
                TextColumn::make('program_studi')
                    ->label('Program Studi'),
                TextColumn::make('predikat')
                    ->label('Predikat')
                    ->badge()
                    ->color('success'),
                TextColumn::make('certificate_date')
                    ->label('Tanggal Sertifikat')
                    ->date('d F Y'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('generate')
                    ->label('Generate PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(Certificate $record): string => route('certificate.generate', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->roles[0]->name !== 'super_admin') {
            $query->whereHas('internship', function (Builder $q) {
                $q->where('user_id', Auth::user()->id);
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }
}
