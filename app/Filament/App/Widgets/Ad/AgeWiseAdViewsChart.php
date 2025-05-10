<?php

namespace App\Filament\App\Widgets\Ad;

use App\Models\UserTrafficSource;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AgeWiseAdViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    public $age;
    public ?string $filter = 'today';
    public $views;
    public $recordId;
    protected static ?string $pollingInterval = '1s';
    protected function getData(): array
    {
        $this->age=$this->getAgeData()->pluck('age')->toArray();
        $this->views= $this->getAgeData()->pluck('total_views')->toArray();
        return [
            'datasets' => [
                [

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
            'labels' => $this->age,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return __('messages.t_age_wise_ad_views_chart');
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    public function getAgeData($filter = null)
    {
        $ageTrafficData = UserTrafficSource::where('trackable_id', $this->recordId)
            ->select(
                DB::raw("CASE
                        WHEN users.date_of_birth IS NOT NULL
                        THEN FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365)
                        ELSE 'N/A'
                        END AS age"),
                DB::raw('COUNT(*) as total_views')
            )
            ->join('users', 'user_traffic_sources.user_id', '=', 'users.id')
            ->where(function ($query) {
                // Include users with age > 1 or NULL age
                $query->whereRaw('FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) > 1')
                    ->orWhereNull('users.date_of_birth');
            });

        // Apply date filter based on the selected filter value
        if ($this->filter) {
            switch ($this->filter) {
                case 'today':
                    $ageTrafficData->whereDate('user_traffic_sources.created_at', '=', now()->toDateString());
                    break;
                case 'week':
                    $ageTrafficData->whereBetween('user_traffic_sources.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $ageTrafficData->whereMonth('user_traffic_sources.created_at', '=', now()->month)
                        ->whereYear('user_traffic_sources.created_at', '=', now()->year);
                    break;
                case 'year':
                    $ageTrafficData->whereYear('user_traffic_sources.created_at', '=', now()->year);
                    break;
            }
        }

        $ageTrafficData = $ageTrafficData->groupBy('age')->get();
        return $ageTrafficData;
    }

}
