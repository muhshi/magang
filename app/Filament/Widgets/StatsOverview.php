<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Ticket;
<<<<<<< HEAD
use App\Models\Leave; // asumsi presensi lewat Leave
=======
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
>>>>>>> upstream/main
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $pollingInterval = '30s';
<<<<<<< HEAD
    protected static ?int $sort = 5;
=======
>>>>>>> upstream/main

    protected function getStats(): array
    {
        return array_merge(
            $this->getProjectManagementStats(),
            $this->getAttendanceStats(),
        );
    }

    protected function getProjectManagementStats(): array
    {
        $totalProjects = Project::count();
<<<<<<< HEAD
        $totalTickets = Ticket::count();
        $newTicketsLastWeek = Ticket::where('created_at', '>=', Carbon::now()->subDays(7))->count();
=======

        // Total Tickets
        $totalTickets = Ticket::count();

        // Tickets created in the last 7 days
        $newTicketsLastWeek = Ticket::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        // Users count
        $usersCount = User::count();

        // Tickets without assignee
>>>>>>> upstream/main
        $unassignedTickets = Ticket::whereNull('user_id')->count();

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description('Active projects in the system')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Total Tickets', $totalTickets)
                ->description('Tickets across all projects')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('New Tickets This Week', $newTicketsLastWeek)
                ->description('Created in the last 7 days')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('info'),

            Stat::make('Unassigned Tickets', $unassignedTickets)
                ->description('Tickets without an assignee')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($unassignedTickets > 0 ? 'danger' : 'success'),
<<<<<<< HEAD
        ];
    }

    protected function getAttendanceStats(): array
    {
        $today = Carbon::today();
        $presentToday = Leave::whereDate('created_at', $today)->count();
        $leaveToday = Leave::whereDate('created_at', $today)->whereNotNull('leave_type')->count();

        return [
            Stat::make('Total Presensi Hari Ini', $presentToday)
                ->description('Termasuk semua jenis presensi')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Izin Hari Ini', $leaveToday)
                ->description('Jumlah pegawai yang izin')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('warning'),
        ];
    }
=======

            Stat::make('Team Members', $usersCount)
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
>>>>>>> upstream/main
}
