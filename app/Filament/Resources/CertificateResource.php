<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Internship; // PERBAIKAN: Ganti model ke Internship
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertificateResource extends Resource
{
    // PERBAIKAN: Arahkan resource ini untuk menggunakan model Internship
    protected static ?string $model = Internship::class;

    // PERBAIKAN: Atur ikon, nama, dan URL di sidebar
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Sertifikat';
    protected static ?string $slug = 'sertifikat';

    /**
     * Kita tidak butuh form create/edit, jadi biarkan kosong.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    /**
     * PERBAIKAN: Definisikan tabel untuk menampilkan daftar peserta yang layak.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('user.profile.photo')
                    ->label('Foto')
                    ->circular(),
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('user.profile.school_name')
                    ->label('Universitas/Sekolah')
                    ->searchable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d F Y'),
            ])
            ->actions([
                Action::make('generate')
                    ->label('Generate PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(Internship $record): string => route('certificate.generate', $record))
                    ->openUrlInNewTab(),
            ])
            // Hapus bulk actions karena tidak diperlukan
            ->bulkActions([]);
    }

    /**
     * PERBAIKAN: Filter data agar hanya menampilkan peserta yang statusnya 'accepted'.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'accepted');
    }

    /**
     * PERBAIKAN: Arahkan ke halaman list yang benar.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCertificates::route('/'),
        ];
    }
}
