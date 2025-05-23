<?php

namespace App\Filament\Widgets;

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
        $newApplicantsThisWeek = User::role('Calon Magang')
            ->where('created_at', '>=', CarbonCarbon::now()->subDays(7))
            ->count();
        $acceptedApplicants = User::whereHas('internships', function ($query) {
            $query->where('status', 'accepted');
        })->count();

        return [
            Stat::make('Total Calon Magang', $totalApplicants)
                ->description('Semua pendaftar magang')
                ->descriptionIcon('heroicon-m-user')
                ->color('gray'),

            Stat::make('Pendaftar Minggu Ini', $newApplicantsThisWeek)
                ->description('Bertambah 7 hari terakhir')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pendaftar Diterima', $acceptedApplicants)
                ->description('Status: Magang BPS')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),
        ];
    }
}
