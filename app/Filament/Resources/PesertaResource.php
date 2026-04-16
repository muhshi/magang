<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesertaResource\Pages;
use App\Models\Certificate;
use App\Models\Internship;
use App\Models\User;
use App\Settings\SystemSettings;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PesertaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $label = 'Peserta';
    protected static ?string $pluralLabel = 'Peserta';
    protected static ?string $slug = 'peserta';
    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Sertifikat';
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('internship.school_name')
                    ->label('Universitas')
                    ->searchable(),

                TextColumn::make('internship.program_studi')
                    ->label('Program Studi'),

                TextColumn::make('durasi')
                    ->label('Periode Magang')
                    ->getStateUsing(function ($record) {
                        $internship = $record->internship;
                        if (!$internship) return '-';
                        return Carbon::parse($internship->start_date)->translatedFormat('d M Y')
                            . ' - ' . Carbon::parse($internship->end_date)->translatedFormat('d M Y');
                    }),

                TextColumn::make('roles.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Magang BPS' => 'success',
                        'Alumni Magang' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('sertifikat_status')
                    ->label('Sertifikat')
                    ->getStateUsing(function ($record) {
                        $internship = $record->internship;
                        if (!$internship) return 'Tidak ada data';
                        return $internship->certificate ? 'Sudah dibuat' : 'Belum dibuat';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'Sudah dibuat' ? 'success' : 'warning'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('generate_sertifikat')
                    ->label('Generate Sertifikat')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->visible(function ($record) {
                        $internship = $record->internship;
                        return $internship && !$internship->certificate;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Sertifikat')
                    ->modalDescription('Sertifikat akan di-generate otomatis dari data pendaftaran. Lanjutkan?')
                    ->action(function ($record) {
                        $internship = $record->internship;

                        if (!$internship) {
                            Notification::make()
                                ->title('Data magang tidak ditemukan')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Auto-generate nomor sertifikat
                        $lastCert = Certificate::orderBy('id', 'desc')->first();
                        $lastNumber = 0;
                        if ($lastCert && preg_match('/^B-(\d+)/', $lastCert->certificate_number, $matches)) {
                            $lastNumber = (int) $matches[1];
                        }
                        $newNumber = $lastNumber + 1;
                        $year = Carbon::now()->year;
                        $certificateNumber = 'B-' . $newNumber . '/33210/HM.340/' . $year;

                        // Create certificate
                        $certificate = Certificate::create([
                            'internship_id' => $internship->id,
                            'uuid' => Str::uuid(),
                            'certificate_number' => $certificateNumber,
                            'program_studi' => $internship->program_studi ?? '-',
                            'fakultas' => $internship->fakultas ?? '-',
                            'nim' => $internship->nim ?? '-',
                            'predikat' => 'SANGAT BAIK',
                            'certificate_date' => Carbon::today(),
                        ]);

                        Notification::make()
                            ->title('Sertifikat berhasil di-generate!')
                            ->success()
                            ->send();

                        // Redirect to preview page
                        return redirect()->to(
                            PesertaResource::getUrl('preview-certificate', ['record' => $record->id])
                        );
                    }),

                Action::make('lihat_sertifikat')
                    ->label('Lihat Sertifikat')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(function ($record) {
                        $internship = $record->internship;
                        return $internship && $internship->certificate;
                    })
                    ->url(fn ($record) => PesertaResource::getUrl('preview-certificate', ['record' => $record->id])),
            ])
            ->bulkActions([
                BulkAction::make('generate_sertifikat_bulk')
                    ->label('Generate Sertifikat')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Sertifikat')
                    ->modalDescription('Sertifikat akan di-generate untuk semua peserta yang dipilih dan belum memiliki sertifikat. Lanjutkan?')
                    ->action(function (Collection $records) {
                        $generated = 0;
                        $skipped = 0;

                        foreach ($records as $record) {
                            $internship = $record->internship;

                            if (!$internship || $internship->certificate) {
                                $skipped++;
                                continue;
                            }

                            $lastCert = Certificate::orderBy('id', 'desc')->first();
                            $lastNumber = 0;
                            if ($lastCert && preg_match('/^B-(\d+)/', $lastCert->certificate_number, $matches)) {
                                $lastNumber = (int) $matches[1];
                            }
                            $newNumber = $lastNumber + 1;
                            $year = Carbon::now()->year;
                            $certificateNumber = 'B-' . $newNumber . '/33210/HM.340/' . $year;

                            Certificate::create([
                                'internship_id' => $internship->id,
                                'uuid' => Str::uuid(),
                                'certificate_number' => $certificateNumber,
                                'program_studi' => $internship->program_studi ?? '-',
                                'fakultas' => $internship->fakultas ?? '-',
                                'nim' => $internship->nim ?? '-',
                                'predikat' => 'SANGAT BAIK',
                                'certificate_date' => Carbon::today(),
                            ]);

                            $generated++;
                        }

                        Notification::make()
                            ->title("Sertifikat di-generate: {$generated}, Dilewati: {$skipped}")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    /**
     * Filter query: hanya user dengan role Magang BPS atau Alumni Magang
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Magang BPS', 'Alumni Magang']);
            })
            ->with(['internship.certificate']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeserta::route('/'),
            'preview-certificate' => Pages\PreviewCertificate::route('/{record}/preview-certificate'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
