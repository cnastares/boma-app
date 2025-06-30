<?php

namespace App\Filament\Resources\CustomerReviewResource\Pages;

use App\Filament\Resources\CustomerReviewResource;
use App\Models\CustomerReview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCustomerReviews extends ListRecords
{
    protected static string $resource = CustomerReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(CustomerReview::count()),
            
            'pending' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->pending())
                ->badge(CustomerReview::pending()->count())
                ->badgeColor('warning'),
            
            'approved' => Tab::make('Aprobadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->approved())
                ->badge(CustomerReview::approved()->count())
                ->badgeColor('success'),
            
            'rejected' => Tab::make('Rechazadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->rejected())
                ->badge(CustomerReview::rejected()->count())
                ->badgeColor('danger'),
            
            'flagged' => Tab::make('Flaggeadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->flagged())
                ->badge(CustomerReview::flagged()->count())
                ->badgeColor('primary'),
            
            'needs_attention' => Tab::make('Necesita Atención')
                ->modifyQueryUsing(fn (Builder $query) => $query->needsAttention())
                ->badge(CustomerReview::needsAttention()->count())
                ->badgeColor('danger'),
            
            'recent' => Tab::make('Recientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->recent(7))
                ->badge(CustomerReview::recent(7)->count())
                ->badgeColor('info'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerReviewResource\Widgets\ReviewStatsOverview::class,
        ];
    }
}