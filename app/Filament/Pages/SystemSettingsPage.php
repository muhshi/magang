<?php

namespace App\Filament\Pages;

use App\Settings\SystemSettings;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ViewField;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SystemSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'system-settings';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | \UnitEnum | null $navigationGroup = 'Manajemen Presensi';
    protected static ?string $title = 'Pengaturan Sistem';
    protected string $view = 'filament.pages.system-settings-page';
    protected static ?int $navigationSort = 70;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }

    public ?array $data = [];

    public function mount(SystemSettings $settings): void
    {
        $this->form->fill([
            'default_office_name'        => $settings->default_office_name ?? 'BPS Kabupaten Demak',
            'default_office_lat'         => $settings->default_office_lat ?? -6.894561,
            'default_office_lng'         => $settings->default_office_lng ?? 110.637492,
            'default_geofence_radius_m'  => $settings->default_geofence_radius_m ?? 100,
            'default_work_start'         => $settings->default_work_start ?? '08:00',
            'default_work_end'           => $settings->default_work_end ?? '16:00',
            'default_workdays'           => $settings->default_workdays ?? [1, 2, 3, 4, 5],
            'certificate_template_path'  => $settings->certificate_template_path ?? 'images/TEMPLATE.png',
            'certificate_pdf_password'   => $settings->certificate_pdf_password ?? 'demak3321',
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Group::make()->schema([ // ====== KOLOM KIRI
                Section::make('Lokasi Kantor Default')->schema([
                    TextInput::make('default_office_name')
                        ->label('Nama Kantor')
                        ->required()
                        ->maxLength(100),

                    Map::make('default_location')
                        ->label('Location')
                        ->columnSpanFull()
                        ->default(fn () => [
                            'lat' => $this->data['default_office_lat'] ?? -6.894561,
                            'lng' => $this->data['default_office_lng'] ?? 110.637492,
                        ])
                        ->afterStateUpdated(function (Set $set, ?array $state): void {
                            if (! $state) return;
                            $set('default_office_lat', $state['lat']);
                            $set('default_office_lng', $state['lng']);
                        })
                        ->afterStateHydrated(function ($state, $record, Set $set, Get $get): void {
                            $lat = $get('default_office_lat');
                            $lng = $get('default_office_lng');
                            if ($lat !== null && $lng !== null) {
                                $set('default_location', ['lat' => (float) $lat, 'lng' => (float) $lng]);
                            }
                        })
                        ->liveLocation()
                        ->showMarker()
                        ->markerColor('#22c55e')
                        ->showFullscreenControl()
                        ->showZoomControl()
                        ->draggable()
                        ->tilesUrl("http://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}&s=Ga")
                        ->zoom(16)
                        ->detectRetina(),

                    Group::make()->schema([
                        TextInput::make('default_office_lat')
                            ->label('Latitude')
                            ->required()
                            ->numeric(),
                        TextInput::make('default_office_lng')
                            ->label('Longitude')
                            ->required()
                            ->numeric(),
                    ])->columns(2),

                    TextInput::make('default_geofence_radius_m')
                        ->label('Radius Geofence')
                        ->numeric()
                        ->minValue(10)
                        ->required()
                        ->suffix('m'),
                ]),
            ])->columnSpan(1),

            Group::make()->schema([ // ====== KOLOM KANAN
                Section::make('Jam & Hari Kerja Default')->schema([
                    TimePicker::make('default_work_start')
                        ->label('Mulai')
                        ->seconds(false)
                        ->required(),
                    TimePicker::make('default_work_end')
                        ->label('Selesai')
                        ->seconds(false)
                        ->required(),
                    CheckboxList::make('default_workdays')
                        ->label('Hari Kerja')
                        ->columns(4)
                        ->required()
                        ->options([
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                            7 => 'Minggu',
                        ]),
                ])->columns(1),

                Section::make('Template Sertifikat')->schema([
                    FileUpload::make('certificate_template_upload')
                        ->label('Upload Template Baru')
                        ->helperText('Upload gambar JPG/PNG untuk background sertifikat (landscape A4). Kosongkan jika tidak ingin mengubah.')
                        ->acceptedFileTypes(['image/jpeg', 'image/png'])
                        ->directory('certificate-templates')
                        ->disk('public')
                        ->image()
                        ->imagePreviewHeight('200')
                        ->maxSize(5120),
                    ViewField::make('certificate_template_preview')
                        ->label('Template Aktif')
                        ->view('filament.forms.certificate-template-preview'),
                    TextInput::make('certificate_pdf_password')
                        ->label('Password PDF Sertifikat')
                        ->helperText('Password untuk melindungi PDF dari pengeditan. PDF tetap bisa dibuka dan dicetak tanpa password.')
                        ->required()
                        ->password()
                        ->revealable(),
                ])->columns(1),
            ])->columnSpan(1),

        ])
            ->columns(2)
            ->statePath('data');
    }

    public function save(): void
    {
        $state = collect($this->form->getState())
            ->except(['default_location', 'certificate_template_upload', 'certificate_template_preview'])
            ->toArray();

        if (strcmp($state['default_work_start'], $state['default_work_end']) >= 0) {
            Notification::make()->title('Jam mulai harus < jam selesai')->danger()->send();
            return;
        }

        $settings = app(SystemSettings::class);

        // Handle certificate template upload
        $formState = $this->form->getState();
        $uploadedFile = $formState['certificate_template_upload'] ?? null;

        if ($uploadedFile) {
            // Uploaded file path is relative to public disk
            $newPath = is_array($uploadedFile) ? reset($uploadedFile) : $uploadedFile;

            // Delete old template if it's not the default
            $oldPath = $settings->certificate_template_path;
            if ($oldPath && $oldPath !== 'images/TEMPLATE.png' && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $state['certificate_template_path'] = $newPath;
        }

        $changed = [];
        foreach ($state as $key => $value) {
            if (! property_exists($settings, $key)) {
                continue;
            }
            if ($settings->{$key} != $value) {
                $changed[$key] = $value;
            }
        }

        if (empty($changed)) {
            Notification::make()->title('Tidak ada perubahan')->info()->send();
            return;
        }

        $settings->fill($changed)->save();

        Notification::make()->title('Pengaturan tersimpan')->success()->send();
    }
}
