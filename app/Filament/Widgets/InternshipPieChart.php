<?php

namespace App\Filament\Widgets;

use App\Models\Internship;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;

class InternshipPieChart extends ChartWidget
{
    use HasWidgetShield;
    protected static ?string $heading = 'Distribusi Pendaftar Magang (Gender)';
    protected static ?int $sort = 1; // urutan tampil

    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $data = Internship::selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender'); // hasil: ['Laki-laki' => 10, 'Perempuan' => 15]

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => $data->values(), // ambil jumlahnya saja
                    'backgroundColor' => [
                        '#36A2EB', // biru
                        '#FF6384', // merah
                    ],
                ],
            ],
            'labels' => $data->keys(), // ambil label gender
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
