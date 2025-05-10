<div>
    {{-- header section --}}
    <livewire:layout.header context="home" lazy />

    <div>
        <style>
            .time-line {
                padding-left: 20px;
                position: relative;
            }

            .time-line::before {
                content: "";
                position: absolute;
                height: var(--line-height, 40px);
                border-left: 5px solid #FDAE4B;
                animation: grow-line 5s ease-out forwards;
            }

            /* Animation for the vertical line */
            @keyframes grow-line {
                from {
                    height: 0;
                }

                to {
                    height: var(--line-height, 65px);
                }
            }

            .order-dot {
                position: relative;
            }

            .order-dot::after {
                background-color: red;
            }

            .order-dot::before {
                content: "";
                display: block;
                background-color: black;
                width: 8px;
                height: 8px;
                border-radius: 100%;
                position: absolute;
                left: -22px;
            }

            .fi-section-content-ctn {
                border: none;
            }

            .fi-section-content {
                padding: 5px 24px 0px;
            }
        </style>

        <div class=" container mx-auto px-4">
            <a href="{{ route('reservation.my-purchases') }}"
                class=" flex items-center gap-x-2 py-5 sm:pt-7 sm:pb-9 cursor-pointer w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class=" w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <h2 class=" text-lg sm:text-xl font-semibold">{{__('messages.t_back_to_my_order')}}</h2>
            </a>
            <div class=" flex flex-wrap gap-2 justify-between items-center">
                <h2 class=" text-lg sm:text-xl font-semibold">
                    {{__('messages.t_placed_order_id')}}{{$order->order_number}}</h2>

                @if (!$isOrderCancel)
                <x-filament::button wire:click="cancelMyOrder()" color="gray">{{__('messages.t_cancel_my_orders')}}
                </x-filament::button>
                @endif
            </div>

            {{-- order section --}}
            <section class=" py-8 sm:py-10 grid grid-cols-1 md:grid-cols-5 gap-y-7 md:gap-x-14">
                {{-- card section --}}
                <div
                    class=" md:col-span-3 md:row-span-3 border border-gray-950/5 dark:border-white/10 classic:border-black rounded-lg bg-white dark:bg-gray-900 h-fit">
                    @php
                    $quantity = 0;
                    @endphp
                    @foreach ($order->items as $item)

                    @php
                    $quantity += $item->quantity;
                    @endphp

                    <div
                        class=" p-5 sm:px-6 sm:py-7  border-b border-gray-950/5 dark:border-white/10 classic:border-black ">
                        <a target="_blank" href="{{ route('ad.overview', $item->ad?->slug?? '#') }}" class=" flex gap-4">
                            <div>
                                @php
                                $imageProperties = $item->ad?->image_properties;
                                $altText = $imageProperties['1'] ?? $item->ad?->title;
                                @endphp
                                <img src="{{ $item->ad?->primaryImage ?? asset('/images/placeholder.jpg') }}"
                                    alt="{{ $altText }}"
                                    class="aspect-square object-cover h-20 flex w-full md:h-[7rem] rounded-xl">
                            </div>
                            <div class=" flex flex-col justify-between w-full">
                                <div class=" w-full">
                                    <div class=" flex flex-wrap justify-between items-center w-full">
                                        <h3 class="sm:text-lg font-semibold">{{ $item->ad?->title ?? __('messages.t_N/A') }}</h3>
                                        <span class=" font-medium">x{{$item->quantity}}</span>
                                    </div>
                                    <div class=" text-sm sm:text-base text-[#71717A]">{{ $item->ad?->category?->name }}
                                        <span
                                            class=" md:block text-xs sm:text-sm text-[#71717A]">{{__('messages.t_order_seller')}}
                                            {{
                                            $item->ad?->user->name }}</span>
                                    </div>
                                </div>
                                <div class="items-center gap-x-3 md:gap-x-10 w-full">
                                    <span
                                        class=" dark:text-white text-xs sm:text-sm font-semibold">{{
                                        ($order->order_type == RESERVATION_TYPE_POINT_VAULT) ?
                                        currencyToPointConversion(config('app.currency_symbol').' '.
                                        \Number::format(floor($item->price), locale: $paymentSettings->currency_locale))
                                        : config('app.currency_symbol').' '. \Number::format(floor($item->price),
                                        locale: $paymentSettings->currency_locale) }}</span>
                                    {{-- @if($item->ad?->isEnabledOffer())
                                    <span class=" text-xs sm:text-sm font-semibold">{{ ($order->order_type ==
                                        RESERVATION_TYPE_POINT_VAULT) ?
                                        currencyToPointConversion(config('app.currency_symbol').' '.
                                        \Number::format(floor($item->discount_price), locale:
                                        $paymentSettings->currency_locale)) : config('app.currency_symbol').' '.
                                        \Number::format(floor($item->discount_price), locale:
                                        $paymentSettings->currency_locale) }}</span>
                                    @endif --}}
                                </div>
                            </div>
                        </a>
                    </div>


                    @endforeach

                    <div class=" dark:border-white/10 classic:border-black pt-1 pb-5">
                        <x-filament::section collapsible
                            class="w-full !ring-0 !shadow-none !border-none !rounded-none !py-0">
                            <x-slot name="heading" class=" !text-2xl">
                                <h3 class=" text-lg font-semibold">{{__('messages.t_order_track')}}</h3>
                            </x-slot>

                            {{-- Content --}}
                            <div class=" time-line text-sm sm:text-base grid grid-cols-1 gap-y-3">
                                @foreach ($histories as $history)
                                <div class="order-dot before:dark:bg-white" wire:key="history-{{$history->id}}">
                                    <div class=" -translate-y-2">
                                        <h4 class="@if (!$history->action_date)
                                            text-[#71717A]/70
                                        @endif">{{ \Str::title(str_replace('_', ' ' , $history->action)) }}</h4>
                                        <h4 class=" @if (!$history->action_date)
                                            text-[#71717A]/70
                                            @else
                                            text-sm text-[#71717A]
                                        @endif">{{ $history->action_date ?
                                            \Carbon\Carbon::parse($history->action_date)->format('D, M d, Y h:m a') :
                                            'update soon'}}</h4>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </x-filament::section>
                    </div>
                </div>

                {{-- summary section --}}
                <div class=" md:col-span-2 md:row-span-1">
                    <div
                        class=" border border-gray-950/5 dark:border-white/10 classic:border-black rounded-lg bg-white dark:bg-gray-900 p-5">
                        <h3 class=" sm:text-lg font-semibold">{{__('messages.t_order_summary')}}</h3>
                        <div class=" text-sm sm:text-base grid grid-cols-1 gap-y-5 pt-5">
                            <div class=" flex justify-between items-center">
                                <h4>{{__('messages.t_cart_quantity',['cartquantity'=>$quantity])}}</h4>
                                <h4 class=" font-medium">{{ ($order->order_type == RESERVATION_TYPE_POINT_VAULT) ?
                                    formatPriceWithCurrency($order->subtotal_amount) : formatPriceWithCurrency($order->subtotal_amount) }}</h4>
                            </div>
                            @if ($order->order_type != RESERVATION_TYPE_POINT_VAULT)
                            {{-- <div class=" flex justify-between items-center">
                                <h4>Discount</h4>
                                <h4 class=" dark:text-white font-medium">{{ config('app.currency_symbol').' '.
                                    \Number::format(floor($order->discount_amount), locale:
                                    $paymentSettings->currency_locale) }}</h4>
                            </div> --}}
                            @endif
                            @if ($order->order_type != RESERVATION_TYPE_POINT_VAULT)
                            <div class=" flex justify-between items-center">
                                <h4>{{__('messages.t_tax')}}</h4>
                                <h4 class=" dark:text-white font-medium">{{formatPriceWithCurrency($order->tax_amount)}}</h4>
                            </div>
                            @endif
                            @if ($order->order_type != RESERVATION_TYPE_POINT_VAULT)
                            <div class=" flex justify-between items-center">
                                <h4>{{__('messages.t_delivery_charges')}}</h4>
                                <h4 class=" dark:text-white text-[#307A16] font-medium">{{__('messages.t_free_charge')}}</h4>
                            </div>
                            @endif
                            <div class=" border-t border-gray-950/5 dark:border-white/10 classic:border-black"></div>
                            <div class=" leading-none text-base sm:text-lg font-bold flex justify-between items-center">
                                <h2>{{__('messages.t_total')}} {{ ($order->order_type != RESERVATION_TYPE_POINT_VAULT) ? __('messages.t_point_purchase_kp_amount') :
                                    __('messages.t_point_purchase_point_purchases')}}</h2>
                                <h2>{{ ($order->order_type == RESERVATION_TYPE_POINT_VAULT) ?
                                   formatPriceWithCurrency($order->total_amount)  :   formatPriceWithCurrency($order->total_amount)  }}</h2>
                            </div>

                            @if($order->order_type != RESERVATION_TYPE_POINT_VAULT && $order->total_amount != $order->converted_amount)
                            <div class=" leading-none text-base sm:text-lg font-bold flex justify-between items-center">
                                <h2>{{__('messages.t_my_purchase_converted_amount')}}</h2>
                                <h2>{{formatPriceWithCurrency($order->converted_amount)  }}</h2>
                            </div>
                            @endif


                        </div>
                    </div>
                </div>
                <div
                    class=" md:col-span-2 md:row-span-2 border border-gray-950/5 dark:border-white/10 classic:border-black rounded-lg bg-white dark:bg-gray-900 p-5 h-fit">
                    <h3 class=" sm:text-lg font-semibold">{{__('messages.t_payment_info')}}</h3>
                    <div class=" text-sm sm:text-base grid grid-cols-1 gap-y-5 pt-5">
                        @if ($order->order_type != RESERVATION_TYPE_POINT_VAULT)
                        <div class=" flex justify-between items-center">
                            <h4>{{__('messages.t_payment_method')}}</h4>
                            <h4>{{ \Str::title($order->payment_method) }}</h4>
                        </div>
                        @endif
                        <div class=" flex justify-between items-center">
                            <h4>{{__('messages.t_date_of_process')}}</h4>
                            <h4>{{ \Str::title(\Carbon\Carbon::parse($order->created_at)->format('d M Y h:m a') ) }}
                            </h4>
                        </div>
                        <div class=" border-t border-gray-950/5 dark:border-white/10 classic:border-black"></div>
                        <div class=" flex gap-x-5 justify-between">
                            <h4 class=" whitespace-nowrap">{{__('messages.t_shipping_info')}}</h4>
                            <h4 class=" text-end ">{{ $order->contact_name }}<span
                                    class=" block">{{$order->shipping_address}}</span></h4>
                        </div>
                        <div class=" flex justify-between items-center">
                            <h4>{{__('messages.t_order_contact_no')}}</h4>
                            <h4>{{ $order->contact_phone_number }}</h4>
                        </div>
                    </div>
                </div>
            </section>

            {{-- cancellation section --}}
            <!-- <section class=" pb-10">
                <h3 class=" text-lg sm:text-xl font-semibold">Cancellation & Return Policy</h3>
                <p class=" text-sm sm:text-base pt-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non sodales nisi. Duis at nisi id magna gravida dapibus. Vivamus at eleifend mi. Nam eu libero sed augue feugiat imperdiet vel quis quam. Maecenas feugiat felis imperdiet suscipit Show More</p>
            </section> -->
        </div>

    </div>

    <script>
        const element = document.querySelector('.time-line');
        const height = element.offsetHeight;
        let orderCount = '{{ $histories->count() - 1 }}';
        let orderDot = document.querySelectorAll('.time-line .order-dot');

        for (i = 0; i < orderDot.length; i++) {
            if (i <= orderCount) {
                orderDot[i].style.color = 'black';
            }
        }

        // Select the first and second .order-dot elements
        const firstOrderDot = document.querySelector('.time-line .order-dot:nth-child(1)');

        // Get their heights using the offsetHeight property (includes padding and border)
        const firstOrderDotHeight = firstOrderDot.offsetHeight;

        // Function to change the height dynamically
        function changeLineHeight(newHeight) {
            const timeLineElement = document.querySelector('.time-line');
            timeLineElement.style.setProperty('--line-height', newHeight + 'px');
        }

        // Example: Change the height to 80%
        changeLineHeight(orderCount * (firstOrderDotHeight + 12));
    </script>
</div>
