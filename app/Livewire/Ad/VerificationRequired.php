<?php

namespace App\Livewire\Ad;

use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Livewire\Component;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class VerificationRequired extends Component
{
    use SEOToolsTrait;


    public $isMobileHidden = false;
    public function mount()
    {
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

        $title = __('messages.t_verification_required') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }
    /**
     * Render the component view.
     *
     * @return \Illuminate\View\View The view to render.
     */
    public function render()
    {
        return view('livewire.ad.verification-required');
    }
}
