<?php

namespace App\Filament\Widgets;

use App\Models\Internship;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon as CarbonCarbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class InternshipOverview extends BaseWidget
{
    use HasWidgetShield;
    protected function getStats(): array
    {
        return
            $this->getInternshipRegistrationStats();
    }

    protected function getInternshipRegistrationStats(): array
    {
        $totalApplicants = User::role('Calon Magang')->count();
        $pendingPendaftar = Internship::where('status', 'pending')->count();
        $acceptedApplicants = User::whereHas('internships', function ($query) {
            $query->where('status', 'accepted');
        })->count();

        return [
            Stat::make('Total Calon Magang', $totalApplicants)
                ->description('Semua pendaftar magang')
                ->descriptionIcon('heroicon-m-user')
                ->color('gray'),

            // PERBAIKAN: Stat baru untuk pendaftar yang statusnya pending
            Stat::make('Menunggu Approval', $pendingPendaftar)
                ->description('Pendaftar dengan status pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Pendaftar Diterima', $acceptedApplicants)
                ->description('Status: Magang BPS')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),
        ];
    }
}
