<div x-data="{address: false}">
    <livewire:layout.header isMobileHidden lazy />
    <x-page-header title="{{ __('messages.t_checkout_summary') }}" isMobileHidden :$referrer />

    <x-user-navigation />
    @if ($carts->isEmpty())
    @include('livewire.reservation._parties.empty-cart')
    @else
    {{-- cart view section  --}}
    <section class=" container mx-auto px-4">
        <a href="/cart-summary" class=" flex items-center gap-x-2 py-5 md:py-8 cursor-pointer w-fit">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class=" w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <h2 class=" text-xl font-semibold">{{ __('messages.t_back_to_cart') }}</h2>
        </a>

        {{-- payment section --}}
        <div class=" grid grid-cols-1 lg:grid-cols-5 lg:gap-x-14">
            {{-- back cart section  --}}
            <div class=" lg:col-span-3">
                @if ($deliveryAddress)
                <div
                    class=" flex flex-wrap gap-3 justify-between border border-gray-950/5 dark:border-white/10 classic:border-black bg-white dark:bg-gray-900 rounded-lg p-6">
                    <div>
                        <h4 class="font-semibold pb-2">{{ __('messages.t_delivery_to') }}: {{$deliveryAddress->name}}, {{$deliveryAddress->postal_code}}. </h4>
                        <span class="text-sm block">{{ $deliveryAddress->house_number }}, {{ $deliveryAddress->address }}, {{ $deliveryAddress->city->name }}, {{ $deliveryAddress->state->name }}, {{ $deliveryAddress->country->name }}.</span>
                    </div>
                    <x-filament::modal width="3xl" id="change-addess">
                        <x-slot name="trigger">
                            <svg class=" stroke-black dark:stroke-white" width="20" height="21" viewBox="0 0 20 21" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M14.0517 4.23919L15.4575 2.83252C15.7506 2.53946 16.148 2.37482 16.5625 2.37482C16.977 2.37482 17.3744 2.53946 17.6675 2.83252C17.9606 3.12559 18.1252 3.52307 18.1252 3.93752C18.1252 4.35198 17.9606 4.74946 17.6675 5.04252L8.81833 13.8917C8.37777 14.332 7.83447 14.6556 7.2375 14.8334L5 15.5L5.66667 13.2625C5.8444 12.6656 6.16803 12.1223 6.60833 11.6817L14.0517 4.23919ZM14.0517 4.23919L16.25 6.43752M15 12.1667V16.125C15 16.6223 14.8025 17.0992 14.4508 17.4508C14.0992 17.8025 13.6223 18 13.125 18H4.375C3.87772 18 3.40081 17.8025 3.04917 17.4508C2.69754 17.0992 2.5 16.6223 2.5 16.125V7.37502C2.5 6.87774 2.69754 6.40083 3.04917 6.0492C3.40081 5.69757 3.87772 5.50002 4.375 5.50002H8.33333"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <h4 class=" font-medium">{{ __('messages.t_change_address') }}</h4>
                        </x-slot>

                        <x-slot name="heading">
                            {{ __('messages.t_please_select_address') }}
                        </x-slot>
                        @foreach ($locations as $location)
                        <div wire:click="selectAddress('{{$location->id}}')" class="flex justify-between border border-gray-950/5 dark:border-white/10 classic:border-black bg-white dark:bg-gray-900 rounded-lg p-3">
                            <div class="flex">
                                <div>
                                    <input name="address"
                                        @if ($deliveryAddress->id == $location->id) checked @endif
                                    type="checkbox" class="fi-checkbox-input rounded border-none bg-white shadow-sm ring-1 transition duration-75 checked:ring-0 focus:ring-2 focus:ring-offset-0 disabled:pointer-events-none disabled:bg-gray-50 disabled:text-gray-50 disabled:checked:bg-current disabled:checked:text-gray-400 dark:bg-white/5 dark:disabled:bg-transparent dark:disabled:checked:bg-gray-600 text-primary-600 ring-gray-950/10 focus:ring-primary-600 checked:focus:ring-primary-500/50 dark:text-primary-500 dark:ring-white/20 dark:checked:bg-primary-500 dark:focus:ring-primary-500 dark:checked:focus:ring-primary-400/50 dark:disabled:ring-white/10">
                                </div>
                                <div class="pl-5">
                                    <h4 class="font-semibold pb-2">{{ __('messages.t_delivery_to') }}{{$location->name}}, {{$location->postal_code}}. </h4>
                                    <span class="text-sm block">{{ $location->house_number }}, {{ $location->address }}, {{ $location->city->name }}, {{ $location->state->name }}, {{ $location->country->name }}.</span>
                                </div>

                            </div>
                        </div>
                        @endforeach

                        <div @click="$dispatch('open-modal', {id: 'add-address'})" class="cursor-pointer flex justify-between border border-gray-950/5 dark:border-white/10 classic:border-black bg-white dark:bg-gray-900 rounded-lg p-3">
                            <h4 class="flex font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[1.25rem] h-[1.25rem] dark:text-gray-500 pt-[2px]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <span>{{ __('messages.t_add_address') }}</span>
                            </h4>
                        </div>
                    </x-filament::modal>
                </div>
                @else
                <div class="flex justify-between border border-gray-950/5 dark:border-white/10 classic:border-black bg-white dark:bg-gray-900 rounded-lg p-6">
                    <div>
                        <h4 class=" font-medium">{{ __('messages.t_please_select_address') }}</h4>
                    </div>
                    <div @click="$dispatch('open-modal', {id: 'add-address'})" class=" flex items-center gap-x-1 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[1.25rem] h-[1.25rem] dark:text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>

                        <h4 class=" font-medium">{{ __('messages.t_add_address') }}</h4>
                    </div>
                </div>
                @endif

                <div class=" pt-5">
                    <h2 class="mb-10 text-xl font-semibold">{{ __('messages.t_payment_options')}}</h2>
                    @if(count($this->initializePaymentOptions()) >= 1)
                    <form wire:submit>
                        <div class="mb-5 payment-methods">
                            {{ $this->paymentOptionForm }}
                        </div>
                    </form>
                    @else
                     @include('components.empty-payment')
                    @endif
                </div>
            </div>



            {{-- summary section  --}}
            <div class=" lg:col-span-2 sticky top-40 h-fit">
                <div class=" border border-gray-950/5 dark:border-white/10 classic:border-black rounded-lg bg-white dark:bg-gray-900 p-5">
                    <h3 class=" text-lg font-semibold">{{ __('messages.t_order_summary') }}</h3>
                    <div class=" grid grid-cols-1 gap-y-5 pt-5">
                        @foreach ($carts as $cart)
                        <div class=" flex justify-between items-start md:items-center">
                            <h4>{{ $cart->ad->title }} ({{$cart->quantity}} Items)</h4>
                            @if ($cart->ad->isEnabledOffer() && $cart->ad?->offer_price)
                            <h4 class=" font-medium whitespace-nowrap">{{ currencyToPointConversion(formatPriceWithCurrency($cart->ad?->offer_price * $cart->quantity) ) }}</h4>
                            @else
                            <h4 class=" font-medium whitespace-nowrap">{{ currencyToPointConversion(formatPriceWithCurrency($cart->ad->price * $cart->quantity) ) }}</h4>
                            @endif
                        </div>
                        @endforeach
                        <div class=" flex justify-between items-start md:items-center">
                            <h4>{{ __('messages.t_delivery_charges') }}</h4>
                            <h4 class=" text-[#307A16] font-medium">{{ __('messages.t_free_charge') }}</h4>
                        </div>
                        <div class=" flex justify-between items-start md:items-center">
                            <h4>{{ __('messages.t_subtotal') }}</h4>
                            <h4 class="  font-medium whitespace-nowrap">{{ currencyToPointConversion(formatPriceWithCurrency($subtotalAmount) ) }}</h4>
                        </div>
                        @if(!isEnablePointSystem() && isECommerceTaxOptionEnabled() && is_ecommerce_active())
                            <div class=" flex justify-between items-start md:items-center">
                                <h4>{{ __('messages.t_tax') }}</h4>
                                <h4 class="  font-medium whitespace-nowrap">{{ currencyToPointConversion(formatPriceWithCurrency($tax) ) }}</h4>
                            </div>
                        @endif

                        <div class=" border-t border-gray-950/5 dark:border-white/10 classic:border-black"></div>
                        <div class=" leading-none text-lg font-bold flex justify-between items-center">
                            <h2>{{ __('messages.t_total_amount') }}</h2>
                            <h2>{{ currencyToPointConversion(formatPriceWithCurrency($totalAmount)) }}</h2>
                        </div>
                        @if ($this->defaultCurrency && $this->isDifferentRate)
                        <div class=" leading-none text-lg font-bold flex justify-between items-center">
                            <h2>{{ __('messages.t_total_including_exchange_rate') }}</h2>
                            <h2>{{formatPriceWithCurrency($convertedTotal) }}</h2>
                        </div>
                        @endif

                        {{-- <div class=" leading-none text-lg font-bold flex justify-between items-center">
                            <h2>{{ __('messages.t_total_amount') }}</h2>
                            <h2>{{ currencyToPointConversion(formatPriceWithCurrency($convertedTotal)) }}</h2>
                        </div> --}}
                    </div>
                </div>
                <div class=" pt-5">
                    @if ($payment_method)
                    <!-- :$type :data="$this->paymentData" :total="$this->defaultCurrency && $this->isDifferentRate ? $this->convertedTotal : $totalAmount" :$id :$subtotal :$tax -->
                    @if (str_starts_with($payment_method, 'offline_'))
                    <x-button.secondary wire:click="offlinePaymentOrderNow()" size="lg" class=" font-semibold w-full mb-4">{{ __('messages.t_place_order') }}</x-button.secondary>
                    @else
                    <livewire:dynamic-component :key="$currentPayment" :component="$currentPayment" :totalAmount="$this->defaultCurrency && $this->isDifferentRate ? $this->convertedTotal : $totalAmount" />
                    @endif

                    @endif
                </div>
            </div>
        </div>
    </section>


    <!-- Address model -->
    <x-filament::modal width="3xl" id="add-address">
        <x-slot name="heading">
            {{ __('messages.t_add_new_address') }}
        </x-slot>

        <form wire:submit="addAddress">
            {{ $this->locationForms }}

            <button type="submit" style="float: right;" class=" inline-flex items-center justify-center px-4 py-2 text-base border  rounded-xl  disabled:opacity-50 disabled:pointer-events-none transition bg-black dark:bg-white/10 border-black text-white hover:bg-gray-700 focus:outline-none  font-semibold  mt-8">
                {{__('messages.t_submit')}}
            </button>
        </form>
    </x-filament::modal>
    @endif
</div>
