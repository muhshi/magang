<?php

namespace App\Filament\Widgets;

use App\Models\Internship;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class InternshipPieChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Distribusi Pendaftar Magang (Gender)';
    protected static ?int $sort = 3; // urutan tampil

    protected ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $data = Internship::selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $labels = $data->keys()->map(fn($gender) => match ($gender) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => $gender ?? 'Lainnya',
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => $data->values(),
                    'backgroundColor' => [
                        '#36A2EB', // biru
                        '#FF6384', // merah
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
