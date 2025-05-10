<?php

namespace App\Filament\App\Resources\StoreBannerResource\Pages;

use App\Filament\App\Resources\StoreBannerResource;
use App\Models\StoreBanner;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoreBanners extends ListRecords
{
    protected static string $resource = StoreBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->visible(function(){
                $bannerLimit=getActiveSubscriptionPlan()?getActiveSubscriptionPlan()->banner_count:0;
                $activeBanners=StoreBanner::where('user_id',auth()->id())->count();
                return $bannerLimit>$activeBanners;
            }),
        ];
    }
}
