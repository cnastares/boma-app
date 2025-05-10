<?php

namespace App\Livewire\Reservation\Purchases;

use App\Models\Reservation\Order;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Url;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class ViewPurchase extends Component
{
    use SEOToolsTrait;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';
    public $order;
    public $isOrderCancel;
    public $histories;

    /**
     * Mount lifecycle hook.
     */
    public function mount($id)
    {
        $this->loadOrderData($id);
        $this->setSeoData();
    }

    /**
     * Load order data and history.
     */
    protected function loadOrderData($id)
    {
        $this->order = Order::findOrFail($id);
        $this->histories = $this->order->histories()->whereNotNull('action_date')->get();
        $this->isOrderCancel = $this->order->histories()->where('action', 'order_cancelled')->exists();
    }

    /**
     * Cancel the order and update the history.
     */
    public function cancelMyOrder()
    {
        $this->order->histories()
            ->where('action', 'cancelled') // Filter only "cancelled" actions
            ->whereNull('action_date') // Ensure action_date is NULL
            ->update([
                'command' => 'by customer',
                'action_date' => now(),
            ]);

        $this->order->histories()->create([
            'user_id' => $this->order->user_id,
            'vendor_id' => $this->order->vendor_id,
            'action' => 'order_cancelled',
            'command' => __('messages.t_by_customer'),
            'action_date' => now(),
        ]);

        $this->isOrderCancel = true;
        $this->histories = $this->order->histories()->whereNotNull('action_date')->get();

        $this->sendCancelNotification();
    }

    /**
     * Send cancellation notification.
     */
    protected function sendCancelNotification()
    {
        Notification::make()
            ->title(__('messages.t_cancel_my_order'))
            ->success()
            ->send();
    }

    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);

        $separator = $generalSettings->separator ?? '-';
        $siteName = $generalSettings->site_name ?? app_name();

        $title = __('messages.t_seo_my_purchases_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.reservation.purchases.view-purchase');
    }
}
