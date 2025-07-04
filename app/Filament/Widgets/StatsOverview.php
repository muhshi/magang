<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Ticket;

use App\Models\Leave; // asumsi presensi lewat Leave

use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        if ($isSuperAdmin) {
            return $this->getSuperAdminStats();
        } else {
            return $this->getUserStats();
        }
    }

    protected function getSuperAdminStats(): array
    {
        $totalProjects = Project::count();


        // Total Tickets
        $totalTickets = Ticket::count();
        $newTicketsLastWeek = Ticket::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $usersCount = User::count();
        $unassignedTickets = Ticket::whereDoesntHave('assignees')->count();
        $myTickets = DB::table('tickets')
            ->join('ticket_users', 'tickets.id', '=', 'ticket_users.ticket_id')
            ->where('ticket_users.user_id', Auth::user()->id)
            ->count();

        $myCreatedTickets = Ticket::where('created_by', Auth::user()->id)->count();
        $overdueTickets = Ticket::where('tickets.due_date', '<', Carbon::now())
            ->whereHas('status', function ($query) {
                $query->whereNotIn('name', ['Completed', 'Done', 'Closed']);
            })
            ->count();

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description('Active projects in the system')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Total Tickets', $totalTickets)
                ->description('Tickets across all projects')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('My Assigned Tickets', $myTickets)
                ->description('Tickets assigned to you')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('info'),

            Stat::make('New Tickets This Week', $newTicketsLastWeek)
                ->description('Created in the last 7 days')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('info'),

            Stat::make('Unassigned Tickets', $unassignedTickets)
                ->description('Tickets without any assignee')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($unassignedTickets > 0 ? 'danger' : 'success'),

            Stat::make('My Created Tickets', $myCreatedTickets)
                ->description('Tickets you created')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),

            Stat::make('Overdue Tickets', $overdueTickets)
                ->description('Past due date')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueTickets > 0 ? 'danger' : 'success'),

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

    protected function getUserStats(): array
    {
        $user = auth()->user();

        $myProjects = $user->projects()->count();

        $myProjectIds = $user->projects()->pluck('projects.id')->toArray();

        $projectTickets = Ticket::whereIn('project_id', $myProjectIds)->count();

        $myAssignedTickets = DB::table('tickets')
            ->join('ticket_users', 'tickets.id', '=', 'ticket_users.ticket_id')
            ->where('ticket_users.user_id', $user->id)
            ->count();

        $myCreatedTickets = Ticket::where('created_by', $user->id)->count();

        $newTicketsThisWeek = Ticket::whereIn('project_id', $myProjectIds)
            ->where('tickets.created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        $myOverdueTickets = DB::table('tickets')
            ->join('ticket_users', 'tickets.id', '=', 'ticket_users.ticket_id')
            ->join('ticket_statuses', 'tickets.ticket_status_id', '=', 'ticket_statuses.id')
            ->where('ticket_users.user_id', $user->id)
            ->where('tickets.due_date', '<', Carbon::now())
            ->whereNotIn('ticket_statuses.name', ['Completed', 'Done', 'Closed'])
            ->count();

        $myCompletedThisWeek = DB::table('tickets')
            ->join('ticket_users', 'tickets.id', '=', 'ticket_users.ticket_id')
            ->join('ticket_statuses', 'tickets.ticket_status_id', '=', 'ticket_statuses.id')
            ->where('ticket_users.user_id', $user->id)
            ->whereIn('ticket_statuses.name', ['Completed', 'Done', 'Closed'])
            ->where('tickets.updated_at', '>=', Carbon::now()->subDays(7))
            ->count();

        $teamMembers = User::whereHas('projects', function ($query) use ($myProjectIds) {
            $query->whereIn('projects.id', $myProjectIds);
        })->where('id', '!=', $user->id)->count();

        return [
            Stat::make('My Projects', $myProjects)
                ->description('Projects you are member of')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('My Assigned Tickets', $myAssignedTickets)
                ->description('Tickets assigned to you')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color($myAssignedTickets > 10 ? 'danger' : ($myAssignedTickets > 5 ? 'warning' : 'success')),

            Stat::make('My Created Tickets', $myCreatedTickets)
                ->description('Tickets you created')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('info'),

            Stat::make('Project Tickets', $projectTickets)
                ->description('Total tickets in your projects')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('Completed This Week', $myCompletedThisWeek)
                ->description('Your completed tickets')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($myCompletedThisWeek > 0 ? 'success' : 'gray'),

            Stat::make('New Tasks This Week', $newTicketsThisWeek)
                ->description('Created in your projects')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('info'),

            Stat::make('My Overdue Tasks', $myOverdueTickets)
                ->description('Your past due tickets')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($myOverdueTickets > 0 ? 'danger' : 'success'),

            Stat::make('Team Members', $teamMembers)
                ->description('People in your projects')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
