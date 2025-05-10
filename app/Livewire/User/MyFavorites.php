<?php

namespace App\Livewire\User;

use App\Enums\AdStatus;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

/**
 * MyFavorites Component.
 * Allows users to view their favorite ads.
 */
class MyFavorites extends Component
{
    use SEOToolsTrait;

    // List of favorite ads
    public $ads;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    /**
     * Mount lifecycle hook.
     * Fetches the favorite ads of the currently authenticated user.
     */
    public function mount()
    {
        $this->fetchFavoriteAds();
        $this->setSeoData();
    }

    /**
     * Fetches the favorite ads for the authenticated user.
     */
    protected function fetchFavoriteAds()
    {
        if (!Auth::check()) {
            // Handle error: User not authenticated.
            // This can redirect to login or show an error message.
            return;
        }

        $this->ads = Auth::user()->favouritesAds()
        ->whereHas('ad', function ($query) {
            $query->where('status', AdStatus::ACTIVE->value);
        })
        ->with('ad')
        ->get()
        ->pluck('ad');

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

        $title = __('messages.t_seo_my_fav_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Renders the MyFavorites view.
     */
    public function render()
    {
        return view('livewire.user.my-favorites');
    }
}
