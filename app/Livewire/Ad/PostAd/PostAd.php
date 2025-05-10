<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\OrderPackageItem;
use App\Models\UsedPackageItem;
use App\Models\UserAdPosting;
use App\Settings\AdSettings;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Settings\GeneralSettings;
use App\Settings\PackageSettings;
use App\Settings\SEOSettings;
use App\Settings\SubscriptionSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Carbon\Carbon;

class PostAd extends Component
{
    use SEOToolsTrait;

    #[Url]
    public $current = null;

    #[Url]
    public $isWebView = false;

    public Ad $ad;

    #[Url(keep: true)]
    public $id = '';

    // Regular properties
    public $promotionIds = [];

    public $steps = [];
    public $fromUrl;

    /**
     * Mount the component and set the properties if an ad ID is provided.
     * If an ad ID is provided, it is checked for validity and ownership by the current user.
     * If the ad is not found or does not belong to the current user, a 404 error is triggered.
     */
    public function mount()
    {
        $this->initiationSteps();
        // First, check if user verification is required and if the user meets this requirement
        $verificationCheck = $this->checkUserVerification();
        if ($verificationCheck) {
            return $verificationCheck; // Redirect if verification is required and the user is not verified
        }
        $this->fromUrl = url()->previous();

        if (!empty($this->id)) {
            $ad = Ad::find($this->id);
            $userId = auth()->id();
            if (!$ad || $ad->user_id != $userId) {
                abort(404, 'Not found');
            }
            //update disable location
            if ($ad && $ad->category && ($ad->adType?->disable_location || $ad?->adType?->default_location)) {
                $this->dispatch('hide-location');
            }
            if ($ad && $ad->status->value == 'draft' && app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
                $this->checkAdLimit();
            }
            // if(app('filament')->hasPlugin('subscription') && app(SubscriptionSettings::class)->status){
            //     $this->checkSubscriptionAdLimit();
            // }

        } else {
            if (app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
                $this->checkAdLimit();
            }
            if (app('filament')->hasPlugin('subscription') && app(SubscriptionSettings::class)->status) {
                $this->checkSubscriptionAdLimit();
            }
        }

        // Check if current page is 'ad.post-ad.payment-ad' and 'promotionIds' is empty
        if ($this->current === 'ad.post-ad.payment-ad' && empty($this->promotionIds)) {
            $routeParameters = [
                'id' => $this->id,
                'current' => 'ad.post-ad.promote-ad',
            ];

            return redirect()->route('post-ad', $routeParameters);
        }

        $this->setSeoData();
    }

    public function initiationSteps()
    {
        $firstStep = 'ad.post-ad.ad-detail';

        if (is_vehicle_rental_active()) {
            $firstStep = 'livewire.vehicle-ad-detail';
        }

        $this->steps = [
            $firstStep,
            'ad.post-ad.visualize-ad',
            'ad.post-ad.locate-ad'
        ];

        if (!$this->current) {
            $this->current = $firstStep;
            // $this->current = Arr::first($this->steps);
        }
    }

    protected function checkUserVerification()
    {
        // Check if user verification is required to post ads
        $verificationRequired = app(AdSettings::class)->user_verification_required;

        if ($verificationRequired) {
            // Check if the currently logged-in user is verified
            $user = auth()->user();

            if (!$user || !$user->verified) { // Assuming your User model has a 'verified' attribute
                // Redirect to a verification required page if the user is not verified
                return redirect()->route('verification-required');
            }
        }

        return null;
    }


    protected function checkAdLimit()
    {
        $userId = auth()->id();

        // Retrieve or create the UserAdPosting record for the current user
        $userAdPosting = UserAdPosting::firstOrCreate(
            ['user_id' => $userId],
            ['period_start' => Carbon::now()]
        );

        $renewalPeriod = app(PackageSettings::class)->ad_renewal_period;
        $now = Carbon::now();

        // Determine the next period start date based on the renewal period
        $nextPeriodStart = $renewalPeriod === 'year' ?
            $userAdPosting->period_start->addYear() :
            $userAdPosting->period_start->addMonth();

        // Reset the ad count if the current date is greater or equal to the next period start date
        if ($now->greaterThanOrEqualTo($nextPeriodStart)) {
            $userAdPosting->ad_count = 0;
            $userAdPosting->free_ad_count = 0;
            $userAdPosting->period_start = $now;
            $userAdPosting->save();
        }

        // Free ad limit from settings
        $freeAdLimit = app(PackageSettings::class)->free_ad_limit;

        // Calculate the total available limit from active package items
        $activeAdLimit = OrderPackageItem::whereHas('orderPackage', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('type', 'ad_count')
            ->whereDate('expiry_date', '>=', now())
            ->sum('available');

        // Total ad limit is the sum of free limit and the current period's ad count from UserAdPosting
        $totalAdLimit = $freeAdLimit + $activeAdLimit;

        // Check if the user's ad count for the current period is within the total limit
        if ($userAdPosting->ad_count >= $totalAdLimit) {
            return redirect()->route('ad-limit-reached');
        }

        return null;
    }


    /**
     * Toggle the selected promotions.
     */
    #[On('promotion-selected')]
    public function togglePromotion($selectedPromotions)
    {
        $this->promotionIds = array_keys($selectedPromotions);
    }

    /**
     * Move to the next step in the process.
     */
    #[On('next-step')]
    public function next()
    {
        $currentIndex = array_search($this->current, $this->steps);

        if ($currentIndex !== false && isset($this->steps[$currentIndex + 1])) {
            $this->current = $this->steps[$currentIndex + 1];
        }
    }

    /**
     * Move to the previous step or redirect to home if at the first step.
     */
    public function back()
    {
        $currentIndex = array_search($this->current, $this->steps);

        if ($currentIndex === 0 || $this->current == 'ad.post-ad.promote-ad') {
            // If it's the first step, redirect to home
            return redirect($this->current == 'ad.post-ad.promote-ad' ? route('filament.app.pages.my-ads') : ($this->fromUrl ?? route('home')));
        }

        if ($currentIndex !== false && isset($this->steps[$currentIndex - 1])) {
            $this->current = $this->steps[$currentIndex - 1];
        } else {
            // If it's the payment confirm
            $this->current = 'ad.post-ad.promote-ad';
        }
    }

    /**
     * Get the title based on the current step.
     *
     * @return string
     */
    public function getTitle()
    {
        switch ($this->current) {
            case 'livewire.vehicle-ad-detail':
                return __('messages.t_ad_details');
            case "ad.post-ad.ad-detail":
                return __('messages.t_ad_details');
            case 'ad.post-ad.visualize-ad':
                return __('messages.t_visualize_ad');
            case 'ad.post-ad.locate-ad':
                return __('messages.t_locate_ad');
            case 'ad.post-ad.promote-ad':
                return __('messages.t_promote_ad');
            case 'ad.post-ad.payment-ad':
                return __('messages.t_payment_ad');
            default:
                return '';
        }
    }

    /**
     * Update the Ad ID.
     *
     * @param int $id
     */
    #[On('ad-created')]
    public function updateAdId($id)
    {
        $this->id = $id;
    }


    /**
     * Update the Current.
     *
     * @param string $current
     */
    #[On('current-step')]
    public function updateCurrentStep($current)
    {
        $this->current = $current;
    }

    /**
     * Check if the current step is the last step.
     *
     * @return bool
     */
    public function isLastStep()
    {
        return $this->current === end($this->steps) || $this->current == 'ad.post-ad.promote-ad';
    }

    /**
     * Get the index of the current step.
     *
     * @return int
     */
    public function getCurrentStepIndex()
    {
        return array_search($this->current, $this->steps);
    }

    /**
     * Set SEO data
     */
    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);


        $separator = $generalSettings->separator ?? '-';
        $siteName = $generalSettings->site_name ?? app_name();

        $title = __('messages.t_seo_post_ad_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    #[On('hide-location')]
    public function disableLocation()
    {
        if (($key = array_search('ad.post-ad.locate-ad', $this->steps)) !== false) {
            unset($this->steps[$key]);
        }
    }

    #[On('enable-location')]
    public function enableLocation()
    {
        if (($key = array_search('ad.post-ad.locate-ad', $this->steps)) == false) {
            $this->steps[] = 'ad.post-ad.locate-ad';
        }
    }

    /**
     * Redirect to the success page after publishing.
     */
    #[On('publish-clicked')]
    public function publish()
    {
        if (app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
            // Update UserAdPosting record
            $this->updateUserAdPosting();
        }
        return redirect()->route('success-ad', ['id' => $this->id]);
    }


    /**
     * Update the UserAdPosting record for the current user.
     */
    protected function updateUserAdPosting()
    {
        $userId = auth()->id();
        $userAdPosting = UserAdPosting::firstOrCreate(
            ['user_id' => $userId],
            ['period_start' => Carbon::now()]
        );

        $renewalPeriod = app(PackageSettings::class)->ad_renewal_period;
        $now = Carbon::now();

        // Determine the next period start date based on the renewal period
        $nextPeriodStart = $renewalPeriod === 'year' ?
            $userAdPosting->period_start->addYear() :
            $userAdPosting->period_start->addMonth();

        // Reset the ad count if the current date is greater or equal to the next period start date
        if ($now->greaterThanOrEqualTo($nextPeriodStart)) {
            $userAdPosting->ad_count = 0;
            $userAdPosting->free_ad_count = 0;
            $userAdPosting->period_start = $now;
        }

        // Increment ad count
        $userAdPosting->ad_count++;

        // Calculate the total available limit from active package items
        $activeAdLimit = OrderPackageItem::whereHas('orderPackage', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->where('status', 'active');
        })
            ->where('type', 'ad_count')
            ->where('expiry_date', '>=', $now)
            ->sum('available');

        // Free ad limit from settings
        $freeAdLimit = app(PackageSettings::class)->free_ad_limit;

        // Total ad limit is the sum of free limit and active ad limit
        $totalAdLimit = $freeAdLimit + $activeAdLimit;

        // Check if the user has reached the ad limit
        if ($userAdPosting->ad_count > $totalAdLimit) {
            return redirect()->route('ad-limit-reached');
        } else {
            // Check for an active ad package
            $activeAdPackage = OrderPackageItem::whereHas('orderPackage', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'active');
            })
                ->where('type', 'ad_count')
                ->where('expiry_date', '>=', $now)
                ->where('available', '>', 0)
                ->orderBy('expiry_date', 'asc')
                ->first();

            if ($activeAdPackage) {
                // Decrement an ad from the active package
                $activeAdPackage->decrement('available');
                $activeAdPackage->increment('used');

                UsedPackageItem::create([
                    'ad_id' => $this->id,
                    'order_package_item_id' => $activeAdPackage->id,
                ]);
            } else {
                $userAdPosting->free_ad_count++;
            }
            $userAdPosting->save();
        }
    }

    public function checkSubscriptionAdLimit()
    {

        //Check if the user's ad count for the current period is within the total limit
        if (isSubscriptionAdLimitOver()) {
            return redirect()->route('subscription.ad-limit-reached');
        }
        return null;
    }
    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.ad.post-ad.post-ad');
    }
}
