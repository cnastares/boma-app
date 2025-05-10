<div x-data="{
    isCopied: false,
    copy() {
        const url = '{{ url()->current() }}';
        var _this = this;
        navigator.clipboard.writeText(url).then(function() {
            _this.isCopied = true;
            setTimeout(() => {
                _this.isCopied = false;
            }, 2000);
        }, function(err) {
            console.error('Failed to copy text: ', err);
        });
    }
}">

    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
        'main-content'=> __('messages.t_skip_to_main_content'),
        'footer'=> __('messages.t_skip_to_footer')
    ]])

    <!-- Page Header -->
    <x-page-header title="{{ $this->ad->title }}" isMobileHidden />

    <!-- Header -->
    <livewire:layout.header isMobileHidden lazy />

    <!-- Ad Placement header -->
    @include('livewire.ad-type.ad-details._parties.ad-placement-header')

    <!-- Main Content -->
    <main id="main-content" class="sticky-scroll-margin">
    <!-- Ad Title Section -->
    @include('livewire.ad-type.ad-details._parties.ad-title-section')

    <!-- Breath crumbs -->
    @include('livewire.ad-type.ad-details._parties.breadcrumbs')

    <!-- review Model -->
    @include('livewire.ad-type.ad-details.modals.review-modal')

    <div class="md:pb-10 md:pt-6 ">
        <div class="container mx-auto md:px-4">
            <!-- Owner View -->
            @include('livewire.ad-type.ad-details._parties.owner-view')

            @if (Auth::id() == $this->ad->user_id && $this->ad->status->value == 'active')
            <div class="py-2 px-4 md:px-0">
                <livewire:ad.sell-faster id="{{ $this->ad->id }}" isHorizontal="{{ true }}" />
            </div>
            @endif

            <div class="grid grid-cols-7 bg-white md:ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:rounded-xl classic:ring-black ">
                <div class="md:col-span-5 col-span-7  rtl:border rtl:border-r-0 rtl:border-l rtl:border-t-0 rtl:border-b-0 md:border-r border-gray-200 dark:border-white/20 classic:border-black relative">

                    <!-- Check image is slider -->
                    @include('livewire.ad-type.ad-details._parties.ad-image')

                    <!-- Vehicle rental related -->
                    @include('livewire.ad-type.ad-details._parties.vehicle-rental')

                    <!-- Ad description -->
                    <x-ad.description :description="$descriptionHtml" />

                    <div class="space-y-4 px-4 pb-4 md:mb-8">
                        <!-- Condition -->
                        @if ($ad->condition && $ad->adType?->disable_condition == false)
                        <div class=" space-x-2">
                            <span class="font-medium text-lg w-1/3">{{ __('messages.t_condition') }}: </span>
                            <span class="text-base w-2/3">{{ $ad->condition->name }}</span>
                        </div>
                        @endif

                        <!-- Render Dynamic Fields -->
                        @include('livewire.ad-type.ad-details._parties.dynamic-field')
                    </div>

                    @if (optional(current($customizationSettings->ad_detail_page))['enable_mobile_view_ad_action_in_below_ad'])
                    <div class="md:hidden px-6 pt-6  pb-6 border border-gray-200 dark:border-white/20 classic:border-black border-l-0 border-r-0 rounded-none">
                        <x-ad.share-report :$isFavourited :$ad />
                    </div>
                    @endif
                </div>

                <div class="md:col-span-2 col-span-7 ">
                    <!-- Point System Based -->
                    @if (auth()->id() != $ad->user_id && isEnablePointSystem() && in_array($ad->adType?->marketplace, [POINT_SYSTEM_MARKETPLACE]) && $ad->price_type_id == 1)
                    <div class="border border-gray-200 dark:border-white/20  classic:border-black border-l-0 border-r-0 border-t-0 rounded-none p-4"
                        x-data="{ policy: false, }">
                        <div class=" pt-5">
                            @if (auth()->check() && $ad->price > max(auth()->user()->wallet?->points, 0))
                            <x-button.secondary onclick="window.location='/buy-point'" size="lg" class=" font-semibold w-full mb-4 dark:!bg-secondary-600">{{ __('messages.t_buy_point') }}</x-button.secondary>
                            <p class="text-red-700 font-semibold">{{ __('messages.t_you_do_not_have_enough') }}</p>
                            @else
                            <x-button.secondary wire:click="buyNow()" size="lg" class="w-full mb-4">{{ __('messages.t_buy_now') }}</x-button.secondary>
                            @endif
                        </div>
                    </div>
                    @else <!-- E-commerce related -->
                    @include('livewire.ad-type.ad-details._parties.online-shop')
                    @endif


                    @if (is_vehicle_rental_active() && $ad->start_date || ($vehicleRentalSettings->enable_whatsapp && $ad->user->phone_number))
                    <div
                        class="border border-gray-200 dark:border-white/20 classic:border-black lg:border-t-0 border-l-0 border-r-0 rounded-none p-4">
                        @if ($ad->start_date)
                        <x-ad.vehicle-booking :$ad />
                        @endif
                        <!-- Chat with Owner Button -->
                    </div>
                    @endif

                    <!-- Contact info without online shop -->
                    @if (!in_array($ad->adType?->marketplace, [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
                    <div
                        class="hidden md:block border dark:border-white/20 classic:border-black border-l-0 border-r-0 border-t-0">
                        <x-ad.contact :$ad />
                    </div>
                    @endif

                    <!-- Seller info -->
                    <div>
                        <livewire:user.seller-info :$isWebsite :$ad extraClass="border-l-0 border-r-0 md:border-t-0 rounded-none" />
                    </div>

                    <!-- External Ad Settings -->
                    @include('livewire.ad-type.ad-details._parties.external-ad-settings')

                    <!-- Share Report -->
                    <div class="py-6 px-4 hidden md:block">
                        <h3 class="text-lg mb-4 font-semibold">{{ __('messages.t_ad_actions') }}</h3>
                        <x-ad.share-report :$isFavourited :$ad />
                    </div>

                    <!-- Tags section -->
                    @if ($tags && count($tags) > 0 && $ad->adType?->enable_tags == true)
                    <div class="py-6 px-4 md:border-t dark:border-white/20 classic:border-black">
                        <h3 class="text-lg mb-4 font-semibold">{{ __('messages.t_tags') }}</h3>
                        <div>
                            @foreach ($tags as $tag)
                            <a wire:key="tag-{{ $tag['name'] }}" href="{{ $tag['link'] }}"
                                class="inline-block bg-gray-200 hover:bg-gray-300 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2 capitalize">{{ $tag['name'] }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Ads -->
        @include('livewire.ad-type.ad-details._parties.related-ads')
    </div>

    <!-- Contact info -->
    <div class="md:hidden fixed bottom-[66px] left-0 right-0 border-t border-gray-200 bg-white dark:bg-gray-900 dark:border-white/10 classic:border-black z-10 @if (is_ecommerce_active() || isEnablePointSystem()) px-4 @endif">
        <x-ad.contact :$ad />
    </div>
    </main>
    <!-- Ad Placement footer -->
    @include('livewire.ad-type.ad-details._parties.ad-placement-footer')

    <!-- Sidebar -->
    <livewire:layout.sidebar />

    <!-- Modals (Share ad) -->
    @include('livewire.ad-type.ad-details.modals.share-ad-modal')

    <!-- Modals (Report ad) -->
    @include('livewire.ad-type.ad-details.modals.report-ad')

    <!-- Bottom navigation -->
    <div class="z-30">
        <livewire:layout.bottom-navigation />
    </div>

    <!-- Footer Layout -->
    <livewire:layout.footer />

    <livewire:ad.verify-age :$categorySlug :$subCategorySlug :$childCategorySlug :canRepeat="false" />

    <!-- Custom Script -->
    @include('livewire.ad-type.ad-details._parties.script')
</div>
