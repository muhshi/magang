<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternshipResource\Pages;
use App\Filament\Resources\InternshipResource\RelationManagers;
use App\Models\Internship;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InternshipResource extends Resource
{
    protected static ?string $model = Internship::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-identification';

    protected static ?string $label = "Pendaftaran Magang";

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Pribadi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('full_name')
                                    ->label('Nama Lengkap')
                                    ->required(),

                                TextInput::make('birth_place')
                                    ->label('Tempat Lahir')
                                    ->required(),

                                DatePicker::make('birth_date')
                                    ->label('Tanggal Lahir')
                                    ->native(false)
                                    ->required(),

                                Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->required(),

                                TextInput::make('phone')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->required(),

                                TextInput::make('email')
                                    ->label('Email Aktif')
                                    ->email()
                                    ->required(),
                            ]),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make('Data Pendidikan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('school_name')
                                    ->label('Asal Instansi')
                                    ->required(),

                                Select::make('education_level')
                                    ->label('Tingkat Pendidikan')
                                    ->options([
                                        'SMA' => 'SMA',
                                        'SMK' => 'SMK',
                                        'D3' => 'Diploma (D3)',
                                        'S1' => 'Sarjana (S1)',
                                        'S2' => 'Magister (S2)',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        if (in_array($state, ['SMA', 'SMK'])) {
                                            $set('program_studi', null);
                                            $set('fakultas', null);
                                        }
                                    })
                                    ->required(),

                                TextInput::make('jurusan')
                                    ->label('Jurusan')
                                    ->required(),

                                TextInput::make('program_studi')
                                    ->label('Program Studi')
                                    ->disabled(fn (Get $get): bool => in_array($get('education_level'), ['SMA', 'SMK']))
                                    ->required(fn (Get $get): bool => !in_array($get('education_level'), ['SMA', 'SMK'])),

                                TextInput::make('fakultas')
                                    ->label('Fakultas')
                                    ->disabled(fn (Get $get): bool => in_array($get('education_level'), ['SMA', 'SMK']))
                                    ->required(fn (Get $get): bool => !in_array($get('education_level'), ['SMA', 'SMK'])),

                                TextInput::make('nim')
                                    ->label(fn (Get $get): string => in_array($get('education_level'), ['SMA', 'SMK']) ? 'NISN' : 'NIM')
                                    ->required(),

                                DatePicker::make('start_date')
                                    ->label('Durasi Magang (Mulai)')
                                    ->native(false)
                                    ->required(),

                                DatePicker::make('end_date')
                                    ->label('Durasi Magang (Selesai)')
                                    ->native(false)
                                    ->required(),
                            ]),
                    ]),

                Section::make('Dokumen Pendukung')
                    ->schema([
                        AdvancedFileUpload::make('letter_file')
                            ->label('Surat Pengantar (PDF/DOC)')
                            ->pdfPreviewHeight(400)
                            ->pdfDisplayPage(1)
                            ->pdfToolbar(true)
                            ->pdfZoomLevel(100)
                            ->pdfFitType(PdfViewFit::FIT)
                            ->pdfNavPanes(true)
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->disk('public')
                            ->directory('magang/letter')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->previewable(true)
                            ->downloadable()
                            ->required(),

                        FileUpload::make('photo_file')
                            ->label('Foto (JPG/PNG, Maks. 3MB)')
                            ->image()
                            ->maxSize(3072)
                            ->disk('public')
                            ->directory('magang/photo')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->downloadable()
                            ->required()
                    ]),

                Section::make('Lain-lain')
                    ->schema([
                        Textarea::make('motivation')
                            ->label('Motivasi atau Alasan Mengajukan Magang')
                            ->columnSpanFull(),

                        Textarea::make('skills')
                            ->label('Keterampilan yang Dimiliki')
                            ->columnSpanFull(),
                    ]),
                Section::make('Approval Admin') // Ganti nama section agar lebih jelas
                    ->schema([
                        Select::make('status')
                            ->options([
                                'accepted' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->live(), // <--- Tambahkan ini untuk membuat form reaktif

                        Textarea::make('note')
                            ->label('Catatan (Opsional)')
                            ->columnSpanFull(),

                        AdvancedFileUpload::make('acceptance_letter_file')
                            ->label('Upload Surat Penerimaan')
                            ->pdfPreviewHeight(400)
                            ->pdfDisplayPage(1)
                            ->pdfToolbar(true)
                            ->pdfZoomLevel(100)
                            ->pdfFitType(PdfViewFit::FIT)
                            ->pdfNavPanes(true)
                            ->disk('public')
                            ->directory('magang/acceptance-letters') // Simpan di direktori terpisah
                            ->visibility('private')
                            //->required(fn(Get $get): bool => $get('status') === 'accepted') // Wajib jika status 'accepted'
                            ->visible(fn(Get $get): bool => $get('status') === 'accepted'), // Muncul jika status 'accepted'

                    ])
                    ->hidden(Auth::user()->roles[0]->name !== 'super_admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $bpsTabs = ['magang_bps', 'alumni'];

        return $table
            ->columns([
                // === KOLOM NORMAL (Pending, Diterima, Ditolak, Semua) ===
                Tables\Columns\ImageColumn::make('photo_file')
                    ->label('Pas Foto')
                    ->size(45)
                    ->circular()
                    ->disk('public')
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                TextColumn::make('gender')
                    ->label('Gender')
                    ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable()
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                Tables\Columns\TextColumn::make('school_name')
                    ->label('Asal Instansi')
                    ->searchable()
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                Tables\Columns\TextColumn::make('education_level')
                    ->label('Jenjang')
                    ->alignCenter()
                    ->searchable()
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                TextColumn::make('durasi')
                    ->label('Durasi')
                    ->alignCenter()
                    ->getStateUsing(fn($record) => \Carbon\Carbon::parse($record->start_date)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($record->end_date)->translatedFormat('d M Y'))
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                // === KOLOM KHUSUS (Magang BPS & Alumni) ===
                TextColumn::make('user.name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable()
                    ->visible(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                TextColumn::make('school_name')
                    ->label('Asal Instansi')
                    ->searchable()
                    ->visible(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                TextColumn::make('program_studi')
                    ->label('Program Studi')
                    ->visible(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                TextColumn::make('periode_magang')
                    ->label('Periode Magang')
                    ->getStateUsing(function ($record) {
                        if (!$record->start_date || !$record->end_date) return '-';
                        return \Carbon\Carbon::parse($record->start_date)->translatedFormat('d M Y')
                            . ' - ' . \Carbon\Carbon::parse($record->end_date)->translatedFormat('d M Y');
                    })
                    ->visible(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                TextColumn::make('user.roles.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Magang BPS' => 'success',
                        'Alumni Magang' => 'info',
                        default => 'gray',
                    })
                    ->visible(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                TextColumn::make('sertifikat_status')
                    ->label('Sertifikat')
                    ->getStateUsing(fn ($record) => $record->certificate ? 'Sudah dibuat' : 'Belum dibuat')
                    ->badge()
                    ->color(fn ($state) => $state === 'Sudah dibuat' ? 'success' : 'warning')
                    ->visible(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->hidden(fn ($livewire) => in_array($livewire->activeTab ?? null, $bpsTabs)),

                EditAction::make()
                    ->button()
                    ->label(function (Internship $record): string {
                        if (Auth::user()->roles[0]->name === 'super_admin') {
                            if ($record->status === 'accepted') return 'Accepted';
                            if ($record->status === 'rejected') return 'Rejected';
                        }
                        return 'Approval';
                    })
                    ->color(function (Internship $record): string {
                        if (Auth::user()->roles[0]->name === 'super_admin') {
                            if ($record->status === 'accepted') return 'success';
                            if ($record->status === 'rejected') return 'danger';
                        }
                        return 'primary';
                    })
                    ->visible(function (Internship $record, $livewire): bool {
                        if (in_array($livewire->activeTab ?? null, ['magang_bps', 'alumni'])) return false;
                        if (Auth::user()->roles[0]->name === 'super_admin') return true;
                        return $record->status === 'pending';
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * PERBAIKAN: Tambahkan logika query berdasarkan peran pengguna.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Jika user BUKAN super_admin...
        if (Auth::user()->roles[0]->name !== 'super_admin') {
            // ...tampilkan hanya pendaftaran miliknya sendiri.
            $query->where('user_id', Auth::user()->id);
        }

        // Kembalikan query yang sudah dimodifikasi.
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternships::route('/'),
            'create' => Pages\CreateInternship::route('/create'),
            'view' => Pages\ViewInternship::route('/{record}'),
            'edit' => Pages\EditInternship::route('/{record}/edit'),
        ];
    }
}
