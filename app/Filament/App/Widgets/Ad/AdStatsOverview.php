<?php

namespace App\Filament\App\Widgets\Ad;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdStatsOverview extends BaseWidget
{
    public $userTrafficSources;
    public $averageViewTime;
    public $totalVisits;
    public $likesCount;
    public $contactConversionRate;
    public $totalWebsiteUrlClicked;
    public function mount($userTrafficSources,$averageViewTime,$likesCount,$contactConversionRate,$totalWebsiteUrlClicked){
        $this->userTrafficSources=$userTrafficSources;
        $this->averageViewTime=$averageViewTime ?? 0;
        $this->likesCount=$likesCount;
        $this->totalVisits=count($this->userTrafficSources)??0;
        $this->contactConversionRate=$contactConversionRate;
        $this->totalWebsiteUrlClicked=$totalWebsiteUrlClicked;
    }
    protected function getStats(): array
    {
        $stats=[];
        if(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->number_of_visits){
            $stats[]=Stat::make(__('messages.t_number_of_visits'),  $this->totalVisits)
            ->chart([7, 2, 10, 3, 18, 4, 17])
            ->color('success');
        }
        if(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->average_view_time){
            $stats[]=Stat::make(__('messages.t_average_view_time'),  $this->getAverageTime());
        }
        if(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->number_of_favorites){
            $stats[]=Stat::make(__('messages.t_number_of_favorites'),  $this->likesCount);
        }
        if(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->clicks_on_link){
            $stats[]= Stat::make(__('messages.t_total_websiteurl_clicked'),  $this->totalWebsiteUrlClicked);
        }
        if(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->contact_conversion_rate_level=='basic'){
            $stats[]=  Stat::make(__('messages.t_contact_conversion_rate'),  $this->contactConversionRate);
        }
        return $stats;
    }

    public function getAverageTime(){
        if($this->averageViewTime>=60){
            return pluralize($this->averageViewTime/60, __('messages.t_minute'),__('messages.t_minutes'));
        }else{
            return pluralize($this->averageViewTime, __('messages.t_second'),__('messages.t_seconds'));
        }
    }
}
