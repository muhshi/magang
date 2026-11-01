# Mangga Muda System Optimization & Fixes Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menghilangkan celah keamanan pada route & presensi, memperbaiki bug inkonsistensi role SSO & perhitungan widget, mengoptimalkan query N+1 pada penugasan & rekapitulasi presensi, menyempurnakan form penugasan dengan assignee langsung, serta membersihkan komponen legacy manajemen project sesuai aturan simplifikasi penugasan.

**Architecture:** 
1. Hardening & Security: Membersihkan debug route publik di `routes/web.php` dan mengimplementasikan server-side geofence validation (Haversine formula) di `App\Livewire\Presensi`.
2. Role & Permission Standardization: Menyelaraskan nama role SSO (`Pegawai BPS`) di `SsoController` dan mengganti semua akses rentan `roles[0]->name` dengan method aman `hasRole()`.
3. Query & Performance: Menerapkan batch fetching pada `RekapPresensi` (eliminasi N+1), eager loading pada `TicketResource` & `LogbookResource`, dan deduplikasi PDF generator di `CertificateController`.
4. Bugfixes & Widget Refactor: Memperbaiki filter tanggal `InternDashboard`, memperbarui widget dashboard (`StatsOverview`, `UserStatisticsTable`, `RecentActivityTable`, `InternshipBarChart`, `InternshipPieChart`) agar selaras dengan simplifikasi penugasan dan bebas dari referensi tabel `Project`.
5. UX Enhancement: Menambahkan input `assignees` dan `status` langsung pada form `TicketResource`.

**Tech Stack:** Laravel 13, Filament 3/5, Livewire 3, MySQL/SQLite, Spatie Laravel-Permission, PHPUnit/Pest.

**Spec:** Temuan audit dan observasi menyeluruh sistem Mangga Muda per 1 November 2026.

## Global Constraints
- Jangan pernah melakukan drop, truncate, atau delete apapun terkait database eksisting (User Rule Global).
- Modul manajemen project lama di-ignore/disingkirkan dari UI karena telah digantikan oleh simplifikasi penugasan (`simplifypenugasan.md`).
- Sesuai aturan commit: lakukan commit yang jelas dan update `README.md` pada section Changelog dengan tanggal hari ini.

## Review Focus
1. Input koordinat presensi di luar radius kantor wajib ditolak di server walau dimanipulasi di sisi client.
2. Pengguna SSO SIPETRA baru wajib mendapatkan role `Pegawai BPS` sehingga dapat langsung membuat penugasan.
3. User tanpa role atau multi-role tidak boleh menyebabkan crash `Undefined array key 0`.
4. Form pembuatan tiket baru wajib dapat langsung meng-assign peserta magang dan membuat logbook terkait.
5. Perhitungan rekap presensi bulanan menghasilkan data akurat tanpa memicu puluhan query berulang.

---

### Task 1: Security Hardening pada Routes & Server-Side Geofencing Presensi

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Livewire/Presensi.php`
- Test: `tests/Feature/PresensiGeofenceTest.php`

**Interfaces:**
- Consumes: `SystemSettings` (default lat, lng, radius), `Attendance` model
- Produces: Server-side distance check `calculateDistance($lat1, $lon1, $lat2, $lon2)` returning meters

- [ ] **Step 1: Write the failing test for geofence validation & route security**

```php
// tests/Feature/PresensiGeofenceTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Schedule;
use App\Settings\SystemSettings;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Presensi;

class PresensiGeofenceTest extends TestCase
{
    public function test_debug_routes_are_not_accessible_in_production(): void
    {
        $response = $this->get('/info');
        $this->assertTrue($response->status() === 404 || $response->status() === 403);
    }

    public function test_presensi_fails_when_outside_geofence_radius(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Magang BPS');
        $this->actingAs($user);

        // Office is at -6.894561, 110.637492 with 100m radius
        // Submit coordinate far away (e.g. Jakarta -6.2088, 106.8456)
        Livewire::test(Presensi::class)
            ->set('latitude', -6.2088)
            ->set('longitude', 106.8456)
            ->call('store')
            ->assertHasErrors(['latitude']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PresensiGeofenceTest`
Expected: FAIL (routes exist with 200, or presensi doesn't validate geofence on server)

- [ ] **Step 3: Implement security cleanup in `routes/web.php` and geofence check in `Presensi.php`**

1. In `routes/web.php`:
   - Remove `/debug-log-config`, `/test-write`, `/test-log`, `/info`, `/old-home`.
   - Update `attendance/export` to use `response()->streamDownload()` + `Excel::raw()` instead of `Excel::download()` for Octane compatibility.
2. In `app/Livewire/Presensi.php`:
   - Add Haversine distance calculator method:
     ```php
     private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
     {
         $earthRadius = 6371000; // meters
         $latFrom = deg2rad($lat1);
         $lonFrom = deg2rad($lon1);
         $latTo = deg2rad($lat2);
         $lonTo = deg2rad($lon2);

         $latDelta = $latTo - $latFrom;
         $lonDelta = $lonTo - $lonFrom;

         $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
             cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

         return $angle * $earthRadius;
     }
     ```
   - In `store()`:
     - Check `$data['isWfa']`. If false, calculate distance from `$data['officeLat']`, `$data['officeLng']`.
     - If distance > `$data['radius']`, add validation error on `latitude`: `Anda berada di luar radius kantor (${distance}m dari maksimal ${radius}m)`.
     - Remove `$this->isWithinRadius = true;` hardcode.
     - Add database transaction / lock to prevent double-submit presensi.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=PresensiGeofenceTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add routes/web.php app/Livewire/Presensi.php tests/Feature/PresensiGeofenceTest.php
git commit -m "fix(security): remove exposed debug routes and enforce server-side geofencing"
```

---

### Task 2: Standardisasi Role SSO & Perbaikan Null-Safe Role Checking

**Files:**
- Modify: `app/Http/Controllers/Auth/SsoController.php`
- Modify: `app/Filament/Resources/AttendanceResource.php`
- Modify: `app/Filament/Resources/InternshipResource.php`
- Modify: `app/Filament/Resources/LeaveResource.php`
- Modify: `app/Filament/Resources/ScheduleResource.php`
- Modify: `app/Filament/Resources/InternshipResource/Pages/ListInternships.php`
- Test: `tests/Feature/RoleConsistencyTest.php`

**Interfaces:**
- Consumes: Spatie `HasRoles` trait on `User`
- Produces: Safe role checks `auth()->user()?->hasRole('super_admin')` and `auth()->user()?->hasRole('Pegawai BPS')`

- [ ] **Step 1: Write the failing test for role assignment and null-safe role checking**

```php
// tests/Feature/RoleConsistencyTest.php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleConsistencyTest extends TestCase
{
    public function test_user_without_roles_does_not_crash_resource_queries(): void
    {
        $user = User::factory()->create(); // no role assigned
        $this->actingAs($user);

        // AttendanceResource query should not throw Undefined array key 0
        $query = \App\Filament\Resources\AttendanceResource::getEloquentQuery();
        $this->assertNotNull($query);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=RoleConsistencyTest`
Expected: FAIL with `Undefined array key 0` or similar exception.

- [ ] **Step 3: Update `SsoController.php` and replace `roles[0]->name` everywhere**

1. In `app/Http/Controllers/Auth/SsoController.php`:
   - Change `$user->assignRole('pegawai');` to `$user->assignRole('Pegawai BPS');`.
2. In `app/Filament/Resources/AttendanceResource.php`:
   - Replace `if (Auth::user()->roles[0]->name != 'super_admin')` with `if (!auth()->user()?->hasRole('super_admin'))`.
3. In `app/Filament/Resources/InternshipResource.php`:
   - Replace all `Auth::user()->roles[0]->name === 'super_admin'` or `!== 'super_admin'` with `auth()->user()?->hasRole('super_admin')`.
4. In `app/Filament/Resources/LeaveResource.php`:
   - Replace `Auth::user()->roles[0]->name != 'super_admin'` and `Auth::user()->roles[0]->name === 'super_admin'` with `auth()->user()?->hasRole('super_admin')`.
5. In `app/Filament/Resources/ScheduleResource.php`:
   - Replace `Auth::user()->roles[0]->name == 'super_admin'` with `auth()->user()?->hasRole('super_admin')`.
   - Remove `User::latest()->take(5)` limit and allow standard searchable preload for users:
     ```php
     Forms\Components\Select::make('user_id')
         ->relationship('user', 'name')
         ->searchable()
         ->preload()
         ->required(),
     ```
6. In `app/Filament/Resources/InternshipResource/Pages/ListInternships.php`:
   - Replace `Auth::user()->roles[0]->name === 'super_admin' || Auth::user()->roles[0]->name === 'Pegawai BPS'` with `auth()->user()?->hasAnyRole(['super_admin', 'Pegawai BPS'])`.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=RoleConsistencyTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Auth/SsoController.php app/Filament/Resources/AttendanceResource.php app/Filament/Resources/InternshipResource.php app/Filament/Resources/LeaveResource.php app/Filament/Resources/ScheduleResource.php app/Filament/Resources/InternshipResource/Pages/ListInternships.php tests/Feature/RoleConsistencyTest.php
git commit -m "fix(roles): standardize SSO role to Pegawai BPS and replace fragile roles[0] checks"
```

---

### Task 3: Perbaikan Bug Widget Dashboard & Sinkronisasi Penugasan Baru

**Files:**
- Modify: `app/Filament/Widgets/InternDashboard.php`
- Modify: `app/Filament/Widgets/StatsOverview.php`
- Modify: `app/Filament/Widgets/InternshipBarChart.php`
- Modify: `app/Filament/Widgets/InternshipPieChart.php`
- Modify: `app/Filament/Widgets/UserStatisticsTable.php`
- Modify: `app/Filament/Widgets/RecentActivityTable.php`
- Test: `tests/Feature/DashboardWidgetsTest.php`

**Interfaces:**
- Consumes: `Ticket::where('status', ...)` (simplified status), `Attendance::whereDate('created_at', ...)`
- Produces: Correct widget statistics without `Project` references

- [ ] **Step 1: Write test for dashboard stats calculation**

```php
// tests/Feature/DashboardWidgetsTest.php
namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Internship;
use Carbon\Carbon;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    public function test_intern_dashboard_calculates_attendance_by_created_at(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Magang BPS');
        Internship::create([
            'user_id' => $user->id,
            'full_name' => 'Test Intern',
            'birth_place' => 'Demak',
            'birth_date' => '2002-01-01',
            'gender' => 'L',
            'phone' => '08123456789',
            'email' => $user->email,
            'address' => 'Demak',
            'school_name' => 'Undip',
            'education_level' => 'S1',
            'jurusan' => 'Statistika',
            'start_date' => Carbon::now()->startOfMonth()->toDateString(),
            'end_date' => Carbon::now()->addMonths(2)->toDateString(),
            'letter_file' => 'dummy.pdf',
            'photo_file' => 'dummy.jpg',
            'status' => 'accepted',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'schedule_latitude' => -6.89,
            'schedule_longitude' => 110.63,
            'schedule_start_time' => '08:00:00',
            'schedule_end_time' => '16:00:00',
            'start_latitude' => -6.89,
            'start_longitude' => 110.63,
            'start_time' => '08:05:00',
            'end_time' => '16:05:00',
            'created_at' => Carbon::now(),
        ]);

        $this->actingAs($user);
        $widget = new \App\Filament\Widgets\InternDashboard();
        $stats = $widget->getStats();

        $hadirStat = collect($stats)->firstWhere('description', 'Presensi bulan ini');
        $this->assertStringContainsString('1 hari', $hadirStat->getValue());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=DashboardWidgetsTest`
Expected: FAIL (returns 0 hari due to `where('start_time', '>=', $bulanIni)`)

- [ ] **Step 3: Implement widget bugfixes & alignment**

1. In `app/Filament/Widgets/InternDashboard.php`:
   - Change line 53 from `where('start_time', '>=', $bulanIni)` to `whereDate('created_at', '>=', $bulanIni)`.
2. In `app/Filament/Widgets/StatsOverview.php`:
   - Remove `Project::count()` stat and replace with `Penugasan Aktif` / `Logbook Hari Ini`.
   - In `getSuperAdminStats()`:
     - Fix `overdueTickets`:
       ```php
       $overdueTickets = Ticket::where('due_date', '<', Carbon::today())
           ->where(function ($q) {
               $q->whereNull('status')->orWhereNotIn('status', ['selesai']);
           })
           ->count();
       ```
   - In `getAttendanceStats()`:
     - Change `$presentToday = Leave::whereDate('created_at', $today)->count();` to query `Attendance::whereDate('created_at', $today)->count()`.
     - Change `$leaveToday` to query `Leave::where('status', 'approved')->where('start_date', '<=', $today)->where('end_date', '>=', $today)->count()`.
   - In `getUserStats()`:
     - Remove join with `ticket_statuses` and `Project`. Query simplified tickets assigned to `$user->id`.
3. In `app/Filament/Widgets/InternshipBarChart.php`:
   - Change `'label' => 'Blog posts'` to `'label' => 'Pendaftar Magang'`.
4. In `app/Filament/Widgets/InternshipPieChart.php`:
   - Map `'L'` => `'Laki-laki'` and `'P'` => `'Perempuan'` in datasets labels.
5. In `app/Filament/Widgets/UserStatisticsTable.php`:
   - Replace `projects as total_projects` with `logbooks as total_logbooks`.
6. In `app/Filament/Widgets/RecentActivityTable.php`:
   - Update query to show recent penugasan & logbook activities instead of joining obsolete `ticket.project.members`.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=DashboardWidgetsTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Widgets/ tests/Feature/DashboardWidgetsTest.php
git commit -m "fix(widgets): fix InternDashboard attendance date bug and align widgets with simplified penugasan"
```

---

### Task 4: Eliminasi N+1 Query & Optimasi Performa (RekapPresensi & Resources)

**Files:**
- Modify: `app/Filament/Pages/RekapPresensi.php`
- Modify: `app/Filament/Resources/TicketResource.php`
- Modify: `app/Filament/Resources/LogbookResource.php`
- Modify: `app/Filament/Resources/UserResource.php`
- Modify: `app/Http/Controllers/CertificateController.php`
- Test: `tests/Feature/PerformanceQueryTest.php`

**Interfaces:**
- Consumes: Batched queries using `whereIn('user_id', $userIds)`
- Produces: Eager loaded collections, single-query grouped data

- [ ] **Step 1: Write test to verify RekapPresensi executes within bounded query count**

```php
// tests/Feature/PerformanceQueryTest.php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceQueryTest extends TestCase
{
    public function test_rekap_presensi_runs_in_few_queries(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        // Create 5 intern users
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            $user->assignRole('Magang BPS');
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $page = new \App\Filament\Pages\RekapPresensi();
        $page->mount();
        $page->getRekapData();

        $queries = DB::getQueryLog();
        // Without N+1, queries should be under 10 (not 2 per user * 5 = 10+ queries)
        $this->assertLessThan(10, count($queries));
    }
}
```

- [ ] **Step 2: Run test to verify it fails or exceeds bound**

Run: `php artisan test --filter=PerformanceQueryTest`
Expected: FAIL (too many queries executed)

- [ ] **Step 3: Implement batch querying in `RekapPresensi.php` and eager loading in resources**

1. In `app/Filament/Pages/RekapPresensi.php`:
   - Fetch all users first:
     ```php
     $users = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Magang BPS', 'Alumni Magang']))
         ->with(['internship'])
         ->get();
     $userIds = $users->pluck('id');
     ```
   - Batch fetch attendances:
     ```php
     $allAttendances = Attendance::whereIn('user_id', $userIds)
         ->whereYear('created_at', $year)
         ->whereMonth('created_at', $month)
         ->get()
         ->groupBy('user_id');
     ```
   - Batch fetch leaves:
     ```php
     $allLeaves = Leave::whereIn('user_id', $userIds)
         ->where('status', 'approved')
         ->where(function ($q) use ($monthStart, $monthEnd) {
             $q->whereBetween('start_date', [$monthStart, $monthEnd])
               ->orWhereBetween('end_date', [$monthStart, $monthEnd])
               ->orWhere(function ($q2) use ($monthStart, $monthEnd) {
                   $q2->where('start_date', '<', $monthStart)
                      ->where('end_date', '>', $monthEnd);
               });
         })
         ->get()
         ->groupBy('user_id');
     ```
   - In loop `$users as $user`: replace `Attendance::where(...)` with `$allAttendances->get($user->id, collect())` and `$allLeaves->get($user->id, collect())`.
2. In `app/Filament/Resources/TicketResource.php`:
   - In `getEloquentQuery()`:
     ```php
     return parent::getEloquentQuery()->with(['employee', 'assignees']);
     ```
3. In `app/Filament/Resources/LogbookResource.php`:
   - In `getEloquentQuery()`:
     ```php
     return parent::getEloquentQuery()->with(['assignees']);
     ```
4. In `app/Filament/Resources/UserResource.php`:
   - Remove `RelationManagers\ProjectsRelationManager::class` and `projects_count` column.
   - Eager load roles in `getEloquentQuery()`.
5. In `app/Http/Controllers/CertificateController.php`:
   - Extract common PDF generation logic into `private function renderCertificatePdf(Certificate $certificate, string $disposition)` to eliminate 80+ lines of duplicate code.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=PerformanceQueryTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Pages/RekapPresensi.php app/Filament/Resources/TicketResource.php app/Filament/Resources/LogbookResource.php app/Filament/Resources/UserResource.php app/Http/Controllers/CertificateController.php tests/Feature/PerformanceQueryTest.php
git commit -m "perf(rekap): eliminate N+1 queries in RekapPresensi and add eager loading to resources"
```

---

### Task 5: UX Enhancement: Assignee & Status Langsung pada Form Penugasan

**Files:**
- Modify: `app/Filament/Resources/TicketResource.php`
- Modify: `app/Filament/Resources/TicketResource/Pages/CreateTicket.php`
- Modify: `app/Filament/Resources/TicketResource/Pages/EditTicket.php`
- Test: `tests/Feature/TicketAssigneeFlowTest.php`

**Interfaces:**
- Consumes: Form data `assignee_ids`, `status`
- Produces: Synced `ticket_users` relation and auto-created/updated `Logbook` entries

- [ ] **Step 1: Write test for creating ticket with assignees and auto-generating logbooks**

```php
// tests/Feature/TicketAssigneeFlowTest.php
namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Logbook;
use App\Models\Ticket;
use App\Models\User;
use App\Filament\Resources\TicketResource\Pages\CreateTicket;
use Livewire\Livewire;
use Tests\TestCase;

class TicketAssigneeFlowTest extends TestCase
{
    public function test_creating_ticket_with_assignees_creates_logbooks(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);

        $intern = User::factory()->create();
        $intern->assignRole('Magang BPS');

        $employee = Employee::create(['name' => 'Pegawai A']);

        Livewire::test(CreateTicket::class)
            ->fillForm([
                'employee_id' => $employee->id,
                'created_at' => now()->toDateString(),
                'name' => 'Tugas Uji Coba',
                'priority' => 'important',
                'status' => 'proses',
                'assignee_ids' => [$intern->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $ticket = Ticket::where('name', 'Tugas Uji Coba')->first();
        $this->assertNotNull($ticket);
        $this->assertTrue($ticket->assignees->contains($intern->id));

        // Verify logbook was auto-created for this intern
        $logbook = Logbook::where('ticket_id', $ticket->id)->where('user_id', $intern->id)->first();
        $this->assertNotNull($logbook);
        $this->assertEquals('proses', $logbook->status);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TicketAssigneeFlowTest`
Expected: FAIL (field `assignee_ids` not in schema or logbook not created on create)

- [ ] **Step 3: Implement assignee and status fields in `TicketResource.php` and sync hooks**

1. In `app/Filament/Resources/TicketResource.php`:
   - In `form()` schema:
     - Add `Forms\Components\Select::make('status')` with options `['belum', 'proses', 'revisi', 'selesai']`, default `'belum'`.
     - Add `Forms\Components\Select::make('assignee_ids')` with label `Yang Ditugaskan (Peserta Magang)`:
       ```php
       Forms\Components\Select::make('assignee_ids')
           ->label('Yang Ditugaskan (Peserta Magang)')
           ->multiple()
           ->options(function () {
               $acceptedUserIds = \App\Models\Internship::where('status', 'accepted')->pluck('user_id');
               return \App\Models\User::where(function ($query) use ($acceptedUserIds) {
                   $query->whereHas('roles', fn ($q) => $q->where('name', 'Magang BPS'))
                         ->orWhereIn('id', $acceptedUserIds);
               })
               ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'Alumni Magang'))
               ->pluck('name', 'id')->toArray();
           })
           ->searchable()
           ->preload()
           ->columnSpanFull(),
       ```
2. In `app/Filament/Resources/TicketResource/Pages/CreateTicket.php`:
   - In `afterCreate()`:
     - Sync assignees to `ticket_users`.
     - Automatically create `Logbook` entries for each assigned user with source `system`.
3. In `app/Filament/Resources/TicketResource/Pages/EditTicket.php`:
   - In `mutateFormDataBeforeFill(array $data)`:
     - Fill `'assignee_ids' => $this->record->assignees->pluck('id')->toArray()`.
   - In `afterSave()`:
     - Sync assignees and update/create/delete `Logbook` entries accordingly.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=TicketAssigneeFlowTest`
Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Resources/TicketResource.php app/Filament/Resources/TicketResource/Pages/ tests/Feature/TicketAssigneeFlowTest.php
git commit -m "feat(penugasan): add direct assignee selection and status in ticket form with logbook sync"
```

---

### Task 6: Full Verification, Documentation Update & Final Push

**Files:**
- Modify: `README.md`
- Run: Entire test suite `php artisan test`

- [ ] **Step 1: Run full test suite to ensure everything passes**

Run: `php artisan test`
Expected: All tests PASS with 0 failures.

- [ ] **Step 2: Update `README.md` with Changelog**

Add changelog section for `1 November 2026`:
- **Keamanan & Perapian Route**: Hapus route debug publik (`/info`, `/debug-log-config`, `/test-write`, `/test-log`, `/old-home`) dan ganti export presensi ke `streamDownload` yang aman untuk FrankenPHP.
- **Server-Side Geofencing Presensi**: Validasi jarak koordinat (formula Haversine) di sisi server untuk mencegah manipulasi lokasi presensi di luar radius kantor.
- **Standardisasi Role SSO SIPETRA**: Samakan role pengguna baru SSO menjadi `Pegawai BPS` agar selaras dengan hak akses fitur penugasan.
- **Null-Safe Role Checking**: Mengganti pemanggilan rentan `roles[0]->name` dengan `auth()->user()->hasRole()` di seluruh resource.
- **Perbaikan Widget Dashboard & InternDashboard**: Perbaiki perbandingan kolom tanggal presensi di dashboard peserta, hapus label dummy `Blog posts`, format label gender L/P, serta bersihkan dependensi model `Project` lama pada widget.
- **Optimasi Performa & Query Rekap Presensi**: Eliminasi masalah N+1 query pada rekapitulasi presensi menggunakan batch grouping query, serta tambahkan eager loading pada resource penugasan dan logbook.
- **Peningkatan UX Penugasan**: Isian peserta magang (`assignees`) dan `status` kini dapat diisi langsung saat membuat atau mengedit penugasan tanpa harus melalui tombol aksi tabel.
- **Deduplikasi Kode Sertifikat**: Satukan logika render template sertifikat PDF di `CertificateController`.

- [ ] **Step 3: Final Commit & Push**

```bash
git add .
git commit -m "chore: update changelog for 1 November 2026 system optimization and fixes"
git push
```
