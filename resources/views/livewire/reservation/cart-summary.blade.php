<div x-data="{address: false}">
    <livewire:layout.header isMobileHidden lazy />
    <x-page-header title="{{ __('messages.t_cart_summary') }}" isMobileHidden :$referrer />

    <x-user-navigation />

    {{-- cart section --}}
    @if ($carts->isEmpty())
    @include('livewire.reservation._parties.empty-cart')
    @else
    @if (isECommerceQuantityOptionEnabled())
    <div class="container mx-auto px-4 mt-2">
        <p class="text-sm text-gray-500 ">
            {{ __('messages.t_max_quantity_helper', ['quantity' => getECommerceMaximumQuantityPerItem()]) }}
        </p>
    </div>
    @endif
    <section class=" container mx-auto px-4 py-8 md:py-10 grid grid-cols-1 lg:grid-cols-5 gap-y-7 lg:gap-x-14">
        {{-- card section --}}
        <div
            class=" lg:col-span-3 border border-gray-950/5 dark:border-white/10 classic:border-black rounded-lg bg-white dark:bg-gray-900">
            @if(auth()->check())
            @if ($deliveryAddress)
            <div
                class=" flex flex-wrap gap-3 justify-between items-center border-b border-gray-950/5 dark:border-white/10 classic:border-black p-6">
                <div>
                    <h4 class="font-semibold pb-2">{{ __('messages.t_delivery_to') }}: {{$deliveryAddress->name}},
                        {{$deliveryAddress->postal_code}}. </h4>
                    <span class="text-sm block">{{ $deliveryAddress->house_number }}, {{ $deliveryAddress->address }},
                        {{ $deliveryAddress->city?->name }}, {{ $deliveryAddress->state?->name }}, {{
                        $deliveryAddress->country?->name }}.</span>
                </div>
                <div class=" flex items-center gap-x-1 cursor-pointer">
                    <x-filament::modal width="3xl" id="change-addess">
                        <x-slot name="trigger">
                            <svg class=" stroke-black dark:stroke-white" width="20" height="21" viewBox="0 0 20 21"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M14.0517 4.23919L15.4575 2.83252C15.7506 2.53946 16.148 2.37482 16.5625 2.37482C16.977 2.37482 17.3744 2.53946 17.6675 2.83252C17.9606 3.12559 18.1252 3.52307 18.1252 3.93752C18.1252 4.35198 17.9606 4.74946 17.6675 5.04252L8.81833 13.8917C8.37777 14.332 7.83447 14.6556 7.2375 14.8334L5 15.5L5.66667 13.2625C5.8444 12.6656 6.16803 12.1223 6.60833 11.6817L14.0517 4.23919ZM14.0517 4.23919L16.25 6.43752M15 12.1667V16.125C15 16.6223 14.8025 17.0992 14.4508 17.4508C14.0992 17.8025 13.6223 18 13.125 18H4.375C3.87772 18 3.40081 17.8025 3.04917 17.4508C2.69754 17.0992 2.5 16.6223 2.5 16.125V7.37502C2.5 6.87774 2.69754 6.40083 3.04917 6.0492C3.40081 5.69757 3.87772 5.50002 4.375 5.50002H8.33333"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <h4 class=" font-medium">{{ __('messages.t_change_address') }}</h4>
                        </x-slot>

                        <x-slot name="heading">
                            {{ __('messages.t_please_select_address') }}
                        </x-slot>
                        @foreach ($locations as $location)
                        <div role="button" wire:click="selectAddress('{{$location->id}}')"
                            class="flex justify-between border border-gray-950/5 dark:border-white/10 classic:border-black bg-white dark:bg-gray-900 rounded-lg p-3">
                            <div class="flex">
                                <div>
                                    <input name="address" @if ($deliveryAddress->id == $location->id) checked @endif
                                    type="checkbox" class="fi-checkbox-input rounded border-none bg-white shadow-sm
                                    ring-1 transition duration-75 checked:ring-0 focus:ring-2 focus:ring-offset-0
                                    disabled:pointer-events-none disabled:bg-gray-50 disabled:text-gray-50
                                    disabled:checked:bg-current disabled:checked:text-gray-400 dark:bg-white/5
                                    dark:disabled:bg-transparent dark:disabled:checked:bg-gray-600 text-primary-600
                                    ring-gray-950/10 focus:ring-primary-600 checked:focus:ring-primary-500/50
                                    dark:text-primary-500 dark:ring-white/20 dark:checked:bg-primary-500
                                    dark:focus:ring-primary-500 dark:checked:focus:ring-primary-400/50
                                    dark:disabled:ring-white/10">
                                </div>
                                <div class="pl-5">
                                    <h4 class="font-semibold pb-2">-{{ __('messages.t_delivery_to') }}
                                        {{$location->name}},
                                        {{$location->postal_code}}. </h4>
                                    <span class="text-sm block">{{ $location->house_number }}, {{ $location->address }},
                                        {{ $location->city->name }}, {{ $location->state->name }}, {{
                                        $location->country->name }}.</span>
                                </div>

                            </div>
                        </div>
                        @endforeach

                        <button type="button" @click="$dispatch('open-modal', {id: 'add-address'})"
                            class="cursor-pointer flex justify-between border border-gray-950/5 dark:border-white/10 classic:border-black bg-white dark:bg-gray-900 rounded-lg p-3">
                            <h4 class="flex font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="w-[1.25rem] h-[1.25rem] dark:text-gray-500 pt-[2px]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <span>{{ __('messages.t_add_address') }}</span>
                            </h4>
                        </button>
                    </x-filament::modal>
                </div>
            </div>

            @else
            <div
                class=" flex justify-between items-center border-b border-gray-950/5 dark:border-white/10 classic:border-black p-6">
                <div>
                    <h4 class=" font-medium">{{ __('messages.t_please_select_address') }}</h4>
                </div>
                <button type="button" @click="$dispatch('open-modal', {id: 'add-address'})"
                    class="cursor-pointer flex items-center gap-x-1 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-[1.25rem] h-[1.25rem] dark:text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>

                    <h4 class=" font-medium">{{ __('messages.t_add_address') }}</h4>
                </button>
            </div>
            @endif
            @endif
            @php
            $totalAmount = 0;
            @endphp

            @if(auth()->check())

            @foreach ($carts as $index => $cart)
            @if(!empty($cart->ad))

            <div wire:key="cart-{{$cart->id}}"
                class=" px-6 py-7 @if(count($carts) != ($index + 1)) border-b border-gray-950/5 dark:border-white/10 classic:border-black @endif">
                <a target="_blank" href="{{ route('ad.overview', $cart->ad?->slug) }}" class=" flex gap-x-4">
                    <div>
                        @php
                        $imageProperties = $cart->ad?->image_properties;
                        $altText = $imageProperties['1'] ?? $cart->ad?->title;
                        @endphp
                        <img src="{{ $cart->ad?->primaryImage ?? asset('/images/placeholder.jpg') }}"
                            alt="{{ $altText }}"
                            class="aspect-square object-cover h-32 flex w-full md:h-[7rem] rounded-xl">
                    </div>
                    <div class=" flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $cart->ad?->title }}</h3>
                            <span class=" text-[#71717A]">{{ $cart->ad?->category?->name }}</span>
                            <div class=" text-sm text-[#71717A] mt-2">{{ __('messages.t_cart_summary') }} {{
                                $cart->ad?->user?->name }}</div>
                        </div>

                        <div class=" flex items-center gap-x-2">
                            @if ($cart->ad->isEnabledOffer() && $cart->ad?->offer_price)

                            <span class=" text-sm text-[#71717A] line-through">{{
                                currencyToPointConversion(formatPriceWithCurrency($cart->ad?->price),
                                $cart->ad?->adType?->marketplace) }}</span>

                            <span
                                class=" text-sm font-semibold">{{currencyToPointConversion($cart->ad?->offer_price?config('app.currency_symbol').'
                                '. \Number::format(floor($cart->ad->offer_price), locale:
                                $paymentSettings->currency_locale):null, $cart->ad?->adType?->marketplace)}}</span>



                            @if ($cart->ad->getOfferPercentage() )
                            <span class=" text-sm text-[#FDAE4B] font-semibold">{{ $cart->ad->getOfferPercentage() }} %
                                OFF</span>
                            @endif
                            @php
                            $totalAmount = $totalAmount + ($cart->ad->offer_price * $cart->quantity);

                            @endphp

                            @else
                            @php
                            $totalAmount =$totalAmount + ($cart->ad->price * $cart->quantity);
                            @endphp
                            <span class=" text-sm font-semibold">{{
                                currencyToPointConversion(formatPriceWithCurrency($cart->ad?->price),
                                $cart->ad?->adType?->marketplace) }}</span>
                            @endif
                        </div>
                    </div>
                </a>

                <div class=" flex gap-x-[45px] items-end pt-4">
                    @if (isECommerceQuantityOptionEnabled() && !isEnablePointSystem())
                    <div x-data="{ quantity: @entangle('cart.quantity').defer }">
                        <x-label value="Quantity" class=" !text-base" />
                        <x-input min="1" max="{{ getECommerceMaximumQuantityPerItem() }}" type="number"
                            value="{{ $cart->quantity }}" wire:model="cart.quantity"
                            wire:change="updateQuantity('{{$cart->id}}', $event.target.value)"
                            class="mt-2 border border-[#B0B0B0] classic:border-black dark:border-white/10 focus:ring-0 focus:!outline-none dark:focus-within:border-2 dark:focus-within:!border-primary-600 dark:!bg-zinc-800 py-1 !rounded-md max-w-[80px]" />

                    </div>
                    @endif
                    <button type="button" class=" flex items-center gap-x-1 cursor-pointer pb-1.5"
                        wire:click="removeCart('{{$cart->id}}')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-[1.25rem] h-[1.25rem] dark:text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>

                        <span class="font-semibold underline underline-offset-1">{{ __('messages.t_remove') }}</span>
                    </button>
                </div>

            </div>
            @endif

            @endforeach
            @else
            @foreach ($carts as $index => $cart)
            @if(!empty($cart->ad))
            <div wire:key="cart-{{$cart->cart_id}}"
                class=" px-6 py-7 @if(count($carts) != ($index + 1)) border-b border-gray-950/5 dark:border-white/10 classic:border-black @endif">
                <a target="_blank" href="{{ route('ad.overview', $cart->ad?->slug) }}" class=" flex gap-x-4">
                    <div>
                        @php
                        $imageProperties = $cart->ad?->image_properties;
                        $altText = $imageProperties['1'] ?? $cart->ad?->title;
                        @endphp
                        <img src="{{ $cart->ad?->primaryImage ?? asset('/images/placeholder.jpg') }}"
                            alt="{{ $altText }}"
                            class="aspect-square object-cover h-32 flex w-full md:h-[7rem] rounded-xl">
                    </div>
                    <div class=" flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $cart->ad?->title }}</h3>
                            <span class=" text-[#71717A]">{{ $cart->ad?->category?->name }}</span>
                            <div class=" text-sm text-[#71717A] mt-2">{{ __('messages.t_cart_summary') }} {{
                                $cart->ad?->user?->name }}</div>
                        </div>

                        <div class=" flex items-center gap-x-2">
                            @if ($cart->ad->isEnabledOffer() && $cart->ad?->offer_price)

                            <span class=" text-sm text-[#71717A] line-through">{{
                                currencyToPointConversion(formatPriceWithCurrency($cart->ad?->price),
                                $cart->ad?->adType?->marketplace) }}</span>

                            <span
                                class=" text-sm font-semibold">{{currencyToPointConversion($cart->ad?->offer_price?config('app.currency_symbol').'
                                '. \Number::format(floor($cart->ad->offer_price), locale:
                                $paymentSettings->currency_locale):null, $cart->ad?->adType?->marketplace)}}</span>



                            @if ($cart->ad->getOfferPercentage() )
                            <span class=" text-sm text-[#FDAE4B] font-semibold">{{ $cart->ad->getOfferPercentage() }} %
                                OFF</span>
                            @endif
                            @php
                            $totalAmount = $totalAmount + ($cart->ad->offer_price * $cart->quantity);
                            @endphp

                            @else
                            @php
                            $totalAmount =$totalAmount + ($cart->ad->price * $cart->quantity);
                            @endphp
                            <span class=" text-sm font-semibold">{{
                                currencyToPointConversion(formatPriceWithCurrency($cart->ad?->price),
                                $cart->ad?->adType?->marketplace) }}</span>
                            @endif
                        </div>
                    </div>
                </a>



                <div class=" flex gap-x-[45px] items-end pt-4">
                    @if (isECommerceQuantityOptionEnabled() && !isEnablePointSystem())
                    <div x-data="{ quantity: @entangle('cart.quantity').defer }">
                        <x-label value="Quantity" class=" !text-base" />
                        <x-input min="1" max="{{ getECommerceMaximumQuantityPerItem() }}" type="number"
                            value="{{ $cart->quantity }}" wire:model="cart.quantity"
                            wire:change="updateQuantity('{{$cart->cart_id}}', $event.target.value)"
                            class="mt-2 border border-[#B0B0B0] classic:border-black dark:border-white/10 focus:ring-0 focus:!outline-none dark:focus-within:border-2 dark:focus-within:!border-primary-600 dark:!bg-zinc-800 py-1 !rounded-md max-w-[80px]" />

                    </div>
                    @endif
                    <button type="button" class=" flex items-center gap-x-1 cursor-pointer pb-1.5"
                        wire:click="removeCart('{{$cart->cart_id}}')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-[1.25rem] h-[1.25rem] dark:text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>

                        <span class="font-semibold underline underline-offset-1">{{ __('messages.t_remove') }}</span>
                    </button>
                </div>

            </div>
            @endif
            @endforeach
            @endif
        </div>

        @php
        $tax = (!isEnablePointSystem() && isECommerceTaxOptionEnabled() && is_ecommerce_active()) ? ($totalAmount * getECommerceTaxRate()) / 100 : 0;
        $totalWithTax = $totalAmount + $tax;
        @endphp

        {{-- summary section --}}
        <div class=" lg:col-span-2 sticky top-40 h-fit">
            <div
                class=" border border-gray-950/5 dark:border-white/10 classic:border-black rounded-lg bg-white dark:bg-gray-900 p-5">
                <h3 class=" text-lg font-semibold">{{ __('messages.t_order_summary') }}</h3>
                <div class=" grid grid-cols-1 gap-y-5 pt-5">
                    <div class=" flex justify-between items-center">
                        <h4>{{ __('messages.t_cart_quantity', ['cartquantity' => $carts->sum('quantity')]) }}</h4>
                        <h4 class=" font-medium">{{ currencyToPointConversion(formatPriceWithCurrency($totalAmount)) }}
                        </h4>
                    </div>
                    @if(!isEnablePointSystem() && isECommerceTaxOptionEnabled() && is_ecommerce_active())
                    <div class=" flex justify-between items-center">
                        <h4>{{ __('messages.t_tax') }}</h4>
                        <h4 class=" text-[#307A16] font-medium">{{ formatPriceWithCurrency($tax) }}</h4>
                    </div>
                    @endif
                    <div class=" flex justify-between items-center">
                        <h4>{{ __('messages.t_delivery_charges') }}</h4>
                        <h4 class=" text-[#307A16] font-medium">{{ __('messages.t_free_charge') }}</h4>
                    </div>
                    <div class=" border-t border-gray-950/5 dark:border-white/10 classic:border-black"></div>
                    <div class=" leading-none text-lg font-bold flex justify-between items-center">
                        <h2>{{ __('messages.t_total_amount') }}</h2>
                        <h2>{{ currencyToPointConversion(formatPriceWithCurrency($totalWithTax)) }}</h2>
                    </div>
                </div>
            </div>
            <div class=" pt-5">
                @if (!session()->has('delivery-address') && auth()->check())
                <x-button.secondary @click="$dispatch('open-modal', {id: 'add-address'})" size="lg"
                    class=" font-semibold w-full mb-4 dark:!bg-primary-600 cursor-pointer">{{
                    __('messages.t_place_order') }}</x-button.secondary>
                @elseif (isEnablePointSystem())
                @if ($totalAmount > max(auth()->user()->wallet?->points, 0))
                <x-button.secondary onclick="window.location='/buy-point'" size="lg"
                    class=" font-semibold w-full mb-4 dark:!bg-secondary-600">{{ __('messages.t_buy_point') }}
                </x-button.secondary>
                <p class="text-red-700 font-semibold">{{ __('messages.t_you_do_not_have_enough') }}</p>
                @else
                <x-button.secondary
                    wire:click="{{ auth()->check() ? 'createTemporaryOrderAndRedirectToCheckoutPage(\'' . RESERVATION_TYPE_POINT_VAULT . '\')' : 'redirectToLogin' }}"
                    size="lg" class="font-semibold w-full mb-4 dark:!bg-primary-600">
                    {{ __('messages.t_place_order') }}
                </x-button.secondary>
                @endif
                @else
                <x-button.secondary
                    wire:click="{{ auth()->check() ? 'createTemporaryOrderAndRedirectToCheckoutPage' : 'redirectToLogin' }}"
                    size="lg" class="font-semibold w-full mb-4 dark:!bg-primary-600">
                    {{ __('messages.t_place_order') }}
                </x-button.secondary>

                @endif

                @if (!isEnablePointSystem() && auth()->check())
                <x-button onclick="window.location='/'" size="lg"
                    class=" dark:bg-white/10 dark:text-white font-medium w-full border-black text-black">{{
                    __('messages.t_add_more_item') }}</x-button>
                @endif
            </div>
        </div>

    </section>
    @endif
    <!-- Address model -->
    <x-filament::modal width="3xl" id="add-address">
        <x-slot name="heading">
            {{ __('messages.t_add_new_address') }}
        </x-slot>

        <form wire:submit="addAddress">
            {{ $this->form }}

            <button type="submit" style="float: right;"
                class=" inline-flex items-center justify-center px-4 py-2 text-base border  rounded-xl  disabled:opacity-50 disabled:pointer-events-none transition bg-black dark:bg-primary-600 border-black text-white hover:bg-gray-700 focus:outline-none  font-semibold  mt-8">
                {{__('messages.t_submit')}}
            </button>
        </form>
    </x-filament::modal>
</div>