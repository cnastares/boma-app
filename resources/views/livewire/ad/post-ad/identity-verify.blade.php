<div x-show="showIdentityPopup" x-cloak
   x-trap.noscroll="showIdentityPopup"

   x-on:show-identity-verification-popup.window="showIdentityPopup = true"
   class="fixed inset-0 flex items-center justify-center bg-black backdrop-filter backdrop-blur-sm bg-opacity-60 z-50" >
   <div    x-transition:enter="duration-300 ease-out scale-95"
   x-transition:leave="duration-300 ease-in scale-95" class="bg-white dark:bg-gray-900 p-6 md:p-8 rounded-lg shadow-xl md:max-w-[35%] max-w-[80%] w-full text-center relative">
      <div class="flex justify-center mb-4">
         <x-heroicon-o-shield-check class="w-10 h-10 text-success-600"/>
      </div>
      <h2 class="text-2xl font-semibold  dark:text-white text-gray-800">{{ __('messages.t_identity_verify_label') }}</h2>
      <p class="text-gray-600 mt-3  dark:text-white leading-relaxed">
         {{ __('messages.t_identity_verify_helper') }}
      </p>
      <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
         <button type="button" @click="showIdentityPopup = false; $wire.verifyCenter()"
            class="bg-green-600 text-white px-5 py-2.5 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2 flex-1">
            <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ __('messages.t_identity_verify_center') }}
         </button>
         <button type="button" @click="showIdentityPopup = false; $wire.goHome()"
            class="bg-red-600 text-white px-5 py-2.5 rounded-lg hover:bg-red-700 transition flex items-center justify-center gap-2 flex-1">
            <x-heroicon-o-x-circle class="w-5 h-5" />
            {{ __('messages.t_identity_verify_home') }}
         </button>
      </div>
   </div>
</div>
