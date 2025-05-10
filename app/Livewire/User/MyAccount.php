<?php

namespace App\Livewire\User;

use App\Models\Page;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

/**
 * MyAccount Component.
 * Displays user account information and provides options to navigate through various pages
 * excluding certain predefined ones.
 */
class MyAccount extends Component
{
    use SEOToolsTrait;



    // Properties
    public $pages;  // Holds the list of pages the user can navigate to from their account.

    /**
     * Initializes the component.
     * Fetches the list of active pages excluding certain predefined ones.
     */
    public function mount()
    {
        // Define slugs of pages to be excluded.
        $excludeSlugs = ['careers', 'about-us', 'terms-conditions', 'privacy-policy'];

        // Fetch pages that are active and not in the exclude list.
        $this->pages = Page::where('status', 'active')
                      ->whereNotIn('slug', $excludeSlugs)
                      ->get(['title', 'slug']);

        $this->setSeoData();
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

        $title = __('messages.t_seo_my_account_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Renders the MyAccount view.
     */
    public function render()
    {
        return view('livewire.user.my-account');
    }
}
