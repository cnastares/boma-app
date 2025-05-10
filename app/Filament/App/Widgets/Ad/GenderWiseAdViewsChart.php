<?php

namespace App\Filament\App\Widgets\Ad;

use Filament\Widgets\ChartWidget;

class GenderWiseAdViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    public $gender;
    public $views;
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => $this->views,
                    'backgroundColor' => [
                        'rgb(255, 99, 132)',   // pinkish red
                        'rgb(54, 162, 235)',   // blue
                        'rgb(255, 205, 86)',   // yellow
                        'rgb(75, 192, 192)',   // teal
                        'rgb(153, 102, 255)',  // purple
                        'rgb(255, 159, 64)',   // orange
                        'rgb(201, 203, 207)',  // light gray
                        'rgb(255, 99, 71)',    // tomato red
                        'rgb(34, 139, 34)',    // forest green
                        'rgb(220, 20, 60)'
                    ],
                ],
            ],
            'labels' => $this->gender,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return __('messages.t_gender_wise_ad_views_chart');
    }
}
