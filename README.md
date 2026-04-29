# Mangga Muda (Manajemen Magang Terintegrasi dan Mudah)

![image](https://raw.githubusercontent.com/SeptiawanAjiP/dewakoding-project-management/refs/heads/main/image-1.jpeg)
![image](https://raw.githubusercontent.com/SeptiawanAjiP/dewakoding-project-management/refs/heads/main/image-4.jpeg)
![image](https://raw.githubusercontent.com/SeptiawanAjiP/dewakoding-project-management/refs/heads/main/image-5.jpeg)

A Laravel Filament 3 application for managing projects with ticket management and status tracking.

## Features

- Project management with ticket prefix configuration
- Team member management with role assignments
- Customizable ticket statuses with color coding
- Ticket management with assignees and due dates
- Unique ticket identifiers (PROJECT-XXXXXX format)
- Epic management for organizing tickets into larger initiatives
- Comment system for tickets to facilitate team discussions
- Kanban board view for visualizing ticket progress

## Requirements

- PHP 8.4+
- Laravel 13
- MySQL 8.0+ / PostgreSQL 12+
- Composer

![image](https://raw.githubusercontent.com/SeptiawanAjiP/dewakoding-project-management/refs/heads/main/image-2.jpeg)
![image](https://raw.githubusercontent.com/SeptiawanAjiP/dewakoding-project-management/refs/heads/main/image-6.jpeg)


## Installation

1. Clone the repository:
   ```
   git clone https://github.com/SeptiawanAjiP/dewakoding-project-management
   cd dewakoding-project-management
   ```

2. Install dependencies:
   ```
   composer install
   npm install
   ```

3. Set up environment:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database in `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dewakoding_project_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations:
   ```
   php artisan migrate
   ```

6. Create storage link for file uploads
   ```
   php artisan storage:link
   ```

7. Create a Filament admin user:
   ```
   php artisan make:filament-user
   ```
8. Activate Role & Permission
   ```
   php artisan shield:setup
   php artisan shield:install
   php artisan shield:super-admin
   ```

9. Start the development server:
   ```
   php artisan serve
   ```

## Usage

1. Access the Filament admin panel at `http://localhost:8000/admin`
2. Log in with the Filament user credentials you created
3. Create a new project with custom ticket prefix
4. Add team members to the project
5. Create and customize ticket statuses
6. Add tickets and assign to team members

## Main Features

### Board View

The Board View offers a familiar kanban-style interface for ticket management:

- Drag-and-drop tickets between status columns
- Customize columns to match your team's process
- Quick-edit functionality for updating tickets directly from the board

### Timeline View

The Timeline feature provides a chronological perspective of your project work:

- Visualize project roadmap with start and end dates
- Track milestone completion across time periods
- Easily identify scheduling conflicts or resource bottlenecks

### Epic Management

Epics help organize related tickets into larger initiatives:

- Group tickets by feature, release, or business objective
- Track progress across multiple tickets
- Set start and end dates for planning purposes
- Visualize which tickets belong to which initiatives

### Ticket Comments

The comment system enhances team collaboration:

- Team members can discuss tickets directly in the application
- All comments are timestamped and attributed to users
- Supports rich text formatting for improved readability
- Enables better context sharing and decision documentation

## Running with Laravel Octane and FrankenPHP

This project comes pre-configured with Laravel Octane and FrankenPHP for improved performance. Here's how to use it:

### Prerequisites

The required packages are already included in the project dependencies:
- `laravel/octane` (in composer.json)
- `chokidar` (in package.json for file watching)

They will be installed automatically when you run `composer install` and `npm install` during the standard installation process.

### Running the Application

#### Development Mode

To run the application in development mode with auto-reloading, simply use the provided composer script:
   ```
   php artisan octane:start --server=frankenphp --watch
   ```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Changelog

### 2 Maret 2026
- **Fitur Sertifikat Magang**: Form isian, generate PDF, QR code verifikasi, halaman publik verifikasi & download
- **Custom Fonts**: Anton (judul), Oleo Script (nama peserta), Montserrat (body) untuk template sertifikat
- **Dashboard Peserta Magang**: Stats overview (sisa magang, hadir, izin/cuti, alpha, penugasan aktif, progress), quick actions, tabel penugasan aktif, tabel logbook terbaru
- **Role Permission**: Seeder untuk Calon Magang, Magang BPS, Alumni Magang. Auto remove "Calon Magang" saat approve internship
- **Magang BPS Permission**: Presensi, cuti, view sertifikat (create sertifikat hanya super_admin)

### 3 Maret 2026
- **Resource Peserta**: Daftar user Magang BPS + Alumni dengan tabs filter dan badge count
- **Auto-Generate Sertifikat**: One-click generate dari data pendaftaran (nomor sequential, predikat default SANGAT BAIK), bulk action tersedia
- **Preview Sertifikat**: Halaman preview dengan embedded PDF iframe + data peserta & sertifikat
- **Edit Semua Field**: Modal edit mencakup data peserta (nama, universitas, prodi, fakultas, NIM, periode) dan sertifikat (nomor, predikat, tanggal)
- **Download PDF**: Tombol download terpisah dari preview
- **Form Pendaftaran**: Tambah field program studi, fakultas, NIM di section Data Pendidikan
- **Tabs Peserta**: Filter Magang BPS (aktif) dan Alumni (durasi selesai) dengan badge jumlah
- **Hapus Section Sertifikat**: CertificateResource dihilangkan dari sidebar, diganti resource Peserta

### 4 Maret 2026
- **Dynamic Education Fields**: Field program studi & fakultas otomatis disabled saat tingkat pendidikan SMA/SMK dipilih, label NIM/NISN berubah dinamis sesuai tingkat pendidikan
- **Field Jurusan**: Tambah field Jurusan di section Data Pendidikan (wajib diisi untuk semua tingkat pendidikan)

### 9 Maret 2026
- **Fitur Chat User ↔ Admin**: Floating chat widget di kiri bawah semua halaman (Filament + Landing Page), user bisa langsung chat ke admin
- **Admin Chat Inbox**: Halaman khusus super admin (`/admin/chat`) dengan daftar percakapan dan detail chat split-view
- **File Attachment**: Kirim file/gambar di chat, preview gambar langsung di bubble chat
- **Polling 5 Detik**: Auto-refresh pesan baru setiap 5 detik via Livewire polling
- **Unread Indicator**: Dot indicator di inbox admin
- **Auto Retention 60 Hari**: Scheduled command `chat:prune` hapus pesan lama otomatis setiap hari
- **Responsive Design**: Chat widget responsive untuk mobile, admin inbox stack layout di layar kecil

### 10 Maret 2026
- **Hapus Badge Unread Chat**: Menghapus badge angka unread di sidebar admin Chat karena logika read/unread tidak akurat
- **Hapus Badge Count Pesan Masuk**: Menghapus badge angka di samping teks "Pesan Masuk" pada halaman chat inbox admin
- **Register EmployeeSeeder**: Mendaftarkan `EmployeeSeeder` di `DatabaseSeeder` agar data 44 pegawai BPS otomatis terisi saat `php artisan db:seed`
- **Upload Template Sertifikat**: Fitur upload gambar template sertifikat (JPG/PNG) di Pengaturan Sistem, background sertifikat PDF sekarang dinamis dari setting
- **Ubah Judul Utama**: Mengubah judul aplikasi di README menjadi Mangga Muda (Manajemen Magang Terintegrasi dan Mudah)
- **Replace README**: Menggunakan versi README.md dari branch `nana-work`
- **Fix Docker Configuration**: Mengembalikan file `docker-compose.yml` untuk portainer (magang-franken) yang sempat tertimpa versi Sail, serta memperbaiki `Dockerfile` (menghapus trailing spaces dan menyesuaikan ekstensi PCNTL, Zip, Exif)
- **Fix Presensi Error**: Menyelesaikan issue Class "Spatie\LaravelSettings\Settings" not found pada sisi server dengan `composer install` dan `optimize:clear`, serta memperbaiki error Leaflet map `addLayer` pada halaman presensi dan mengganti CDN Tailwind dengan Vite.

### 11 Maret 2026
- **Password Protection PDF Sertifikat**: PDF sertifikat dilindungi owner password mencegah pengeditan, tetap bisa dibuka dan dicetak tanpa password. Password default `demak3321`, bisa diubah di Pengaturan Sistem
- **Fix Invalid Date Penugasan**: Memperbaiki error `Carbon\InvalidFormatException` saat buka detail penugasan data lama yang tanggalnya berisi "-". Migration membersihkan data + kode defensive untuk mencegah crash di masa depan
- **Redesign Halaman Presensi**: UI baru dengan gradient header, info grid, time cards dengan icon, tombol modern dengan hover effect, dan dukungan dark mode. Matching tema Filament
- **Fix Peserta Tidak Muncul**: Menghapus filter `whereHas('internship')` dari tab Magang BPS di Daftar Peserta, sehingga semua user dengan role Magang BPS tampil meskipun belum punya data internship

### 13 Maret 2026
- **Merge Branch**: Melakukan pull dan merge dari branch `nana-work`.
- **Fix Migration Date**: Menambahkan try-catch pada migration update `start_date` dan `due_date` menjadi null untuk mencegah error mode strict MySQL mengenai Invalid datetime format saat value '-'.
- **Optimasi Layout Presensi Mobile**: Menyesuaikan padding, margin, font size, dimensi ikon, dan tinggi peta agar presensi dapat tampil presisi di layar HP tanpa scroll berlebih.

### 16 Maret 2026
- **Update Landing Page**: Mengubah warna teks "Manajemen Magang Terintegrasi dan Mudah" pada teks header di halaman utama menjadi oranye (menggunakan warna aksen template).

### 28 Maret 2026
- **Tambah Tab Magang BPS & Alumni di Pendaftaran Magang**: Halaman Pendaftaran Magang kini memiliki 6 tab — Pending, Diterima, Ditolak, Magang BPS, Alumni, dan Semua. Tab Magang BPS dan Alumni menampilkan kolom khusus (Nama Peserta, Universitas, Program Studi, Periode Magang, Status, Status Sertifikat) dan bersifat read-only tanpa tombol aksi. Tab lain tetap menampilkan kolom pendaftaran dan tombol aksi seperti sebelumnya.
- **Fix Download Presensi**: Memperbaiki tombol "Download Data" pada halaman Presensi Admin yang menampilkan simbol/karakter acak. Ganti implementasi dari `Excel::download()` ke `response()->streamDownload()` + `Excel::raw()` agar kompatibel dengan Octane/FrankenPHP. Ditambahkan pengecekan data kosong — jika belum ada data presensi, tampilkan notifikasi peringatan "Data presensi masih kosong" alih-alih mengunduh file kosong.
- **Fitur Rekapitulasi Presensi**: Menu baru di grup "Manajemen Presensi" khusus super admin. Berisi tabel rekap per-peserta (Total Hadir, Tepat Waktu, Terlambat, Sisa Magang) dengan filter dropdown per-bulan (12 bulan terakhir), serta diagram batang Chart.js interaktif di bawah tabel yang auto-refresh saat bulan berubah.

### 31 Maret 2026
- **Merge Branch nana-work**: Menggabungkan fitur Task Management (pengganti manajemen project sebelumnya/simplify), System Settings, dan pembaruan UI (style presensi), serta fitur Integrasi API Hari Libur Nasional.
- **Fix Server Listen Error**: Memperbaiki issue `php artisan serve` dengan menyesuaikan konfigurasi environment variabel.
- **Fix Bug Bulan Presensi**: Mengubah penggunaan fungsi `subMonths()` menjadi `startOfMonth()->subMonths()` serta mengatur pembacaan string tanggal ke tanggal 1 (`Y-m-d`) pada view untuk mengatasi bug *overflow* pada bulan dengan 31 hari. Bug ini sebelumnya menyebabkan bulan Februari menghilang di dropdown filter dan menampilkan nama bulan yang salah pada judul tabel rekapitulasi.
- **Integrasi API Hari Libur Nasional**: Mengintegrasikan API `libur.deno.dev` untuk mengambil data hari libur nasional Indonesia. Data di-cache selama 30 hari untuk performa optimal.
- **Perhitungan "Tidak Hadir" di Rekapitulasi Presensi**: Menambahkan kolom Hari Efektif, Tidak Hadir pada tabel rekap. Hari efektif dihitung dari hari kerja (Senin-Jumat) dikurangi hari libur nasional, dan di-clamp berdasarkan tanggal mulai/selesai magang per peserta. Tidak Hadir = Hari Efektif - Total Hadir.
- **Info Ringkasan Bulan**: Menampilkan summary card berisi total hari kerja efektif dan daftar hari libur nasional (beserta nama) yang jatuh pada bulan yang dipilih.
- **Update Chart**: Diagram batang per-peserta kini menampilkan 4 bar: Total Hadir, Tepat Waktu, Terlambat, dan Tidak Hadir (warna amber).
- **Google SSO Login**: Menambahkan fitur Login dengan Google (OAuth 2.0) di halaman login Filament Admin menggunakan `laravel/socialite`. User yang sudah terdaftar langsung login, user baru otomatis dibuatkan akun dengan role `Calon Magang`. Tombol "Masuk dengan Google" tampil di bawah form login biasa dengan divider.
- **Aksi Cepat Cuti**: Menambahkan Action Cepat pada tabel Daftar Cuti untuk role `super_admin` berupa tombol **Setujui** dan **Tolak**. Tombol ini muncul otomatis pada baris cuti yang bersatus `pending` untuk merubah status secara instan tanpa masuk form ubah data.
- **Pembaruan Rekapitulasi Presensi**: Menambahkan kolom baru **Cuti** yang menghitung absensi legal (Cuti Disetujui) pada hari kerja efektif yang beririsan dengan filter bulan. Label *Tidak Hadir* juga telah diganti menjadi **Tanpa Izin**, di mana jumlah "Tanpa Izin" sekarang menyesuaikan dengan rumus `max(0, Hari Efektif - (Total Hadir + Cuti))`. Bar Chart juga diperbarui untuk memuat data *Cuti*.

### 15 April 2026
- **Migrasi Filament v5**: Memperbarui namespace pada `GoogleLogin.php`, `InternshipResource.php`, `LeaveResource.php`, dan `ListInternships.php` untuk sesuai dengan arsitektur `Filament\Schemas` yang baru. Menggunakan `Filament\Actions` untuk aksi tabel dan `Filament\Schemas\Components` untuk komponen form.
- **Pembaruan Admin Panel**: Mengganti `MenuItem` yang telah didepresiasi dengan `Filament\Actions\Action` pada `AdminPanelProvider.php` sesuai standar terbaru.
- **Modernisasi Sertifikat PDF**: Memperbaiki peringatan IDE pada `CertificateController.php` dengan menambahkan pengecekan `method_exists` yang lebih aman saat mengakses CPDF canvas untuk enkripsi PDF, memastikan kompatibilitas dengan Dompdf v3.

### 16 April 2026
- **Merge Branch nana-work**: Melakukan pull dan merge terbaru dari branch `nana-work` yang mencakup perbaikan Filament v5, penghapusan regulasi & Google SSO, fix logout, dan optimasi widget.
- **Fix User Menu Error**: Memperbaiki `LogicException` dengan memberikan unique name pada `Action::make()` di `userMenuItems` `AdminPanelProvider.php`, sekaligus menyelesaikan *deprecation warning* dengan tetap menggunakan `Action` (menggantikan `MenuItem`).
- **Update Component Namespaces**: Menyelesaikan error IDE terkait "Use of unknown class" dengan menyesuaikan namespace `Section`, `Grid`, dan `Group` dari `Forms\Components` & `Infolists\Components` menjadi `Filament\Schemas\Components`, serta mengubah `Tables\Actions\Action` menjadi `\Filament\Actions\Action` sesuai arsitektur Filament v5.
- **Fix FullCalendarWidget Error**: Menginisialisasi properti `$record` pada `CalendarWidget.php` untuk mencegah error "Typed property $record must not be accessed before initialization" yang terjadi di PHP 8.4 saat memuat event pada Livewire update.
- **Restore Disappeared Widgets**: Meregenerasi seluruh *permissions* Filament Shield melalui sinkronisasi ulang agar widget-widget admin kembali muncul.
- **Remove Project Timeline**: Menghapus widget `ProjectTimeline` beserta file *view*-nya karena sudah tidak lagi digunakan pada halaman *Dashboard*.
- **Remove Regulasi & Google SSO**: Menghapus total modul `Aturan` dan fitur login Google SSO, mengembalikan form login ke standar bawaan Filament.
- **Fix Logout Button**: Memperbaiki tombol "Log out" yang sebelumnya tidak berfungsi di Menu Profil.
- **Upgrade PHP 8.4 Support**: Melakukan upgrade base image Dockerfile ke `php8.4` (FrankenPHP) dan menambahkan konfigurasi `platform` di `composer.json` untuk mengatasi konflik dependensi Symfony 8.0 pada server.
- **Fix Dev Script Windows**: Menghapus `laravel/pail` dari script `composer dev` karena membutuhkan ekstensi `pcntl` yang tidak tersedia di Windows.

### 20 April 2026
- **Perbaikan Masalah File Upload**: Menambahkan konfigurasi `->disk('public')` pada seluruh komponen upload (Internship, Profile, Logbook, Penugasan) untuk memperbaiki masalah file foto dan PDF yang tidak nampil setelah upgrade/rebuild server. Ini memastikan file disimpan dan dibaca dari direktori `storage/app/public` yang dapat diakses publik.
- **Sinkronisasi Branch**: Melakukan push perubahan lokal ke branch `nana-work` dan kemudian melakukan sinkronisasi ulang agar isi branch identik dengan `main` sesuai instruksi.
- **Kategorisasi Rekapitulasi Presensi**: Tabel rekapitulasi kini terbagi menjadi dua kategori: **Peserta Aktif** dan **Peserta Selesai** berdasarkan `end_date` untuk memudahkan monitoring.
- **Otomasi Approval Cuti**: Implementasi *scheduled task* `leaves:auto-approve` yang otomatis memberikan *approval* pada pengajuan cuti berstatus `pending` jika sudah melewati 5 hari kerja tanpa respon admin.
- **Pembaruan Label "Asal Instansi"**: Mengubah seluruh label "Universitas" atau "Sekolah/Universitas" menjadi **Asal Instansi** pada modul Pendaftaran Magang dan Sertifikat agar lebih inklusif bagi peserta SMA/SMK.
- **Standarisasi UI dengan Komponen Filament**: Melakukan refaktor total pada halaman Rekapitulasi Presensi dan Preview Lampiran menggunakan komponen Blade bawaan Filament (`x-filament::*`). Ini memastikan tampilan tetap konsisten, responsif, dan mendukung *dark mode* secara native tanpa bergantung pada custom CSS yang rawan berantakan.
- **Pembersihan Logika Tab**: Memperbarui filter pada tab **Magang BPS** dan **Alumni** di Pendaftaran Magang agar secara eksklusif hanya menampilkan data dengan status `accepted` dan mencocokkan *role* user yang sesuai.

### 21 April 2026
- **Fix Internal Server Error (Timeout 30 detik)**: Memperbaiki `APP_URL` yang memiliki double protocol (`http://http://`) dan nama domain yang salah di `.env`. Sekaligus memperbaiki `GOOGLE_REDIRECT_URI` agar sesuai domain aktif (`project_februari.test`).
- **Fix Gate ViewPulse**: Merefaktorisasi definisi `Gate::define('viewPulse')` di `AppServiceProvider.php` agar menggunakan parameter `$user` yang dilewatkan langsung, bukan via facade `Auth`, untuk mencegah potensi rekursi.
- **Fix InvalidArgumentException (filament::grid)**: Mengganti komponen `<x-filament::grid>` yang sudah dihapus di Filament v5 pada halaman Rekapitulasi Presensi dengan `<div>` biasa menggunakan kelas Tailwind CSS grid (`grid grid-cols-1 sm:grid-cols-2 gap-6`).
- **Refactor Layout Rekapitulasi Presensi**: Menghapus seluruh komponen custom `x-filament::icon`, `x-filament::grid`, dan Chart.js. Layout dibangun ulang menggunakan HTML tabel standar dengan Tailwind CSS dan inline SVG icons agar konsisten dengan style Filament v5 (dark mode, badge warna, hover effect).
- **Fix Thumbnail Lampiran (Penugasan & Logbook)**: Kolom Lampiran di tabel Penugasan dan Logbook diperbarui — gambar kini tampil sebagai thumbnail kecil 36×36 inline tanpa mengekspansi tinggi baris, sedangkan file non-gambar cukup tampil sebagai link teks dengan icon lampiran dan nama ekstensi saja.
- **Fix Oversized Icon di Panel Admin**: Menghapus semua penggunaan `x-filament::icon` dan `x-heroicon-*` pada halaman Rekapitulasi Presensi, dan mengganti chevron expandable row dengan karakter teks `▼`. Root cause: Filament v5 memiliki CSS global yang membuat SVG tanpa atribut `width`/`height` eksplisit menjadi fullscreen di dalam konteks Filament panel.
- **Restore Fitur Lengkap Rekapitulasi Presensi**: Mengembalikan layout 20 April yang memiliki kolom Cuti, Tanpa Izin, pemisahan Peserta Aktif/Selesai, dan chart Chart.js per baris, dengan perbaikan icon yang bersih.
- **Layout Penugasan & Logbook**: Mengubah kolom "Yang Ditugaskan" agar menampilkan daftar nama ke bawah (vertikal) setiap ada lebih dari satu orang, menggunakan `listWithLineBreaks`, untuk menghemat ruang kolom secara horizontal.
- **Verifikasi Tab Magang BPS**: Memastikan filter pada tab Magang BPS di Pendaftaran Magang sudah sesuai regulasi: hanya menampilkan data dengan status `accepted` dan user yang memiliki role `Magang BPS`.
- **Eksplorasi Dokumentasi**: Menghasilkan diagram alur (flowchart) penggunaan aplikasi dan ERD keseluruhan database dalam format Mermaid.js untuk keperluan dokumentasi teknis.

### 22 April 2026
- **Perbaikan Hak Akses (Permissions)**: Sinkronisasi format penamaan permission antara Seeder dan Policy. Seluruh seeder kini menggunakan format `PascalCase:Resource` (contoh: `ViewAny:Internship`) sesuai standar Filament Shield, memperbaiki isu di mana user baru tidak dapat melihat menu pendaftaran magang dan menu lainnya di sidebar.

### 28 April 2026
- **Integrasi Laravel ERD**: Menambahkan package `recca0120/laravel-erd` (dev) untuk mem-generate Entity-Relationship Diagram (ERD).
- **Generate ERD**: Menghasilkan file diagram database `erd.svg` yang ditempatkan di root folder. Menggunakan patch untuk mendukung OS Windows dengan binary path yang telah disesuaikan di `config/laravel-erd.php`.

### 29 April 2026
- **Integrasi SIPETRA SSO (OAuth2)**: Mengimplementasikan fitur Login dengan SIPETRA SSO menggunakan Laravel Socialite dan custom provider. Meliputi:
  - Konfigurasi environment variabel (`SIPETRA_CLIENT_ID`, `SIPETRA_CLIENT_SECRET`, `SIPETRA_REDIRECT_URI`, `SIPETRA_BASE_URL`)
  - Registrasi service SIPETRA di `config/services.php` dengan scope `identity_pegawai:read`, `employee:read`, `contact:read`, `roles:read`
  - Custom Socialite Provider (`SipetraSocialiteProvider.php`) untuk menangani protokol OAuth2 SIPETRA
  - Migrasi database menambahkan kolom `sipetra_id`, `sipetra_token`, `sipetra_refresh_token`, `nip`, `jabatan` ke tabel `users`
  - Controller `SsoController` untuk redirect dan callback SSO (user baru otomatis dibuat dengan role `pegawai`)
  - Tombol "Masuk dengan SIPETRA SSO" di halaman login Filament via render hook
- **Redesign Halaman Auth**: Mengubah tampilan default login/register Filament menjadi layout *Split Screen Card*.
  - Sisi kiri: Form login/register dengan styling yang lebih clean.
  - Sisi kanan: Logo Mangga Muda berukuran besar dengan drop shadow interaktif dan latar belakang putih.
  - Membungkus seluruh konten ke dalam sebuah Card di tengah layar dengan background gradasi yang estetik.
