<?php

namespace App\Filament\Widgets;

use App\Models\Internship;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InternshipBarChart extends ChartWidget
{
    protected static ?string $heading = 'Total Pendaftar Menurut Bulan';
    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $data = Trend::model(Internship::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Blog posts',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => \Carbon\Carbon::parse($value->date)->translatedFormat('F')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
