<div class="chat-screen flex flex-col md:flex-none">
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
            /* WebKit */
        }
        .no-resize {
            resize: none;
        }
    </style>
    @php
        $imageProperties = $conversation?->ad?->image_properties;
        $altText = $imageProperties['1'] ?? $conversation?->ad?->title;
    @endphp
    @if ($conversation)
        <div class="flex items-center border-b border-gray-200 px-4 py-2.5 classic:border-black dark:border-white/10">
            <img src="{{ $conversation?->ad?->primaryImage ?? asset('/images/placeholder.jpg') }}"
                alt="{{ $altText }}" class="w-12 h-12 rounded-xl">
            <div class="ml-3">
                <h2 class="text-lg">{{ $conversation?->ad?->title }}</h2>
                @if ( $conversation->ad?->slug)
                <a id="ad-link" class="underline"
                href="{{route('ad.overview', [
                    'slug' => $conversation->ad?->slug
                    ])}}"
                @endif
                 target="__blank">{{__('messages.t_view_ad')}}</a>
                @unless ($conversation->ad?->category?->disable_price_type == true)
                    <div class="flex">
                        <x-price
                            value="{{ formatPriceWithCurrency($conversation?->ad?->price)}}"
                            type_id="{{ $conversation?->ad?->price_type_id }}"
                            label="{{ $conversation?->ad?->priceType->label }}"
                            has_prefix="{{ $conversation?->ad?->adType?->has_price_suffix }}"
                            price_suffix="{{ $conversation?->ad?->price_suffix }}"
                            offer_enabled="{{ $conversation?->ad?->isEnabledOffer() }}"
                            offer_price="{{ $conversation?->ad?->offer_price ? formatPriceWithCurrency($conversation?->ad?->offer_price) : null }}"
                            offer_percentage="{{ $conversation?->ad?->getOfferPercentage() }}"
                            ad_type="{{$conversation?->ad?->adType?->marketplace}}" />
                    </div>
        @endif
    </div>
    </div>
    @endif
    <div
        class="bg-gray-50 rounded-xl-lg md:h-[25rem] min-h-[25rem] relative flex flex-col flex-grow classic:bg-gray-100 dark:bg-gray-800">
        <div wire:poll.10s x-ref="messagesContainer" class="flex-grow overflow-y-auto hide-scrollbar p-4 " x-data="{ init() { this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight; } }"
            x-init="init">
            @foreach ($messages as $message)
                @if ($message->sender_id === Auth::id())
                    <div class="mb-2 text-right">
                        <p class="bg-black text-white p-2 rounded inline-block prose prose-slate ">{!! nl2br($message->content) !!}
                        </p>
                        <p class="text-xs text-gray-600 mt-1">{{ $message->created_at->format('h:i a') }}</p>
                    </div>
                    <!-- <div class=" bg-white rounded-t-2xl rounded-ee-2xl px-3 py-4 mx-5 w-fit">
                        <div class=" bg-[#DEDEDE] flex items-center gap-x-5 rounded-lg px-2 py-1">
                            <h3>Seller Offer $140</h3>
                            <h3 class=" font-semibold">Accepted</h3>
                        </div>
                        <p class=" pt-2 px-2">Seller accept your offer, Let’s move towards the deal</p>
                    </div> -->
                @else
                    <div class="mb-2">
                        <p class=" bg-primary-400 p-2 rounded   inline-block prose prose-slate ">{!! nl2br($message->content) !!}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $message->created_at->format('h:i a') }}</p>

                    </div>
                @endif
            @endforeach
        </div>


        @if ($conversation?->ad != null)
        <div class=" bg-white dark:bg-gray-800 rounded-ss-xl rounded-se-xl rounded-ee-xl" x-data="{
            chatNav: 1,
            arrow: true,
        }">
            @if ((!is_vehicle_rental_active()) && $messageSettings->enable_make_offer)
                <div class=" border border-gray-200 classic:border-black dark:border-white/10 rounded-ss-xl rounded-se-xl relative">
                    <div @click="arrow = !arrow" class=" absolute -top-4 w-full">
                        <div
                            class=" border border-b-0 rounded-t-full border-gray-200 classic:border-black dark:border-white/10 bg-white dark:bg-gray-800 flex mx-auto justify-center items-center w-8 h-4 pt-2 cursor-pointer">
                            <svg :class="arrow == true ? ' w-3.5 h-3.5 stroke-gray-200 classic:stroke-black dark:stroke-[#FFFFFF1A]' :
                                ' w-3.5 h-3.5 rotate-180 stroke-gray-200 classic:stroke-black dark:stroke-[#FFFFFF1A]'"
                                xmlns="http://www.w3.org/2000/svg" width="16" height="10" viewBox="0 0 16 10"
                                fill="none">
                                <path d="M14.875 1.5625L8 8.4375L1.125 1.5625" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                    <div class=" grid grid-cols-2">
                        <button @click="chatNav = 1"
                            class=" flex justify-center items-center gap-x-2 relative py-3 cursor-pointer">
                            <svg :class="chatNav == '1' ? ' stroke-primary-600' : ' stroke-[#495057]'"
                                xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                                fill="none">
                                <path
                                    d="M16.875 7.5925C17.6117 7.82917 18.125 8.5325 18.125 9.34V12.9117C18.125 13.8583 17.4192 14.6617 16.475 14.7392C16.1917 14.7617 15.9083 14.7825 15.625 14.7992V17.375L13.125 14.875C11.9967 14.875 10.88 14.8292 9.775 14.7392C9.53444 14.7197 9.30044 14.6511 9.0875 14.5375M16.875 7.5925C16.7462 7.55106 16.6131 7.52449 16.4783 7.51334C14.2466 7.32807 12.0034 7.32807 9.77167 7.51334C8.82917 7.59167 8.125 8.39417 8.125 9.34V12.9117C8.125 13.6092 8.50833 14.2283 9.0875 14.5375M16.875 7.5925V6.03083C16.875 4.68 15.915 3.50917 14.575 3.335C12.8507 3.11149 11.1137 2.99959 9.375 3C7.6125 3 5.87667 3.11417 4.175 3.335C2.835 3.50917 1.875 4.68 1.875 6.03083V11.2192C1.875 12.57 2.835 13.7408 4.175 13.915C4.65583 13.9775 5.13917 14.0317 5.625 14.0767V18L9.0875 14.5375"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <h3 :class="chatNav == '1' ? ' text-primary-600 font-medium' : ' text-[#495057] font-medium'">{{__('messages.t_message_chat')}}
                            </h3>
                            <div
                                :class="chatNav == '1' ? ' block border-b-2 border-primary-600 absolute bottom-0 w-full' :
                                    ' hidden'">
                            </div>
                        </button>
                        <button @click="chatNav = 2"
                            class=" flex justify-center items-center gap-x-2 relative py-3 cursor-pointer">
                            <svg :class="chatNav == '2' ? ' stroke-primary-600' : ' stroke-[#495057]'"
                                xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                                fill="none">
                                <path
                                    d="M14.2863 4.0715V11.2144M14.2863 4.0715L12.1434 1.21436M14.2863 4.0715L16.4291 1.21436M0.714844 16.2144L4.20627 19.1229C4.71971 19.5504 5.36673 19.7845 6.03484 19.7844H15.2377C15.8948 19.7844 16.4291 19.2515 16.4291 18.5944C16.4291 17.2801 15.3634 16.2129 14.0477 16.2129H7.64913M19.2863 4.0715H9.28627V11.2144H19.2863V4.0715Z"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M5.00056 14.7858L6.07199 15.8572C6.35615 16.1414 6.74155 16.301 7.14342 16.301C7.54528 16.301 7.93068 16.1414 8.21484 15.8572C8.499 15.5731 8.65864 15.1876 8.65864 14.7858C8.65864 14.3839 8.499 13.9985 8.21484 13.7144L6.55199 12.0501C6.28628 11.7847 5.97086 11.5742 5.62375 11.4308C5.27665 11.2874 4.90469 11.2139 4.52913 11.2144H0.714844"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <h3 :class="chatNav == '2' ? ' text-primary-600 font-medium' : ' text-[#495057] font-medium'">{{__('messages.t_message_make_offer')}}</h3>
                            <div
                                :class="chatNav == '2' ? ' block border-b-2 border-primary-600 absolute bottom-0 w-full' :
                                    ' hidden'">
                            </div>
                        </button>
                    </div>
                </div>
                <div x-show="arrow" x-collapse>
                    <div :class="chatNav == '1' ? ' flex items-center gap-x-3 px-5 pb-5 overflow-x-auto w-full hide-scrollbar' :
                        ' hidden'"
                        x-data="{
                            chatBg: null,
                        }" class="@if (!is_vehicle_rental_active()) pt-5 @endif">
                        <button type="button" wire:click="customMessage(`{{ __('messages.t_is_item_available_query') }}`);"
                            :class="chatBg == '1' ? ' text-white bg-[#FDAE4B] dark:text-white dark:bg-gray-800' :
                                ' text-[#71717A] bg-white dark:text-white dark:bg-gray-800'"
                            class="text-sm border border-[#71717A] rounded-md px-4 py-1 min-w-fit">{{ __('messages.t_is_item_available_query') }}</button>
                        <button type="button" wire:click="customMessage(`{{ __('messages.t_meetup_availability_query') }}`);"
                            :class="chatBg == '2' ? ' text-white bg-[#FDAE4B] dark:text-white dark:bg-gray-800' :
                                ' text-[#71717A] bg-white dark:text-white dark:bg-gray-800'"
                            class="text-sm text-[#71717A] border border-[#71717A] rounded-md px-4 py-1 min-w-fit">{{ __('messages.t_meetup_availability_query') }}</button>
                        <button type="button" wire:click="customMessage(`{{ __('messages.t_price_negotiation_query') }}`);"
                            :class="chatBg == '3' ? ' text-white bg-[#FDAE4B] dark:text-white dark:bg-gray-800' :
                                ' text-[#71717A] bg-white dark:text-white dark:bg-gray-800'"
                            class="text-sm text-[#71717A] border border-[#71717A] rounded-md px-4 py-1 min-w-fit">{{ __('messages.t_price_negotiation_query') }}</button>
                    </div>
                    <div :class="chatNav == '2' ? ' flex items-center gap-x-3 px-5 py-5 overflow-x-auto w-full hide-scrollbar' :
                        ' hidden'"
                        x-data="{
                            chatBg: null,
                        }">

                        @if (!$lastOfferMessage)
                            @foreach (getOfferSuggestions($this->conversation->ad->offer_price ?? $this->conversation->ad?->price) as $value)
                                <x-filament::button wire:click="customMessage({{ $value }}, 1);"
                                    class="text-sm border !text-[#000] border-[#71717A] rounded-md px-4 py-1 min-w-fit bg-white dark:!text-white dark:bg-gray-800">
                                    {{ formatPriceWithCurrency($value) }}
                                </x-filament::button>
                            @endforeach
                        @else
                            @if ($lastOfferMessage->sender_id == auth()->id())
                                <div class=" pb-5 w-full">
                                    <h2 class=" text-xl text-center font-semibold">{{__('messages.t_your_offer')}}
                                        ({{ str_replace('Negotiation ', '', $lastOfferMessage->content) }})</h2>
                                    @if ($lastOfferMessage->is_accept_offer)
                                        <div class=" flex justify-center pt-8">
                                            <x-filament::button class=" whitespace-nowrap !bg-black px-8">
                                                {{__('messages.t_ask_contact')}}
                                            </x-filament::button>
                                        </div>
                                    @else
                                        <p class=" text-center pt-2.5">{{__('messages.t_waiting_for_seller_response')}}</p>
                                    @endif
                                </div>
                            @else
                                <div class=" pb-5 w-full">
                                    <h2 class=" text-xl text-center font-semibold">{{__('messages.t_new_offer')}}
                                        ({{ str_replace('Negotiation ', '', $lastOfferMessage->content) }})</h2>
                                    @if ($lastOfferMessage->is_accept_offer)
                                        <div class=" flex justify-center pt-8">
                                            <x-filament::button class=" whitespace-nowrap !bg-black px-8">
                                                {{__('messages.t_ask_contact')}}
                                            </x-filament::button>
                                        </div>
                                    @else
                                        <div class=" flex items-center gap-x-3 pt-8">
                                            <x-filament::button @click="$dispatch('open-modal', { id: 'offer-popup' })"
                                                color="gray" class=" !border !border-black w-full">
                                                {{__('messages.t_message_make_new_offer')}}
                                            </x-filament::button>
                                            <x-filament::button
                                                wire:click="customMessage('I accept your offer, Let’s move towards the deal', 1, 1);"
                                                class="whitespace-nowrap !bg-black px-5 w-full">
                                                {{__('messages.t_lets_go_ahead')}}
                                            </x-filament::button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
            @if ($conversation->ad != null)
            <div :class="chatNav == '2' ? 'hidden' : ' bottom-0 left-0 right-0'"
                class="@if (!is_vehicle_rental_active())  @endif">
                <div x-data="{
                    height: 56,
                    newMessage: @entangle('newMessage'),
                    handleEnter: function(event) {
                        if (event.shiftKey) {
                            this.height += 20;
                        } else if (this.newMessage.trim() !== '') {
                            $wire.sendMessage();
                            newMessage = '';
                            this.height = 56;
                        }
                    }
                }" class="relative md:-m-[0.01rem] shadow-sm send-message">

                    <textarea x-model="newMessage" :style="'height: ' + height + 'px'" x-on:keydown.enter="handleEnter($event)"
                        placeholder="{{ __('messages.t_type_a_message') }}"
                        class="border-none block w-full shadow-inner  dark:text-white text-sm sm:text-base  disabled:bg-slate-100 disabled:cursor-wait ring-1 transition outline-none duration-75 bg-white  dark:bg-gray-900 ring-gray-950/10 focus-within:ring-gray-950/10  dark:ring-white/10 dark:focus-within:ring-primary-500 classic:ring-black md:rounded-br-xl pt-4 no-resize hide-scrollbar">
                    </textarea>

                    <button wire:click="sendMessage" x-on:click="height=56" aria-label="{{__('messages.t_aria_label_send_message')}}"
                        class="absolute top-1.5  right-2 rounded-xl flex items-center p-2 cursor-pointer z-10 "
                        :class="{
                            'text-black bg-primary-600': newMessage.trim() !=
                                '',
                            'text-gray-200 pointer-events-none': newMessage.trim() == ''
                        }"
                        class="">
                        <x-icon-send-email class="w-7 h-7" />
                    </button>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- offer popup  --}}
        <x-filament::modal id="offer-popup" width="3xl">
            <x-slot name="heading" class=" flex items-center">
                {{ __('messages.t_select_reason') }}
            </x-slot>
            <label class="flex items-center gap-x-3">
                <span>{{ __('messages.t_my_last_price') }}</span>
            </label>
            <x-filament::input.wrapper class=" w-full">
                <x-slot name="prefix">
                    $
                </x-slot>
                <x-filament::input wire:model.live="newOffer" type="text" placeholder="{{ __('messages.t_placeholder_offer') }}" />
            </x-filament::input.wrapper>
            <label class=" flex items-center gap-x-3" wire:click="customMessage('{{ __('messages.t_better_offer_message') }}', 1);">
                <x-filament::input.radio name="price" />
                <span>{{ __('messages.t_better_offer_message') }}</span>
            </label>
            <div class=" flex items-center gap-x-3">
                @if ($newOffer)
                    <x-filament::button wire:click="newOfferSend()" class="whitespace-nowrap px-5">
                        {{ __('messages.t_send') }}
                    </x-filament::button>
                @endif

                <x-filament::button @click="$dispatch('close-modal', { id: 'offer-popup' })" color="gray"
                    class=" ">
                    {{ __('messages.t_cancel') }}
                </x-filament::button>
            </div>
        </x-filament::modal>
    </div>
    </div>
