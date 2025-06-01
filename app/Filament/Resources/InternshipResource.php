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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
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

    protected static ?string $navigationIcon = 'heroicon-s-identification';

    public static function form(Form $form): Form
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
                                    ->label('Nama Sekolah/Universitas')
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
                            ->directory('magang/photo')
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
                Section::make()
                    ->schema(
                        [
                            Select::make('status')
                                ->options([
                                    'accepted' => 'Approved',
                                    'rejected' => 'Rejected',
                                ]),
                            Textarea::make('note')->columnSpanFull(),
                        ]
                    )
                    ->hidden(Auth::user()->roles[0]->name !== 'super_admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                //if(Auth::user()->hasRole('Doctor')) -> pakai ini bisa tapi terdeteksi error sama intelephense
                if (Auth::user()->roles[0]->name != 'super_admin') {
                    $query->where('user_id', Auth::user()->id);
                }
            })
            ->columns([
                Tables\Columns\ImageColumn::make('photo_file')
                    ->label('Pas Foto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('school_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('education_level')
                    ->alignCenter()
                    ->searchable(),
                TextColumn::make('durasi')
                    ->label('Durasi')
                    ->alignCenter()
                    ->getStateUsing(fn($record) => \Carbon\Carbon::parse($record->start_date)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($record->end_date)->translatedFormat('d M Y')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                EditAction::make()
                    ->label('Approval')
                    ->button()
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListInternships::route('/'),
            'create' => Pages\CreateInternship::route('/create'),
            'view' => Pages\ViewInternship::route('/{record}'),
            'edit' => Pages\EditInternship::route('/{record}/edit'),
        ];
    }
}
