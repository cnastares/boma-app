<div x-show="showAgePopup" x-cloak x-trap.noscroll="showAgePopup"
    x-on:show-age-verification-popup.window="selectedCategoryId = event.detail.categoryId;selectedfieldName = event.detail.fieldNames; showAgePopup = true"
    class="fixed inset-0 flex items-center justify-center bg-black backdrop-filter backdrop-blur-sm bg-opacity-60 z-50">
    <div x-transition:enter="duration-300 ease-out scale-95" x-transition:leave="duration-300 ease-in scale-95"
        class="bg-white dark:bg-gray-900 p-6 md:p-8 rounded-lg shadow-xl w-full max-w-lg text-center relative">
        <div class="flex justify-center mb-4">
            <x-heroicon-o-user-circle class="w-10 h-10 text-success-600" />
        </div>
        <h2 class="text-2xl font-semibold dark:text-white text-gray-800">{{ __('messages.t_age_verify_label') }}</h2>

        <p class="text-gray-600 mt-3 dark:text-white leading-relaxed">
            {{ __('messages.t_age_verify_helper', ['age' => $ageValue]) }}
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
            <button @click="showAgePopup = false; $wire.verifyIdentity(selectedCategoryId,selectedfieldName)"
                type="button"
                class="bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2 flex-1">
                <x-heroicon-o-check-circle class="w-5 h-5" />
                {{ __('messages.t_age_verify_ok') }}
            </button>

            <button type="button" x-on:click="showAgePopup = false; ageConfirmed = false;
                showChildCategory = (selectedfieldName!='category_id');
                $dispatch('check-category', { categoryId: selectedCategoryId });"
                class="bg-red-600 text-white px-5 py-2.5 rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2 flex-1">
                <x-heroicon-o-x-circle class="w-5 h-5" />
                {{ __('messages.t_age_verify_fail') }}
            </button>
        </div>
    </div>
</div>
