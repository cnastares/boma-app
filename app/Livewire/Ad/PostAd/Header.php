<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\Promotion;
use App\Settings\PaymentSettings;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class Header extends Component
{
    // Reactive properties
    #[Reactive] public $id;
    #[Reactive] public $title;
    #[Reactive] public $current;
    #[Reactive] public $stepIndex;
    #[Reactive] public $isLastStep;

    public $isWebView;

    // Regular properties
    public $selectedPromotions = [];
    public $ad;
    public $isDisabled=true;
    public $isAdDetailsFilled=true;
    public $isDynamicFieldFilled=true;

    /**
     * Mount the component.
     *
     * @param mixed $id
     */
    public function mount($id)
    {
        if ($id) {
            $this->loadAdDetails($id);
        }
    }

      /**
     * Load and set ad details if an ID is provided.
     *
     * @param int $id The ID of the ad to load
     */
    private function loadAdDetails($id)
    {
        $this->ad = Ad::find($id);
    }

    /**
     * Advance to the next step.
     */
    public function next()
    {

        if($this->id){
            $this->dispatch('next-clicked');
        }
    }

    /**
     * Toggle the selected promotions.
     */
    #[On('promotion-selected')]
    public function togglePromotion($selectedPromotions)
    {
        $this->selectedPromotions = $selectedPromotions;
    }

    /**
     * Advance to the publish
     */
    public function publish()
    {
       $this->dispatch('next-clicked');
    }

     /**
     * Advance to the publish
     */
    #[On('preview-ad')]
    public function previewAd()
    {
        return redirect()-> route('ad.overview', [ 'slug' => $this->ad->slug ]);
    }

    /**
     * Process the payment and publish the ad.
     */
    public function payAndPublish()
    {
        // Authentication check
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $this->dispatch('validate-promote-ad');
    }

    /**
     * Generate Stripe line items from promotions.
     */
    protected function generateStripeLineItems($promotions)
    {
        return $promotions->map(function ($promotion) {
            return [
                'price_data' => [
                    'currency' => app(PaymentSettings::class)->currency,
                    'unit_amount' => $promotion->price * 100,
                    'product_data' => [
                        'name' => $promotion->name,
                    ],
                ],
                'quantity' => 1,
            ];
        })->toArray();
    }

    #[On('required-fields-filled')]
    public function requiredFieldsFilled($isFilled){
        $this->isAdDetailsFilled=$isFilled;
        $this->isDisabled=($this->isAdDetailsFilled && $this->isDynamicFieldFilled) ?false :true;
    }
    #[On('dynamic-fields-filled')]
    public function dynamicFieldsFilled($isFilled){
        $this->isDynamicFieldFilled=$isFilled;
        $this->isDisabled=($this->isAdDetailsFilled && $this->isDynamicFieldFilled) ?false :true;

    }
    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.ad.post-ad.header');
    }
}
