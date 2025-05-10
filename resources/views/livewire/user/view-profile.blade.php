<div>

    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
        'main-content'=> __('messages.t_skip_to_main_content')
    ]])

    <livewire:layout.header isMobileHidden lazy />
    <x-page-header :title="__('messages.t_view_profile')" isMobileHidden :$referrer />

    <!-- Enable if this banner feature has been subscribed by user -->
    @if (getSubscriptionSetting('status') && getUserSubscriptionPlan($this->userId)?->banner_count && count($banners))
        <!-- Banner Image -->
        <div class="w-full  lg:h-[350px] md:h-[250px] sm:h-[225px] h-[130px] pt-[1px] relative block overflow-hidden ">
                @if (count($banners))
                    @include('livewire.user.banner')
                @endif
        </div>
    @endif

    <!-- Main content -->
    <main id="main-content" class="sticky-scroll-margin" x-data="{
        search: '',
        open: false
    }">
        {{-- <div class="mb-6">
            <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div> --}}
        {{-- <div class="md:col-span-1 mb-10 md:mb-0">
            <livewire:user.seller-info :userId="$user->id" />
        </div> --}}

        <!-- Seller Details -->
        <section class="bg-white  dark:bg-gray-900 classic:border-y classic:border-black">
            <div class="md:flex justify-between items-center container mx-auto px-4 py-6 dark:text-gray-100">
                <!-- Profile -->
                <div class="flex items-center gap-2">
                    <div class="bg-gray-200  rounded-lg text-black border  h-24 w-24 flex items-center justify-center">
                        @if ($user->profile_image)
                            <img src="{{ $user->profile_image }}" alt="{{ $user->name }}"
                                class=" rounded-lg w-24 h-24 border border-black">
                        @else
                            <span>{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>

                    <div class="ltr:ml-3 rtl:mr-3">
                        <div class="flex items-center gap-1 text-lg font-semibold">
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $user->name }}
                            </h1>
                            @if ($user->verified)
                                <x-filament::icon-button icon="heroicon-s-check-badge"
                                    tooltip="{{ __('messages.t_user_verified_tooltip') }}" size="lg"
                                    color="success" />
                            @endif
                        </div>
                        @php
                            $city = \App\Models\City::with('state', 'country')->find($user->city);
                        @endphp
                        @if ($city && getSubscriptionSetting('status') && getUserSubscriptionPlan($userId)?->enable_location)
                            <p class="text-gray-800">{{ $city->name . ' - ' . $city->state?->name }}</p>
                        @endif
                        @if ($user->about_me)
                        <p class="group-hover:underline text-sm md:text-base text-wrap ">{{ $user->about_me }}</p>
                        @endif
                        @if ((!getSubscriptionSetting('status')) || (getSubscriptionSetting('status') && getUserSubscriptionPlan($userId)?->rating))
                        <a href="{{ route('feedback', ['id' => $user->id]) }}" class="flex items-end gap-x-2 mt-2">
                            <p class="text-3xl font-bold translate-y-1">{{$user->rating}}</p>
                            <div class="flex items-end gap-x-1 w-fit" x-data
                                x-tooltip="{
                                content: '{{ __('messages.t_tooltip_ratings') }}',
                                theme: $store.theme,
                            }">
                                    <x-star-rating :rating=" $user->rating " :id="$user->id" :name="$user->id" />
                                <span class="text-sm">({{ $user->feedbackCount() }})</span>
                            </div>
                            <p class="text-sm text-black cursor-pointer">{{__('messages.t_view_reviews')}}</p>
                        </a>
                        @endif


                    </div>
                </div>

                <div class="text-sm md:text-base py-2 md:py-0">
                    @if (!is_ecommerce_active())
                        <div class="flex items-center mt-1 gap-x-2">
                            <x-heroicon-o-users class="w-6 h-6" />
                            <div class="flex gap-x-2">
                                <button type="button" class="cursor-pointer" wire:click="showFollowersModal">
                                    <span class="font-semibold">{{ $this->followersCount }}</span>
                                    {{ __('messages.t_followers') }}
                                </button>
                                <div class="text-muted"> | </div>
                                <button type="button" class="cursor-pointer" wire:click="showFollowingModal">
                                    <span class="font-semibold">{{ $this->followingCount }}</span>
                                    {{ __('messages.t_following') }}
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center mt-1 gap-x-2">
                            <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                            <span
                                class="">{{ pluralize($user->ads->count(), __('messages.t_created_ad'), __('messages.t_created_ads'), true) }}</span>
                        </div>
                    @endif

                    <!-- Member Since -->
                    <div class="flex items-center mt-1 gap-x-2">
                        <x-heroicon-o-calendar-days class="w-6 h-6" />
                        <span class="">{{ __('messages.t_member_since') }}
                            {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('F Y') }}</span>
                    </div>

                    @if (getSubscriptionSetting('status') &&
                    getUserSubscriptionPlan($userId)?->enable_business_hours &&
                    $user->business_hours &&
                    count($user->business_hours))
                        <button type="button" class="cursor-pointer flex items-center mt-1 gap-x-2 text-black"
                        @click="$dispatch('open-modal', { id: 'business-hours' })">
                        <x-heroicon-o-clock class="w-6 h-6" />
                        <p>{{ __('messages.t_view_business_hours') }}</p>
                        </button>
                    @endif
                </div>

                <div>
                    @if (!is_ecommerce_active() && $userId != auth()->id())
                        <button type="button" wire:click="toggleFollow" x-data
                            x-tooltip="{
                        content: '{{ $this->isFollowing() ? __('messages.t_tooltip_unfollow') : __('messages.t_tooltip_follow') }}',
                        theme: $store.theme,
                    }"
                    aria-label="{{ $this->isFollowing() ? __('messages.t_tooltip_unfollow') : __('messages.t_tooltip_follow') }}"
                            class="{{ $this->isFollowing() ? 'bg-gray-900' : '' }} w-fit px-3 py-1 rounded-full border classic:border-black cursor-pointer flex items-center">
                            @if ($this->isFollowing())
                                <x-heroicon-s-user class="w-6 h-6 text-white" aria-hidden="true" />
                                <x-heroicon-s-check class="w-5 h-5 -ml-1 text-white" aria-hidden="true" />
                            @else
                                <x-heroicon-o-user class="w-6 h-6" aria-hidden="true" />
                                <x-heroicon-o-plus-small class="w-5 h-5 -ml-1" aria-hidden="true" /> &NonBreakingSpace;
                                {{ __('messages.t_follow') }}
                            @endif
                        </button>
                    @endif
                    @if (getSubscriptionSetting('status') && getUserSubscriptionPlan($userId)?->enable_social_media_links)
                        <!-- Enable if this social media feature has been subscribed by user -->
                        <div class="mt-4 ">
                            <x-social-media :facebook_link="$user->facebook_link" :twitter_link="$user->twitter_link" :linkedin_link="$user->linkedin_link" :instagram_link="$user->instagram_link" />
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <div class="md:grid md:grid-cols-3  gap-x-8 container mx-auto px-4 pt-6 ">
            @if (getSubscriptionSetting('status') &&
                    in_array(getUserSubscriptionPlan($userId)?->filter_options_level, ['basic', 'advanced']))
                <div class="md:col-span-1 mb-3 md:mb-0">
                    <div
                        class=" py-6 px-4 bg-white rounded-xl dark:bg-gray-900 classic:border-black classic:border h-fit">
                        <h2 class="mb-2 font-semibold" id="categories">{{ __('messages.t_categories') }}</h2>
                        <ul class="hidden md:block">
                            <li class="mb-1  pl-6 cursor-pointer "
                                >
                            <button class="{{ !$categorySlug ? 'underline' : '' }}" type="button" x-on:click="$wire.categorySlug=null;$dispatch('update-ad-data')">{{ __('messages.t_all_option') }}</button>
                            </li>
                            @foreach ($categories as $category)
                                <li wire:key='user-filter-{{ $category->slug }}'
                                    class="mb-1  pl-6 cursor-pointer "
                                    >
                                    <button class="{{ $categorySlug == $category->slug ? 'underline' : '' }}" type="button" x-on:click="$wire.categorySlug='{{ $category->slug }}';$dispatch('update-ad-data')">{{ $category->name }}</button>
                                </li>
                            @endforeach
                        </ul>
                        <x-filament::input.wrapper class="md:hidden block">
                            <x-filament::input.select name="category_slug" aria-labelledby="categories">
                                <option class="mb-1  pl-6 cursor-pointer  {{ !$categorySlug ? 'underline' : '' }}"
                                    x-on:click="$wire.categorySlug=null;$dispatch('update-ad-data')">
                                    {{ __('messages.t_all_option') }}</option>
                                @foreach ($categories as $category)
                                    <option wire:key='user-filter-{{ $category->slug }}'
                                        class="mb-1  pl-6 cursor-pointer {{ $categorySlug == $category->slug ? 'underline' : '' }}"
                                        x-on:click="$wire.categorySlug='{{ $category->slug }}';$dispatch('update-ad-data')">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>
                </div>
            @endif
            <div class="col-span-2">
                @if (getSubscriptionSetting('status') && getUserSubscriptionPlan($userId)?->filter_options_level == 'advanced')
                    <!-- Ad Stats Overview -->
                    <div
                        class="min-w-[275px] mb-4 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-2 2xl:grid-cols-3 3xl:grid-cols-6">
                        <x-widgets.stats :title="__('messages.t_published_in_last_90_days')" :value="$this->publishedAdCount" description="yes"
                            icon="heroicon-o-document-duplicate" />
                        <x-widgets.stats :title="__('messages.t_sales_completed')" :value="$this->soldAdCount" description="yes"
                            icon="heroicon-o-document-check" />
                    </div>
                    <div class="flex justify-between items-center gap-2  flex-wrap md:flex-row md:flex-nowrap">
                        <div class="flex relative w-auto">
                            <input wire:keydown.enter="$dispatch('update-ad-data')" wire:model="search" x-model="search" name="search"
                                type="text" x-on:input="open = search.length > 0"
                                class="search-box shadow-sm ring-1 ring-gray-950/10 border-none bg-white h-10 md:h-10 pr-10 pl-4 w-full md:min-w-[200px]  rounded-xl focus:outline-none placeholder-muted focus-within:ring-2 dark:bg-white/5  focus-within:ring-primary-600 dark:ring-white/20 dark:focus-within:ring-primary-500 dark:placeholder:text-gray-500 classic:focus:ring-primary-600 classic:ring-black"
                                placeholder="{{ __('messages.t_search_ads') }}">
                            <x-icon-close x-on:click="search = ''; open = false"
                                class="w-4 h-4 text-gray-400 dark:text-gray-500 absolute right-0 top-0 mt-[.8rem] mr-3 cursor-pointer  classic:text-black"
                                x-show="search.length > 0" x-cloak />
                            <button x-bind:disabled="!open" aria-label="search"
                                x-tooltip="{
                            content: '{{ __('messages.t_tooltip_search') }}',
                            theme: $store.theme,
                        }"
                                class="disabled:text-gray-600 dark:text-gray-500 absolute right-0 top-0  mt-[.4rem] mr-2 text-white bg-primary-600 p-1 rounded-full disabled:bg-gray-200"
                                wire:click="$dispatch('update-ad-data')">
                                <x-icon-search class="w-5 h-5 p-[1px]" />
                            </button>
                        </div>
                        <div class="md:flex-grow-0 flex-grow w-[10rem]">
                            {{--
                        <x-label for="sort-by" class="mb-2 font-medium" value="{{ __('messages.t_sort_by') }}" /> --}}
                            <x-filament::input.wrapper class="w-[10rem] ml-auto">
                            <label for="sort-by" class="sr-only">{{ __('messages.t_aria_label_sort_by') }}</label>
                                <x-filament::input.select wire:model.change="sortBy" id="sort-by">
                                    <option value="date">{{ __('messages.t_date') }}</option>
                                    <option value="price_asc">{{ __('messages.t_price_low_to_high') }}</option>
                                    <option value="price_desc">{{ __('messages.t_price_high_to_low') }}</option>
                                </x-filament::input.select>
                            </x-filament::input.wrapper>
                        </div>
                    </div>
                @endif

                <h2 class="my-4 text-lg">
                    {{ pluralize(
                        is_array($filteredAds) || count($filteredAds) ? count($filteredAds) : 0,
                        __('messages.t_ad'),
                        __('messages.t_ads'),
                        displayCountWhenZero: true,
                    ) }}
                </h2>
                    @if (count($filteredAds))
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-3 gap-4 pb-12">
                        @foreach ($filteredAds as $ad)
                            <livewire:ad.ad-item :$ad wire:key="list-{{ $ad->id }}" :ref="'/view-profile/' . auth()->id()" lazy />
                        @endforeach
                    </div>
                    @else
                        <x-not-found description="{{ __('messages.t_you_have_not_created_any_lists_yet') }}" />
                    @endif
            </div>
        </div>
    </main>

    <livewire:layout.bottom-navigation />
    {{-- Modals (Followers || Following) --}}
    @include('livewire.user.modals.followers-modal')

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let startDate = new Date();
            let elapsedTime = 0;

            const focus = function() {
                startDate = new Date();
            };

            const blur = function() {
                const endDate = new Date();
                const spentTime = endDate.getTime() - startDate.getTime();
                elapsedTime += spentTime;
            };

            const beforeunload = function() {
                const endDate = new Date();
                const spentTime = endDate.getTime() - startDate.getTime();
                elapsedTime += spentTime;
                const timeSpentInSeconds = Math.round(elapsedTime / 1000);
                alert(timeSpentInSeconds);
                Livewire.dispatch('saveTimeSpend', {
                    'timeSpentInSeconds': timeSpentInSeconds
                });
                // elapsedTime contains the time spent on page in milliseconds
            };

            window.addEventListener('focus', focus);
            window.addEventListener('blur', blur);
            window.addEventListener('beforeunload', beforeunload);

        });
    </script>
    @if ($user->business_hours)
        <x-modal.index id="business-hours">
            <x-slot name="heading">
                <div class="flex gap-1 items-center">
                    <x-heroicon-o-clock class="w-4 h-4" />
                    {{ __('messages.t_business_hours') }}
                </div>
            </x-slot>
            <div class="classic:border-black border rounded-lg overflow-hidden dark:border-neutral-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-700">
                      <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{ __('messages.t_day') }}</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">{{ __('messages.t_timing') }}</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                        @foreach ($user->business_hours as $days => $hours)

                      <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                            <span >
                            {{ $days }}
                            </span>
                    </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"> {{ $hours }}</td>
                      </tr>
                      @endforeach

                    </tbody>
                  </table>
            </div>
        </x-modal.index>
    @endif
</div>
