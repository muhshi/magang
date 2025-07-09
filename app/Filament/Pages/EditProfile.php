<?php

namespace App\Filament\Pages;

use App\Models\Profile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.edit-profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'My Profile';

    // Properti untuk menampung data form.
    // 'data' untuk form profil, 'passwordData' untuk form password.
    public ?array $data = [];
    public ?array $passwordData = [];

    // Properti untuk menampung instance model Profile
    public ?Profile $profile;

    /**
     * Method ini dijalankan saat halaman pertama kali dibuka.
     * Tugasnya adalah memuat data yang ada ke dalam form.
     */
    public function mount(): void
    {
        // 1. Ambil atau buat record profil untuk user yang sedang login.
        $this->profile = Auth::user()->profile()->firstOrCreate([]);

        // 2. Isi form profil dengan data dari model Profile.
        //    Filament akan otomatis mengisi semua field, termasuk menampilkan foto.
        $this->profileForm->fill($this->profile->toArray());

        // 3. Kosongkan form password.
        $this->passwordForm->fill();
    }

    /**
     * Daftarkan semua form yang ada di halaman ini.
     * Ini wajib agar Filament mengenali '$this->profileForm' dan '$this->passwordForm'.
     */
    protected function getForms(): array
    {
        return [
            'profileForm',
            'passwordForm',
        ];
    }

    /**
     * Mendefinisikan form untuk data profil.
     */
    public function profileForm(Form $form): Form
    {
        return $form
            // KUNCI UTAMA: Ikat form ini ke model Profile.
            // Filament akan otomatis menangani load & save data.
            ->model($this->profile)
            ->statePath('data') // Arahkan data ke properti $data
            ->schema([
                Section::make('Foto Profil')
                    ->schema([
                        FileUpload::make('photo')
                            ->label('Photo [Digunakan untuk sertifikat]')
                            ->image()
                            ->directory('profile-photos')
                            ->imagePreviewHeight('250')
                            ->helperText('Unggah pas foto 4x6 resmi dengan latar belakang merah atau biru.')
                            // Mengatur agar foto berada di tengah
                            ->alignCenter()
                            // Mengambil lebar penuh
                            ->columnSpanFull(),
                    ]),
                Section::make('Informasi Detail')
                    ->schema([
                        TextInput::make('school_name')
                            ->label('Nama Universitas/Sekolah')
                            ->required(),
                        TextInput::make('phone_number')
                            ->label('Nomor Telepon')
                            ->tel(),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(), // Alamat mengambil lebar penuh
                        Textarea::make('bio')
                            ->label('Bio Singkat')
                            ->rows(3)
                            ->columnSpanFull(), // Bio mengambil lebar penuh
                    ])->columns(2), // Section ini tetap menggunakan 2 kolom
            ]);
    }

    /**
     * Method untuk menyimpan data dari form profil.
     */
    public function saveProfile(): void
    {
        // Ambil data yang sudah divalidasi dari form
        $data = $this->profileForm->getState();

        // Update model Profile dengan data baru
        $this->profile->update($data);

        Notification::make()->title('Profil berhasil disimpan!')->success()->send();
        // Refresh halaman untuk memastikan semua data tampil baru.
        $this->redirect(static::getUrl(), navigate: true);
    }

    /**
     * Mendefinisikan form untuk ganti password.
     * Form ini tidak diikat ke model, jadi kita tangani secara manual.
     */
    public function passwordForm(Form $form): Form
    {
        return $form->schema([
            Section::make('Update Password')
                ->description('Pastikan akun Anda menggunakan password yang panjang dan acak.')
                ->schema([
                    TextInput::make('current_password')->password()->required()->currentPassword(),
                    TextInput::make('password')->label('New Password')->password()->required()->confirmed(),
                    TextInput::make('password_confirmation')->label('Confirm Password')->password()->required(),
                ])->columns(2),
        ])->statePath('passwordData');
    }

    /**
     * Method untuk menyimpan password baru.
     */
    public function savePassword(): void
    {
        $data = $this->passwordForm->getState();
        Auth::user()->update(['password' => Hash::make($data['password'])]);

        $this->passwordForm->fill();
        Notification::make()->title('Password berhasil diperbarui!')->success()->send();
    }
}
