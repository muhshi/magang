<?php

namespace App\Filament\Resources\PesertaResource\Pages;

use App\Filament\Resources\PesertaResource;
use App\Models\Certificate;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;

class PreviewCertificate extends Page
{
    protected static string $resource = PesertaResource::class;
    protected string $view = 'filament.resources.peserta-resource.pages.preview-certificate';
    protected static ?string $title = 'Preview Sertifikat';

    public $record;
    public ?Certificate $certificate = null;
    public ?string $pdfUrl = null;

    public function mount(int|string $record): void
    {
        $this->record = User::findOrFail($record);

        $internship = $this->record->internship;
        if ($internship && $internship->certificate) {
            $this->certificate = $internship->certificate;
            $this->pdfUrl = route('certificate.generate', $this->certificate->id);
        }

        static::authorizeResourceAccess();
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if ($this->certificate) {
            $actions[] = Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(route('certificate.download', $this->certificate->uuid))
                ->openUrlInNewTab();

            $actions[] = Actions\Action::make('edit_sertifikat')
                ->label('Edit Sertifikat')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->form([
                    Section::make('Data Peserta')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('full_name')
                                ->label('Nama Lengkap')
                                ->default($this->record->internship->full_name ?? '')
                                ->required(),

                            TextInput::make('school_name')
                                ->label('Universitas')
                                ->default($this->record->internship->school_name ?? '')
                                ->required(),

                            TextInput::make('program_studi')
                                ->label('Program Studi')
                                ->default($this->certificate->program_studi)
                                ->required(),

                            TextInput::make('fakultas')
                                ->label('Fakultas')
                                ->default($this->certificate->fakultas)
                                ->required(),

                            TextInput::make('nim')
                                ->label('NIM')
                                ->default($this->certificate->nim)
                                ->required(),

                            DatePicker::make('start_date')
                                ->label('Periode Mulai')
                                ->default($this->record->internship->start_date ?? null)
                                ->required(),

                            DatePicker::make('end_date')
                                ->label('Periode Selesai')
                                ->default($this->record->internship->end_date ?? null)
                                ->required(),
                        ]),
                    ]),

                    Section::make('Data Sertifikat')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('certificate_number')
                                ->label('Nomor Sertifikat')
                                ->default($this->certificate->certificate_number)
                                ->required(),

                            Select::make('predikat')
                                ->label('Predikat')
                                ->options([
                                    'SANGAT BAIK' => 'SANGAT BAIK',
                                    'BAIK' => 'BAIK',
                                    'CUKUP' => 'CUKUP',
                                    'KURANG' => 'KURANG',
                                ])
                                ->default($this->certificate->predikat)
                                ->required(),

                            DatePicker::make('certificate_date')
                                ->label('Tanggal Sertifikat')
                                ->default($this->certificate->certificate_date)
                                ->required(),
                        ]),
                    ]),
                ])
                ->action(function (array $data): void {
                    $internship = $this->record->internship;

                    // Update internship data
                    $internship->update([
                        'full_name' => $data['full_name'],
                        'school_name' => $data['school_name'],
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                    ]);

                    // Update certificate data
                    $this->certificate->update([
                        'certificate_number' => $data['certificate_number'],
                        'predikat' => $data['predikat'],
                        'program_studi' => $data['program_studi'],
                        'fakultas' => $data['fakultas'],
                        'nim' => $data['nim'],
                        'certificate_date' => $data['certificate_date'],
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Data sertifikat berhasil diperbarui')
                        ->success()
                        ->send();

                    $this->js('window.location.reload()');
                });
        }

        $actions[] = Actions\Action::make('kembali')
            ->label('Kembali')
            ->icon('heroicon-o-arrow-left')
            ->color('gray')
            ->url(PesertaResource::getUrl());

        return $actions;
    }
}
