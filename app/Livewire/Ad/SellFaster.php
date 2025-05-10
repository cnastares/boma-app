<?php

namespace App\Livewire\Ad;

use App\Models\Ad;
use App\Models\OrderPackage;
use App\Settings\PackageSettings;
use App\Settings\SubscriptionSettings;
use Livewire\Attributes\Url;
use Livewire\Component;

class SellFaster extends Component
{

    public $id;

    public $ad;

    public $isHorizontal = false;

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
        if ($this->id) {
            $this->ad = Ad::find($this->id);
        }
    }

    public function sellFastNow()
    {
        if(app('filament')->hasPlugin('subscription') && app(SubscriptionSettings::class)->status) {
            return redirect()->route('filament.app.pages.choose-plan');
        }
        if(app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
            $userOrderPackages = OrderPackage::where('user_id', auth()->id())
                                            ->whereHas('packageItems', function ($query) {
                                                $query->whereDate('expiry_date', '>=', now())
                                                    ->where('type', 'promotion')
                                                    ->where('available', '>', 0);
                                            })
                                            ->first();

            $actionType = $userOrderPackages ? 'apply' : 'single';

            $routeParameters = [
                'pkg_type' => $actionType,
                'ad_id' => $this->id,
            ];

            return redirect()->route('filament.app.pages.choose-package', $routeParameters);
        } else {
            $routeParameters = [
                'id' => $this->id,
                'current' => 'ad.post-ad.promote-ad',
            ];
            return redirect()->route('post-ad', $routeParameters);
        }
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\View\View The view to render.
     */
    public function render()
    {
        return view('livewire.ad.sell-faster');
    }
}
