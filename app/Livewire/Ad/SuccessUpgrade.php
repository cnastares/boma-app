<?php

namespace App\Livewire\Ad;

use App\Models\Ad;
use Livewire\Attributes\Url;
use Livewire\Component;

class SuccessUpgrade extends Component
{

    #[Url]
    public $ad_id;

    public $ad;

    public $isMobileHidden = false;

    /**
     * Initialize the component with the specified action type and load the associated Ad.
     *
     * @param string $actionType The type of action that has been performed.
     */
    public function mount()
    {
        $this->loadAd();
    }

    /**
     * Load the Ad model if an Ad ID is provided.
     */
    protected function loadAd()
    {
        if ($this->ad_id) {
            $this->ad = Ad::find($this->ad_id);
        }
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\View\View The view to render.
     */
    public function render()
    {
        return view('livewire.ad.success-upgrade');
    }
}
