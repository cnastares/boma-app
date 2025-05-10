<div x-data="{ showAgePopup: false,
 selectedCategoryId: null,
  init(){
  $wire.checkAgeVerify();
}
   }" x-show="showAgePopup" x-cloak x-trap.noscroll="showAgePopup" x-on:show-verify-age-popup.window="
        selectedCategoryId = $event.detail.categoryId;console.log($event.detail);
        selectedAgeValue = $event.detail.ageValue;
        showAgePopup = true;
    " class="fixed inset-0 flex items-center justify-center bg-black backdrop-filter backdrop-blur-sm bg-opacity-60 z-50">

    <!-- Popup Modal -->
    <div x-transition:enter="duration-300 ease-out scale-95" x-transition:leave="duration-300 ease-in scale-95"
        class="bg-white dark:bg-gray-900 p-6 md:p-8 rounded-lg shadow-xl w-full max-w-lg text-center relative">

        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <x-heroicon-o-user-circle class="w-10 h-10 text-success-600" />
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-semibold dark:text-white text-gray-800">
            {{ __('messages.t_age_verify_label') }}
        </h2>

        <!-- Dynamic Age Value -->
        <p class="text-gray-600 mt-3 dark:text-white leading-relaxed">
            @if($canRepeat)
            {{ __('messages.t_age_verify_category_message', ['age' => $ageValue]) }}
            @else
            {{ __('messages.t_age_verify_ad_message', ['age' => $ageValue]) }}
            @endif
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
            <!-- Confirm Button -->
            <button @click="showAgePopup = false; $wire.ageVerified(selectedCategoryId)" type="button"
                class="bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2 flex-1">
                <x-heroicon-o-check-circle class="w-5 h-5" />
                {{ __('messages.t_age_verify_ok') }}
            </button>

            <!-- Cancel Button -->
            <button type="button" x-on:click="showAgePopup = false;$wire.redirectPreviousUrl()"
                class="bg-red-600 text-white px-5 py-2.5 rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2 flex-1">
                <x-heroicon-o-x-circle class="w-5 h-5" />
                {{ __('messages.t_age_verify_fail') }}
            </button>
        </div>
    </div>
</div>
