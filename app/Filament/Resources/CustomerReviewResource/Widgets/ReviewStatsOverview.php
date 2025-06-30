<?php

namespace App\Filament\Resources\CustomerReviewResource\Widgets;

use App\Models\CustomerReview;
use App\Models\ReviewReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Carbon\Carbon;

class ReviewStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalReviews = CustomerReview::count();
        $pendingReviews = CustomerReview::pending()->count();
        $approvedReviews = CustomerReview::approved()->count();
        $rejectedReviews = CustomerReview::rejected()->count();
        $flaggedReviews = CustomerReview::flagged()->count();
        
        $pendingReports = ReviewReport::pending()->count();
        $urgentReports = ReviewReport::where('priority', 'urgent')->count();
        
        $averageRating = CustomerReview::approved()->avg('rating');
        $todayReviews = CustomerReview::whereDate('created_at', today())->count();
        
        // Calcular tendencias (comparar con periodo anterior)
        $yesterdayReviews = CustomerReview::whereDate('created_at', Carbon::yesterday())->count();
        $reviewTrend = $todayReviews - $yesterdayReviews;
        
        $weeklyApproved = CustomerReview::approved()->where('approved_at', '>=', now()->subWeek())->count();
        $previousWeekApproved = CustomerReview::approved()
            ->whereBetween('approved_at', [now()->subWeeks(2), now()->subWeek()])
            ->count();
        $approvalTrend = $weeklyApproved - $previousWeekApproved;

        return [
            Stat::make('Total de Reseñas', Number::format($totalReviews))
                ->description($reviewTrend > 0 ? "+$reviewTrend desde ayer" : ($reviewTrend < 0 ? "$reviewTrend desde ayer" : "Sin cambios"))
                ->descriptionIcon($reviewTrend > 0 ? 'heroicon-m-arrow-trending-up' : ($reviewTrend < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($reviewTrend > 0 ? 'success' : ($reviewTrend < 0 ? 'danger' : 'gray'))
                ->chart($this->getReviewChart()),

            Stat::make('Pendientes de Moderación', Number::format($pendingReviews))
                ->description($pendingReviews > 0 ? 'Requieren atención' : 'Todo al día')
                ->descriptionIcon($pendingReviews > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($pendingReviews > 50 ? 'danger' : ($pendingReviews > 20 ? 'warning' : 'success')),

            Stat::make('Aprobadas', Number::format($approvedReviews))
                ->description($approvalTrend > 0 ? "+$approvalTrend esta semana" : ($approvalTrend < 0 ? "$approvalTrend esta semana" : "Sin cambios"))
                ->descriptionIcon($approvalTrend > 0 ? 'heroicon-m-arrow-trending-up' : ($approvalTrend < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color('success'),

            Stat::make('Rating Promedio', $averageRating ? number_format($averageRating, 1) . ' ⭐' : 'N/A')
                ->description($this->getRatingDescription($averageRating))
                ->color($this->getRatingColor($averageRating)),

            Stat::make('Rechazadas', Number::format($rejectedReviews))
                ->description(round(($rejectedReviews / max($totalReviews, 1)) * 100, 1) . '% del total')
                ->color('danger'),

            Stat::make('Flaggeadas', Number::format($flaggedReviews))
                ->description($flaggedReviews > 0 ? 'Necesitan revisión' : 'Ninguna flaggeada')
                ->descriptionIcon($flaggedReviews > 0 ? 'heroicon-m-flag' : 'heroicon-m-check-circle')
                ->color($flaggedReviews > 0 ? 'warning' : 'success'),

            Stat::make('Reportes Pendientes', Number::format($pendingReports))
                ->description($urgentReports > 0 ? "$urgentReports urgentes" : 'Sin reportes urgentes')
                ->descriptionIcon($urgentReports > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-shield-check')
                ->color($urgentReports > 0 ? 'danger' : ($pendingReports > 0 ? 'warning' : 'success')),

            Stat::make('Hoy', Number::format($todayReviews))
                ->description('Reseñas creadas hoy')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }

    private function getReviewChart(): array
    {
        // Obtener reseñas de los últimos 7 días
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = CustomerReview::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        
        return $data;
    }

    private function getRatingDescription(?float $rating): string
    {
        if (!$rating) {
            return 'Sin calificaciones';
        }

        return match (true) {
            $rating >= 4.5 => 'Excelente calidad',
            $rating >= 4.0 => 'Muy buena calidad',
            $rating >= 3.5 => 'Buena calidad',
            $rating >= 3.0 => 'Calidad regular',
            default => 'Necesita mejoras',
        };
    }

    private function getRatingColor(?float $rating): string
    {
        if (!$rating) {
            return 'gray';
        }

        return match (true) {
            $rating >= 4.0 => 'success',
            $rating >= 3.5 => 'warning',
            default => 'danger',
        };
    }

    protected function getColumns(): int
    {
        return 4;
    }
}