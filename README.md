# DewaKoding Project Management

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

- PHP > 8.2+
- Laravel 12
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