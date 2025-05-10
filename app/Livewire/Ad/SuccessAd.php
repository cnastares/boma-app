<?php

namespace App\Livewire\Ad;

use App\Models\Ad;
use App\Settings\AdSettings;
use Livewire\Component;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use App\Settings\SubscriptionSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Carbon\Carbon;
use App\Models\Category;


class SuccessAd extends Component
{
    use SEOToolsTrait;

    public $id;
    public $ad;
    public $isMobileHidden = false;

    /**
     * Mount the component and process the Ad and its promotions.
     *
     * @param int $id The Ad ID.
     */
    public function mount($id)
    {
        $this->id = $id;

        $this->initializeAd();
        $this->setSeoData();
    }

    /**
     * Initialize the Ad and ensure it belongs to the authenticated user.
     */
    protected function initializeAd()
    {
        $this->ad = Ad::find($this->id);
        if (!$this->ad || $this->ad->user_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        } else {
            $this->updateAdDetails();
        }
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

        $title = __('messages.t_seo_success_ad') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Update the Ad details based on settings.
     */
    protected function updateAdDetails()
    {
        if (function_exists('isSubscriptionEnabled') && isSubscriptionEnabled()) {
            if (isSubscriptionAdLimitOver()) {
                return redirect()->route('filament.app.pages.my-ads');
            } else {
                $adSettings = app(AdSettings::class);
                $adModeration = $adSettings->ad_moderation;
                $adDuration = $adSettings->ad_duration;
                $freeAdLimit = getFreeAdLimit();
                $freeAdCount = auth()->user()->getFreeAdCount();
                $adSource = $freeAdLimit > $freeAdCount ? 'free' : 'subscription';
                $subscriptionId = null;
                if ($adSource == 'subscription') {
                    $subscriptionId = $this->findAvailableSubscription();
                }

                if (!$adModeration) {
                    $adModeration = $this->verifyAdminApproval();
                }


                $this->ad->update([
                    'status' => $adModeration ? 'pending' : 'active',
                    'source' => $adSource,
                    'subscription_id' => $subscriptionId,
                    'posted_date' => Carbon::now(),
                ]);
            }
        } else {
            $adSettings = app(AdSettings::class);
            $adModeration = $adSettings->ad_moderation;
            $adDuration = $adSettings->ad_duration;
            $freeAdLimit = getFreeAdLimit();
            $freeAdCount = auth()->user()->getFreeAdCount();
            $adSource = $freeAdLimit > $freeAdCount ? 'free' : 'package';

            if (!$adModeration) {
                $adModeration = $this->verifyAdminApproval();
            }


            $this->ad->update([
                'status' => $adModeration ? 'pending' : 'active',
                'source' => $adSource,
                'posted_date' => Carbon::now(),
                'expires_at' => now()->addDays($adDuration)
            ]);
        }
    }

    /**
     * Verifies if the ad requires manual approval based on its category hierarchy.
     *
     * This method checks the main, sub, and child categories of the ad to determine
     * if manual approval is enabled. If any category in the hierarchy requires manual
     * approval, the method returns true. Otherwise, it returns false.
     *
     * @return
     */
    public function verifyAdminApproval()
    {
        if (!$this->ad) {
            return false; // Early return if ad is not set
        }

        $categories = [
            $this->ad->main_category_id,
            $this->ad->category_id,
            $this->ad->child_category_id // Assuming child category is the same as category_id
        ];

        foreach ($categories as $categoryId) {
            $category = Category::find($categoryId);
            if ($category && $category->enable_manual_approval) {
                return true; // Return false if any category requires manual approval
            }
        }

        return false; // All categories are approved
    }

    public function findAvailableSubscription()
    {
        $user = auth()->user();
        $activeSubscriptions = $user->getActiveSubscriptions();
        foreach ($activeSubscriptions as $subscription) {
            $remainAdCount = $subscription->getRemainAdCount();
            if ($remainAdCount > 0) {
                return $subscription->id;
            }
        }
    }
    /**
     * Render the component view.
     *
     * @return \Illuminate\View\View The view to render.
     */
    public function render()
    {
        return view('livewire.ad.success-ad');
    }
}
