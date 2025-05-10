<?php

namespace App\Livewire\Ad;

use App\Models\AdPromotion;
use App\Models\FavouriteAd;
use App\Models\Promotion;
use App\Settings\AdTemplateSettings;
use App\Settings\AppearanceSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class AdItem extends Component
{
    // Properties
    public $ad;
    #[Reactive]
    public $ref = "/";
    public $isFavourited = false;
    public $isFeatured = false;
    public $isUrgent = false;
    public $featureAdColors;
    public $urgentAdColors;
    public $isSpotlight = false;
    #[Reactive]
    public $currentView;
    /**
     * Mount the component with the given ad.
     *
     * @param mixed $ad The ad to display.
     */
    public function mount($ad)
    {
        $this->ad = $ad;
        $this->isFavourited = FavouriteAd::where('user_id', Auth::id())
            ->where('ad_id', $ad->id)
            ->exists();
        $this->checkPromotions();
        $this->getPromotionColors();
        if ($this->isSpotlight) {
            $this->updateAdViews(2);
        }
    }

    /**
     * Check if the ad has any promotions applied.
     */
    protected function checkPromotions()
    {
        $currentDate = now();

        // Check if the ad is featured
        $this->isFeatured = $this->isPromotionActive(1);

        // Check if the ad is urgent
        $this->isUrgent = $this->isPromotionActive(3);
        if ($this->isFeatured) {
            $this->updateAdViews(1);
        }
        if ($this->isUrgent) {
            $this->updateAdViews(3);
        }
    }

    /**
     * Check if a given promotion is active for the ad.
     *
     * @param int $promotionId The ID of the promotion to check.
     * @return bool Whether the promotion is active or not.
     */
    protected function isPromotionActive(int $promotionId): bool
    {
        $currentDate = now();
        return AdPromotion::where('ad_id', $this->ad->id)
            ->where('active', true)
            ->where('promotion_id', $promotionId)
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->exists();
    }

    /**
     * Add the ad to the user's favourites or remove it if it's already a favourite.
     */
    public function addToFavourites()
    {
        // Ensure the user is authenticated before adding to favourites.
        if (!Auth::check()) {
            // If not logged in, redirect to login page or show a message.
            Notification::make()
                ->title(__('messages.t_login_to_add_favorites'))
                ->success()
                ->send();
            return redirect(route('login'));
        }

        // Toggle the favourite status for the ad.
        if ($this->isFavourited) {
            FavouriteAd::where('user_id', Auth::id())
                ->where('ad_id', $this->ad->id)
                ->delete();
            $this->isFavourited = false;
        } else {
            FavouriteAd::create([
                'user_id' => Auth::id(),
                'ad_id' => $this->ad->id,
            ]);
            $this->isFavourited = true;
        }
    }

    /**
     * Render a placeholder for the component.
     *
     * @param array $params Parameters for the placeholder.
     * @return \Illuminate\Contracts\View\View
     */
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.ad-skeleton', $params);
    }

    public function getPromotionColors()
    {
        $this->featureAdColors = Promotion::find(1);
        $this->urgentAdColors = Promotion::find(3);
    }

    public function updateAdViews($promotionId)
    {
        $adPromotion = AdPromotion::where('ad_id', $this->ad->id)
            ->where('active', true)
            ->where('promotion_id', $promotionId)->first();
        if ($adPromotion) {
            $adPromotion->views += 1;
            $adPromotion->save();
        }
    }

    public function saveClicks()
    {
        // Use a concise conditional expression for promotion ID assignment
        $promotionId = match (true) {
            $this->isFeatured => 1,
            $this->isSpotlight => 2,
            $this->isUrgent => 3,
            default => null,
        };

        // Early return if no promotion ID is set
        if ($promotionId === null) {
            return;
        }

        // Update the ad promotion if found
        $adPromotion = AdPromotion::where('ad_id', $this->ad->id)
            ->where('active', true)
            ->where('promotion_id', $promotionId)
            ->first();

        if ($adPromotion) {
            $adPromotion->clicks += 1;
            $adPromotion->save();
        }
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $adTemplate = app(AdTemplateSettings::class)->theme;

        // Render view based on ad template and current view.
        $view = match ($adTemplate) {
            'classic_frame' => $this->currentView === 'list' ? 'ad-templates.classic.ad-item-list' : 'ad-templates.classic.ad-item',
            'ad_fusion' => $this->currentView === 'list' ? 'ad-templates.ad-fusion.ad-item-list' : 'ad-templates.ad-fusion.ad-item',
            default => $this->currentView === 'list' ? 'ad-templates.classic.ad-item-list' : 'ad-templates.classic.ad-item',
        };

        return view($view);
    }
}
