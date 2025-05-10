<?php

use App\Models\AdPromotion;
use App\Models\Message;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Subscription;
use App\Models\User;
use App\Settings\SubscriptionSettings;
use Illuminate\Database\Eloquent\Builder;

if (!function_exists('checkSubscriptionAdLimit')) {
    function isSubscriptionAdLimitOver(): ?bool
    {
        $userId = auth()->id();
        //Get count of active Ad for current user
        $activeSubscriptionAdCount = auth()->user()->getActiveSubscriptionAdCount();
        $activeFreeAdCount = auth()->user()->getFreeAdCount();
        $totalActiveAdCount=$activeSubscriptionAdCount+$activeFreeAdCount;
        // Free ad limit from settings
        $freeAdLimit = getFreeAdLimit();

        //Get ad limit of the active plans
        $adLimit = getAdLimit($userId);

        $totalAdLimit = $freeAdLimit + $adLimit;
        //Check if the user's ad count for the current period is within the total limit
        if ($totalActiveAdCount >= $totalAdLimit) {
            return true;
        }
        return false;
    }
}

if (!function_exists('getAdLimit')) {
    function getAdLimit()
    {
        $adLimit=0;
        if(getActiveSubscription()){
            $adLimit=getActiveSubscription()?->ad_count;
        }else{
            $adLimit=getFreePlan()?->ad_count;
        }
        return $adLimit;
    }
}

if (!function_exists('getFreeAdLimit')) {
    function getFreeAdLimit()
    {
        return app(SubscriptionSettings::class)->free_ad_limit ?? 0;
    }
}
if (!function_exists('abortIfSubscriptionDisabled')) {
    function abortIfSubscriptionDisabled()
    {
        $subscription = app(SubscriptionSettings::class);
        if ($subscription && $subscription->status == false) {
            abort(404);
        }
    }
}
if (!function_exists('isSubscriptionEnabled')) {
    function isSubscriptionEnabled()
    {
        $subscription = app(SubscriptionSettings::class);
        return $subscription ? $subscription->status : false;
    }
}

if (!function_exists('canReceiveMessage')) {
    function canReceiveMessage($sellerId)
    {
        if(!getSubscriptionSetting('status')){
            return true;
        }
        $interactionCount=getUserSubscriptionPlan($sellerId)?->chat_limit ?? 0;
        $currentMonthMessageCount=Message::where('receiver_id',$sellerId)->count();

        return $interactionCount > $currentMonthMessageCount ;
    }
}

if (!function_exists('getFreePlan')) {
    function getFreePlan()
    {
        return Plan::free()->first();
    }
}
if (!function_exists('getActiveSubscriptionPlan')) {
    function getActiveSubscriptionPlan()
    {
        $activeSubscription=getActiveSubscription();
        $activePlan=$activeSubscription?->plan;
        $freePlan=getFreePlan();
        return $activePlan ?? $freePlan;
    }
}

if (!function_exists('getUserSubscriptionPlan')) {
    function getUserSubscriptionPlan($userId)
    {
        $activeSubscription=Subscription::where('subscriber_id',$userId)->active()->whereDate('ends_at', '>=', today())->first();
        $activePlan=$activeSubscription?->plan;
        $freePlan=getFreePlan();
        return $activePlan ?? $freePlan;
    }
}


if (!function_exists('getRemaningFeaturedAdCount')) {
    function getRemaningFeaturedAdCount()
    {
        $limit=0;
        $activeSubscription=getActiveSubscription();
        if($activeSubscription){
            $limit=$activeSubscription->feature_ad_count;
        }else{
            $limit=getFreePlan()->feature_ad_count ?? 0;
        }
        $existingPromotionCount = AdPromotion::whereHas('ad',function($query){
            $query->where('user_id',auth()->id());
        })
        ->where('promotion_id', 1)
            ->where('end_date', '>=', now())
            ->count();
        return max(0, $limit - $existingPromotionCount);
    }
}

if (!function_exists('getRemaningSpotlightAdCount')) {
    function getRemaningSpotlightAdCount()
    {
        $limit=0;
        $activeSubscription=getActiveSubscription();
        if($activeSubscription){
            $limit=$activeSubscription->spotlight_ad_count;
        }else{
            $limit=getFreePlan()->spotlight_ad_count ?? 0;
        }
        $existingPromotionCount = AdPromotion::whereHas('ad',function($query){
            $query->where('user_id',auth()->id());
        })
            ->where('promotion_id', 2)
            ->where('end_date', '>=', now())
            ->count();
        return max(0, $limit - $existingPromotionCount);
    }
}


if (!function_exists('getRemaningUrgentAdCount')) {
    function getRemaningUrgentAdCount()
    {
        $limit=0;
        $activeSubscription=getActiveSubscription();
        if($activeSubscription){
            $limit=$activeSubscription->urgent_ad_count;
        }else{
            $limit=getFreePlan()->urgent_ad_count ?? 0;
        }
        $existingPromotionCount = AdPromotion::
        whereHas('ad',function($query){
            $query->where('user_id',auth()->id());
        })
            ->where('promotion_id', 3)
            ->where('end_date', '>=', now())
            ->count();
        return max(0, $limit - $existingPromotionCount);
    }
}

if (!function_exists('getRemaningWebsiteUrlAdCount')) {
    function getRemaningWebsiteUrlAdCount()
    {
         $limit=0;
        $activeSubscription=getActiveSubscription();
        if($activeSubscription){
            $limit=$activeSubscription->website_url_count;
        }else{
            $limit=getFreePlan()->website_url_count ?? 0;
        }
        $existingPromotionCount = AdPromotion::
        whereHas('ad',function($query){
            $query->where('user_id',auth()->id());
        })
            ->where('promotion_id', 4)
            ->where('end_date', '>=', now())
            ->count();
        return max(0, $limit - $existingPromotionCount);
    }
}

if (!function_exists('getActiveSubscription')) {
    function getActiveSubscription()
    {
        if(!auth()->user()){
            return ;
        }
        return auth()->user()->getActiveSubscriptions()->first();
    }
}
