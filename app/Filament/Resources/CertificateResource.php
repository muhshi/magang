<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Internship;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CertificateResource extends Resource
{
    protected static ?string $model = Internship::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Sertifikat';
    protected static ?string $slug = 'sertifikat';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('user.profile.photo')
                    ->label('Foto')
                    // PERBAIKAN: Secara eksplisit beri tahu untuk pakai disk 'public'
                    ->disk('public')
                    ->circular()
                    // Beri gambar fallback jika foto tidak ada atau URL salah
                    ->defaultImageUrl(url('/images/mangga.png')),
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
            ->bulkActions([]);
    }

    /**
     * PERBAIKAN: Tambahkan logika query berdasarkan peran pengguna.
     */
    public static function getEloquentQuery(): Builder
    {
        // Mulai dengan query dasar: hanya yang statusnya 'accepted'
        $query = parent::getEloquentQuery()->where('status', 'accepted');

        // Jika user BUKAN super_admin...
        if (Auth::user()->roles[0]->name !== 'super_admin') {
            // ...tampilkan hanya sertifikat miliknya sendiri.
            $query->where('user_id', Auth::user()->id);
        }

        // Kembalikan query yang sudah dimodifikasi.
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCertificates::route('/'),
        ];
    }
}
