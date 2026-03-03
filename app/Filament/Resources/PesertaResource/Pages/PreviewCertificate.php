<?php

namespace App\Filament\Resources\PesertaResource\Pages;

use App\Filament\Resources\PesertaResource;
use App\Models\Certificate;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;

class PreviewCertificate extends Page
{
    protected static string $resource = PesertaResource::class;
    protected static string $view = 'filament.resources.peserta-resource.pages.preview-certificate';
    protected static ?string $title = 'Preview Sertifikat';

    public $record;
    public ?Certificate $certificate = null;

    public function mount(int|string $record): void
    {
        // Bypass Filament's query filter — just find the user directly
        $this->record = User::findOrFail($record);

        $internship = $this->record->internship;
        if ($internship) {
            $this->certificate = $internship->certificate;
        }

        // Skip Filament authorization for this custom page
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

                        DatePicker::make('certificate_date')
                            ->label('Tanggal Sertifikat')
                            ->default($this->certificate->certificate_date)
                            ->required(),
                    ]),
                ])
                ->action(function (array $data): void {
                    $this->certificate->update($data);

                    \Filament\Notifications\Notification::make()
                        ->title('Sertifikat berhasil diperbarui')
                        ->success()
                        ->send();

                    $this->redirect(request()->url());
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
