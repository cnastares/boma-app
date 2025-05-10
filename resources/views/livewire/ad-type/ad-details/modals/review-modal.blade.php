@if (in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
<x-filament::modal id="view-review" width="5xl" :close-button="false">
    <div
        class="bg-white rounded-ss-xl rounded-se-xl md:rounded-xl w-full h-full md:min-h-[22rem] md:max-h-[33rem] dark:bg-gray-800 ">
        <div class=" flex justify-between items-center sticky top-0 bg-white dark:bg-gray-900">
            <div class=" flex items-center gap-x-2">
                <svg class="w-6 h-6" width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M9.47998 1.49897C9.52227 1.3958 9.5943 1.30755 9.6869 1.24543C9.7795 1.18331 9.88848 1.15015 9.99998 1.15015C10.1115 1.15015 10.2205 1.18331 10.3131 1.24543C10.4057 1.30755 10.4777 1.3958 10.52 1.49897L12.645 6.60997C12.6848 6.70561 12.7501 6.78841 12.834 6.84928C12.9178 6.91015 13.0167 6.94672 13.12 6.95497L18.638 7.39697C19.137 7.43697 19.339 8.05997 18.959 8.38497L14.755 11.987C14.6764 12.0542 14.6179 12.1417 14.5858 12.2399C14.5537 12.3382 14.5493 12.4434 14.573 12.544L15.858 17.929C15.8838 18.037 15.877 18.1503 15.8385 18.2545C15.8 18.3587 15.7315 18.4491 15.6416 18.5144C15.5517 18.5797 15.4445 18.6168 15.3335 18.6212C15.2225 18.6256 15.1127 18.597 15.018 18.539L10.293 15.654C10.2048 15.6001 10.1034 15.5715 9.99998 15.5715C9.89659 15.5715 9.79521 15.6001 9.70698 15.654L4.98198 18.54C4.88724 18.598 4.77743 18.6266 4.66644 18.6222C4.55544 18.6178 4.44823 18.5807 4.35835 18.5154C4.26847 18.4501 4.19994 18.3597 4.16143 18.2555C4.12292 18.1513 4.11615 18.038 4.14198 17.93L5.42698 12.544C5.45081 12.4434 5.44643 12.3381 5.41432 12.2399C5.38221 12.1416 5.32362 12.0541 5.24498 11.987L1.04098 8.38497C0.956324 8.3128 0.894988 8.21714 0.864741 8.11009C0.834494 8.00304 0.836696 7.88942 0.87107 7.78362C0.905443 7.67782 0.970441 7.58461 1.05783 7.51578C1.14522 7.44695 1.25107 7.4056 1.36198 7.39697L6.87998 6.95497C6.98323 6.94672 7.0822 6.91015 7.16601 6.84928C7.24981 6.78841 7.3152 6.70561 7.35498 6.60997L9.47998 1.49897Z"
                        fill="#FDAE4B" stroke="black" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class=" text-xl font-semibold flex items-center gap-x-1 pt-1">
                    <div class="">{{ number_format($ad->customerReviews()->avg('rating'), 1) }}</div>
                    <div class=" w-1 h-1 bg-black dark:bg-gray-900 rounded-full"></div>
                    <div @click="$dispatch('open-modal', {id: 'view-review'});"
                        class="cursor-pointer font-semibold underline underline-offset-1 whitespace-nowrap">
                        {{ $ad->customerReviews()->count() }} Reviews
                    </div>
                </div>
            </div>
            <svg @click="$dispatch('close-modal', {id: 'view-review'});" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class=" w-7 h-7 cursor-pointer text-gray-400 hover:text-gray-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </div>

        <div class=" grid grid-cols-1 md:grid-cols-2 py-5 dark:bg-gray-900">
            <div class=" md:sticky md:top-[92px] h-fit border-b border-[#B0B0B0] md:border-none pb-5">
                <div class=" flex gap-x-4">
                    <div>
                        @php
                        $imageProperties = $ad->image_properties;
                        $altText = $imageProperties['1'] ?? $ad->title;
                        @endphp
                        {{-- <Saspect-square object-cover h-32 flex w-full md:h-[7rem] rounded-xl"> --}}
                    </div>
                    <div class=" flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $ad->title }}</h3>
                            <span class=" text-[#71717A]">{{ $ad?->category?->name }}</span>
                            <div class=" text-sm text-[#71717A]">Seller: {{ $ad->user->name }}</div>
                        </div>
                        <div class=" flex items-center gap-x-2">
                            @if ($ad->isEnabledOffer() && $ad->offer_price)
                            <span
                                class=" text-sm font-semibold">{{ $ad->offer_price ? formatPriceWithCurrency($ad->offer_price) : null }}</span>

                            <span
                                class=" text-sm text-[#71717A] line-through">{{ formatPriceWithCurrency($ad->price) }}</span>
                            @if ($ad->getOfferPercentage())
                            <span
                                class=" text-sm text-[#FDAE4B] font-semibold">{{ $ad->getOfferPercentage() }}
                                % OFF</span>
                            @endif
                            @else
                            <span
                                class=" text-sm font-semibold">{{ formatPriceWithCurrency($ad->price) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class=" grid grid-cols-1 gap-y-5 pt-5">
                    <div class="text-lg font-semibold flex items-center">
                        <a href="#" class="flex items-center gap-x-2 cursor-pointer outline-none group">
                            <div
                                class="bg-gray-200 dark:bg-black dark:text-gray-100 text-black border rounded-full h-8 w-8 flex items-center justify-center">
                                <span>{{ substr($ad->user->name, 0, 1) }}</span>
                            </div>
                            <span class=" group-hover:underline">{{ $ad->user->name }}</span>
                        </a>
                    </div>

                    <div class="flex items-center gap-x-2 ml-2">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                            data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z">
                            </path>
                        </svg> <span class="text-sm md:text-base">{{ __('messages.t_member_since') }}
                            {{ \Carbon\Carbon::parse($ad->user->created_at)->translatedFormat('F Y') }}</span>
                    </div>

                    @if ($ad->user->email_verified_at)
                    <div class="flex items-center gap-x-2 ml-2">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75">
                            </path>
                        </svg>
                        <span class="text-sm md:text-base">{{ __('messages.t_email_verified') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- review section  --}}
            <div
                class="md:min-h-[14rem] md:max-h-[25rem] overflow-y-auto md:pl-5 grid grid-cols-1 gap-y-5 md:border-l md:border-[#B0B0B0] pt-5 md:pt-0">
                @foreach ($ad->customerReviews()->get() as $review)
                <div>
                    <div class=" flex items-center gap-x-1.5">
                        <div class="">
                            @if ($review->user->profile_image)
                            <img src="{{ $review->user->profile_image }}"
                                alt="{{ $review->user->name }}"
                                class="rounded-full w-10 h-10 border border-black">
                            @else
                            <div
                                class="bg-gray-200 dark:bg-black dark:text-gray-100 text-black border rounded-full w-10 h-10 flex items-center justify-center">
                                <span>{{ substr($review->user->name, 0, 1) }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <span class=" text-lg font-medium">{{ $review->user->name }}</span>
                            <div class=" flex items-center gap-x-1 pt-1">
                                @for ($i = 0; $i < $review->rating; $i++)
                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M9.47998 1.49897C9.52227 1.3958 9.5943 1.30755 9.6869 1.24543C9.7795 1.18331 9.88848 1.15015 9.99998 1.15015C10.1115 1.15015 10.2205 1.18331 10.3131 1.24543C10.4057 1.30755 10.4777 1.3958 10.52 1.49897L12.645 6.60997C12.6848 6.70561 12.7501 6.78841 12.834 6.84928C12.9178 6.91015 13.0167 6.94672 13.12 6.95497L18.638 7.39697C19.137 7.43697 19.339 8.05997 18.959 8.38497L14.755 11.987C14.6764 12.0542 14.6179 12.1417 14.5858 12.2399C14.5537 12.3382 14.5493 12.4434 14.573 12.544L15.858 17.929C15.8838 18.037 15.877 18.1503 15.8385 18.2545C15.8 18.3587 15.7315 18.4491 15.6416 18.5144C15.5517 18.5797 15.4445 18.6168 15.3335 18.6212C15.2225 18.6256 15.1127 18.597 15.018 18.539L10.293 15.654C10.2048 15.6001 10.1034 15.5715 9.99998 15.5715C9.89659 15.5715 9.79521 15.6001 9.70698 15.654L4.98198 18.54C4.88724 18.598 4.77743 18.6266 4.66644 18.6222C4.55544 18.6178 4.44823 18.5807 4.35835 18.5154C4.26847 18.4501 4.19994 18.3597 4.16143 18.2555C4.12292 18.1513 4.11615 18.038 4.14198 17.93L5.42698 12.544C5.45081 12.4434 5.44643 12.3381 5.41432 12.2399C5.38221 12.1416 5.32362 12.0541 5.24498 11.987L1.04098 8.38497C0.956324 8.3128 0.894988 8.21714 0.864741 8.11009C0.834494 8.00304 0.836696 7.88942 0.87107 7.78362C0.905443 7.67782 0.970441 7.58461 1.05783 7.51578C1.14522 7.44695 1.25107 7.4056 1.36198 7.39697L6.87998 6.95497C6.98323 6.94672 7.0822 6.91015 7.16601 6.84928C7.24981 6.78841 7.3152 6.70561 7.35498 6.60997L9.47998 1.49897Z"
                                            fill="#FDAE4B" stroke="black" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    @endfor
                                    @for ($i = 0; $i < 5 - $review->rating; $i++)
                                        <svg width="20" height="20" viewBox="0 0 20 20"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.47998 1.49897C9.52227 1.3958 9.5943 1.30755 9.6869 1.24543C9.7795 1.18331 9.88848 1.15015 9.99998 1.15015C10.1115 1.15015 10.2205 1.18331 10.3131 1.24543C10.4057 1.30755 10.4777 1.3958 10.52 1.49897L12.645 6.60997C12.6848 6.70561 12.7501 6.78841 12.834 6.84928C12.9178 6.91015 13.0167 6.94672 13.12 6.95497L18.638 7.39697C19.137 7.43697 19.339 8.05997 18.959 8.38497L14.755 11.987C14.6764 12.0542 14.6179 12.1417 14.5858 12.2399C14.5537 12.3382 14.5493 12.4434 14.573 12.544L15.858 17.929C15.8838 18.037 15.877 18.1503 15.8385 18.2545C15.8 18.3587 15.7315 18.4491 15.6416 18.5144C15.5517 18.5797 15.4445 18.6168 15.3335 18.6212C15.2225 18.6256 15.1127 18.597 15.018 18.539L10.293 15.654C10.2048 15.6001 10.1034 15.5715 9.99998 15.5715C9.89659 15.5715 9.79521 15.6001 9.70698 15.654L4.98198 18.54C4.88724 18.598 4.77743 18.6266 4.66644 18.6222C4.55544 18.6178 4.44823 18.5807 4.35835 18.5154C4.26847 18.4501 4.19994 18.3597 4.16143 18.2555C4.12292 18.1513 4.11615 18.038 4.14198 17.93L5.42698 12.544C5.45081 12.4434 5.44643 12.3381 5.41432 12.2399C5.38221 12.1416 5.32362 12.0541 5.24498 11.987L1.04098 8.38497C0.956324 8.3128 0.894988 8.21714 0.864741 8.11009C0.834494 8.00304 0.836696 7.88942 0.87107 7.78362C0.905443 7.67782 0.970441 7.58461 1.05783 7.51578C1.14522 7.44695 1.25107 7.4056 1.36198 7.39697L6.87998 6.95497C6.98323 6.94672 7.0822 6.91015 7.16601 6.84928C7.24981 6.78841 7.3152 6.70561 7.35498 6.60997L9.47998 1.49897Z"
                                                fill="#DEDEDE" stroke="black" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        @endfor
                            </div>
                        </div>
                    </div>
                    {{-- <p class="pt-3">{{ $review->feedback }}</p> --}}
                </div>
                @endforeach
                @if ($ad->customerReviews()->count() == 0)
                <div class="flex flex-col items-center justify-center p-10 w-full">
                    <x-not-found description="{{ __('messages.t_review_is_empty') }}" />
                </div>
                @endif
            </div>
        </div>
    </div>
</x-filament::modal>
@endif
