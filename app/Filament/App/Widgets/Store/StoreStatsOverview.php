<?php

namespace App\Filament\App\Widgets\Store;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreStatsOverview extends BaseWidget
{
    public $storeTrafficSources;
    public $totalVisits;
    public $averageViewTime;
    public function mount($storeTrafficSources,$averageViewTime){

        $this->storeTrafficSources=$storeTrafficSources;
        $this->averageViewTime=$averageViewTime ?? 0;

        $this->totalVisits=count($this->storeTrafficSources) ?? 0;
    }
    protected function getStats(): array
    {
        $stats=[];
        if(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->number_of_visits){
            $stats[]=Stat::make(__('messages.t_number_of_visits'),  $this->totalVisits)
            ->chart([2,10, 3, 5, 4, 17])
            ->color('success');
        }
        if(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->average_view_time){
            $stats[]=Stat::make(__('messages.t_average_view_time'),  $this->getAverageTime());
        }
        return $stats;
    }

    public function getAverageTime(){
        if($this->averageViewTime>=60){
            return pluralize($this->averageViewTime/60, __('messages.t_minute'),__('messages.t_minutes'));
        }else{
            return pluralize($this->averageViewTime , __('messages.t_second'),__('messages.t_seconds'));
        }
    }
}
