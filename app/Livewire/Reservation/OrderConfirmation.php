<?php

namespace App\Livewire\Reservation;

use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Livewire\Component;
use Livewire\Attributes\Url;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class OrderConfirmation extends Component
{
    use SEOToolsTrait;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';


    /**
     * Mount lifecycle hook.
     */
    public function mount()
    {
        $this->setSeoData();
    }

    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings     = app(SEOSettings::class);

        $title = __('messages.t_order_confirmation') . ' ' . ($generalSettings->separator ?? '-') . ' ' .
                 ($generalSettings->site_name ?? config('app.name'));
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($seoSettings->meta_description);
    }

    public function render()
    {
        return view('livewire.reservation.order-confirmation');
    }
}

