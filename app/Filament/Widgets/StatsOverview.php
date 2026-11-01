<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Ticket;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $user = auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');

        if ($isSuperAdmin) {
            return $this->getSuperAdminStats();
        } else {
            return $this->getUserStats();
        }
    }

    protected function getSuperAdminStats(): array
    {
        $today = Carbon::today()->format('Y-m-d');

        $totalTickets = Ticket::count();
        $activeTickets = Ticket::where(function ($q) {
            $q->whereNull('status')->orWhereNotIn('status', ['selesai']);
        })->count();
        $completedTickets = Ticket::where('status', 'selesai')->count();
        $unassignedTickets = Ticket::whereDoesntHave('assignees')->count();

        $overdueTickets = Ticket::where('due_date', '<', Carbon::today())
            ->where(function ($q) {
                $q->whereNull('status')->orWhereNotIn('status', ['selesai']);
            })
            ->count();

        $activeInterns = User::whereHas('roles', fn($q) => $q->where('name', 'Magang BPS'))->count();
        $presentToday = Attendance::whereDate('created_at', Carbon::today())->count();
        $leaveToday = Leave::where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();

        return [
            Stat::make('Peserta Magang Aktif', $activeInterns . ' orang')
                ->description('Status Magang BPS')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Presensi Hari Ini', $presentToday . ' hadir')
                ->description($leaveToday > 0 ? "{$leaveToday} sedang cuti/izin" : 'Semua hadir tepat waktu')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('Total Penugasan', $totalTickets)
                ->description("{$activeTickets} aktif • {$completedTickets} selesai")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),

            Stat::make('Tugas Belum Di-assign', $unassignedTickets)
                ->description('Belum ada peserta yang ditugaskan')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($unassignedTickets > 0 ? 'warning' : 'success'),

            Stat::make('Tugas Overdue', $overdueTickets)
                ->description('Melewati target selesai')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueTickets > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getUserStats(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        $myAssignedTickets = Ticket::whereHas('assignees', fn($q) => $q->where('users.id', $user->id))->count();
        $myActiveTasks = Ticket::whereHas('assignees', fn($q) => $q->where('users.id', $user->id))
            ->where(fn($q) => $q->whereNull('status')->orWhereNotIn('status', ['selesai']))
            ->count();
        $myCompletedTasks = Ticket::whereHas('assignees', fn($q) => $q->where('users.id', $user->id))
            ->where('status', 'selesai')
            ->count();
        $myOverdueTasks = Ticket::whereHas('assignees', fn($q) => $q->where('users.id', $user->id))
            ->where('due_date', '<', Carbon::today())
            ->where(fn($q) => $q->whereNull('status')->orWhereNotIn('status', ['selesai']))
            ->count();
        $myCreatedTickets = Ticket::where('created_by', $user->id)->count();

        return [
            Stat::make('Tugas Saya', $myAssignedTickets)
                ->description('Total tugas yang ditugaskan kepada Anda')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('primary'),

            Stat::make('Tugas Aktif', $myActiveTasks)
                ->description('Sedang proses / belum selesai')
                ->descriptionIcon('heroicon-m-clock')
                ->color($myActiveTasks > 0 ? 'warning' : 'success'),

            Stat::make('Tugas Selesai', $myCompletedTasks)
                ->description('Tugas yang telah selesai dikerjakan')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Tugas Overdue', $myOverdueTasks)
                ->description('Melewati target tanggal selesai')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($myOverdueTasks > 0 ? 'danger' : 'success'),

            Stat::make('Tugas Dibuat', $myCreatedTickets)
                ->description('Tugas yang Anda buat untuk peserta')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('info'),
        ];
    }
}
