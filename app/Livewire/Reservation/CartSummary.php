<?php

namespace App\Livewire\Reservation;

use App\Models\Reservation\Cart;
use App\Traits\HelperTraits;
use App\Models\Reservation\TemporaryOrder;
use App\Settings\{GeneralSettings, SEOSettings};
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Url;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use App\Models\Ad;

class CartSummary extends Component implements HasForms
{
    use InteractsWithForms, HelperTraits, SEOToolsTrait;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    public $carts;
    public ?array $data = [];
    public $isModalOpen = false;
    public $locations;
    public $deliveryAddress;
    protected $listeners = ['updateQuantity'];
    public $id;

    /**
     * Mount lifecycle hook.
     */
    public function mount($id = null)
    {
        if(!isECommerceAddToCardEnabled())
        {
            abort(404);
        }
        $this->id = $id;
        $this->updateCartsFromSession();
        $this->initializeCarts();
        $this->setSeoData();
        $this->initializeDeliveryAddress();
        $this->UpdateMaxQuantityWhenPlacingOrder();
    }

    /**
     * Initializes the user's carts based on authentication status.
     *
     * If the user is authenticated, retrieves carts associated with the user,
     * optionally filtering by a specific cart ID. If the user is not authenticated,
     * retrieves cart items from the session, attaches the corresponding Ad model,
     * and converts each item to an object for Blade compatibility.
     */

    public function initializeCarts()
    {
        if (auth()->check()) {
            $this->carts = auth()->user()->carts()
                ->when($this->id, fn($query) => $query->where('id', $this->id))
                ->get();
        } else {
            $cartItems = session()->get('cart', []);

            $this->carts = collect($cartItems)->map(function ($cartItem) {
                $cartItem['ad'] = Ad::find($cartItem['ad_id']); // Attach Ad model
                return (object) $cartItem; // Convert array to object for Blade compatibility
            });
        }
    }

    /**
     * Redirects the user to the login page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToLogin()
    {

        return redirect()->route('login');
    }


    protected function updateCartsFromSession()
    {
        $carts = session()->get('cart', []);

        if (auth()->check()) {
            foreach ($carts as $cart) {
                $existingCart = auth()->user()->carts()->where('ad_id', $cart['ad_id'])->first();

                if ($existingCart) {
                    $totalQuantity = $existingCart->quantity + $cart['quantity'];
                    $maxQuantity = getECommerceMaximumQuantityPerItem();

                    $existingCart->update([
                        'quantity' => min($totalQuantity, $maxQuantity)
                    ]);
                }
                else {
                    auth()->user()->carts()->create($cart);
                }
            }
            session()->forget(keys: 'cart');
        }
    }

    protected function initializeDeliveryAddress()
    {
        if (auth()->check()) {
            $this->locations = auth()->user()->locations()->where('type', 'delivery_address')->get();

            if ($this->locations->isEmpty()) {
                session()->put('delivery-address', null);
            } elseif (!session()->has('delivery-address')) {
                session()->put('delivery-address', $this->locations[0]);
            }
        }

        $this->deliveryAddress = session()->get('delivery-address', null);
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->locationForm())->statePath('data');
    }

    public function selectAddress($addressId)
    {
        $this->helperSelectAddress($addressId);
    }

    public function addAddress(): void
    {
        $this->helperAddAddress($this->form);
    }

    /**
     * Removes a cart item based on the provided cart ID.
     *
     * @param int|string $cartID The ID of the cart item to be removed.
     * @return void
     */
    public function removeCart($cartID)
    {
        if (auth()->check()) {
            Cart::destroy($cartID);
        } else {
            $cart = session()->get('cart', []);
            $updatedCart = collect($cart)->reject(fn($item) => $item['cart_id'] === $cartID)->values()->toArray();
            session()->put('cart', $updatedCart);
        }

        Notification::make()
            ->title(__('messages.t_removed_successfully'))
            ->success()
            ->send();

        $this->initializeCarts();
    }

    /**
     * Updates the quantity of a cart item.
     *
     * If the specified quantity is less than or equal to zero, the item is removed from the cart.
     * If the quantity exceeds the maximum allowed per item, a warning notification is sent.
     * Updates the cart item quantity for authenticated users in the database, or in the session for guests.
     * Sends a success notification upon successful update and reinitializes the cart data.
     *
     * @param int $cartID The ID of the cart item to update.
     * @param int $quantity The new quantity for the cart item.
     * @return void
     */
    public function updateQuantity($cartID, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeCart($cartID);
            return;
        }

        $maxQuantity = getECommerceMaximumQuantityPerItem();

        if ($quantity > $maxQuantity) {
            Notification::make()
                ->title(__('messages.t_cart_max_limit_reached', ['max' => $maxQuantity]))
                ->warning()
                ->send();
            return;
        }

        if (auth()->check()) {
            Cart::where('id', $cartID)->update(['quantity' => $quantity]);
        } else {
            $cart = session()->get('cart', []);

            $cart = collect($cart)->map(function ($item) use ($cartID, $quantity) {
                if ($item['cart_id'] === $cartID) {
                    $item['quantity'] = $quantity;
                }
                return $item;
            })->values()->toArray();

            session()->put('cart', $cart);
        }
        Notification::make()
            ->title(__('messages.t_updated_successfully'))
            ->success()
            ->send();

        $this->initializeCarts();
    }

    public function UpdateMaxQuantityWhenPlacingOrder()
    {
        if(isECommerceQuantityOptionEnabled() && is_ecommerce_active())
        {
            foreach ($this->carts as $cart) {
                $maxQuantity = getECommerceMaximumQuantityPerItem();
                $finalQuantity = ($cart->quantity >= $maxQuantity) ? $maxQuantity : $cart->quantity;
                if(auth()->check()){
                    $cart->update(['quantity' => $finalQuantity]);
                }else{
                    $cart->quantity = $finalQuantity;
                }
            }
        }
    }

    public function createTemporaryOrderAndRedirectToCheckoutPage($orderType = RESERVATION_TYPE_RETAIL)
    {

        $totalAmount = $this->carts->sum(function ($cart) {
            $price = $cart->ad->isEnabledOffer() && $cart->ad->offer_price ? $cart->ad->offer_price : $cart->ad->price;

            return $cart->quantity * $price;
        });

        if ($orderType == RESERVATION_TYPE_POINT_VAULT && max(auth()->user()->wallet?->points, 0) < $totalAmount) {
            Notification::make()
                ->title(__('messages.t_insufficient_points'))
                ->success()
                ->send();
        }

        $temporaryOrder = TemporaryOrder::create([
            'user_id' => auth()->id(),
            'items' => $this->carts->pluck('id'),
            'total_amount' => $totalAmount,
            'status' => 'order_created',
            'shipping_address_id' => $this->deliveryAddress->id,
        ]);

        session()->put('current_temporary_order', $temporaryOrder->id);
        session()->put('current_total_amount', $temporaryOrder->total_amount);

        if ($orderType == RESERVATION_TYPE_POINT_VAULT) {

            $wallet = auth()->user()->wallet;

            $wallet->update([
                'points' => $wallet->points - $totalAmount,
                'points_on_hold' => $wallet->points_on_hold + $totalAmount
            ]);

            return redirect()->route('reservation.payment-callback.point_based_order', $temporaryOrder->id);
        }

        return redirect()->route('reservation.checkout-summary');
    }

    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);

        $title = __('messages.t_cart_summary') . ' ' . ($generalSettings->separator ?? '-') . ' ' .
            ($generalSettings->site_name ?? config('app.name'));
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($seoSettings->meta_description);
    }

    /**
     * Render the Cart Summary view.
     */
    public function render()
    {
        return view('livewire.reservation.cart-summary');
    }
}
