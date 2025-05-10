<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\AdPromotion;
use App\Models\Promotion;
use Carbon\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use App\Settings\DashBoardSettings;


class RevenueChart extends ChartWidget
{
    use HasWidgetShield;

    public static function getSort(): int
    {
        $moveChartToBottom = app(DashBoardSettings::class)->enable_move_chart_to_bottom;
        return $moveChartToBottom ? 99 : 1;
    }

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return userHasPermission('widget_RevenueChart');
    }

    protected function getType(): string
    {
        return 'line';
    }
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return __('messages.t_ap_revenue_by_ad_upgrade_last_12_months');
    }

    protected function getData(): array
    {
        $labels = [];
        $datasets = [];
        $promotionTypes = Promotion::select('name')->distinct()->get()->pluck('name')->toArray();

        // Create labels (for example, last 12 months)
        for ($i = 11; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subMonths($i)->format('M Y');
        }

        foreach ($promotionTypes as $type) {
            $data = [];

            foreach ($labels as $label) {
                $date = Carbon::createFromFormat('M Y', $label);
                $total = AdPromotion::whereHas('promotion', function ($query) use ($type) {
                    $query->where('name', $type);
                })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('price');

                $data[] = $total;
            }

            $datasets[] = [
                'label' => $type,
                'data' => $data,
                'fill' => 'start',
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }
}
