<div x-data class="pb-14 md:pb-4 bg-white dark:bg-gray-700">

    <header class="flex justify-center items-center md:py-9 py-6">
        <x-brand />
    </header>

    <div class="container mx-auto px-5 ">

        <main class="flex flex-col md:flex-row gap-2">
            <div class="flex-1 max-w-2xl">
                <img src="{{$this->notificationRegistrationSettings->banner_image?Storage::url($this->notificationRegistrationSettings->banner_image) : '/images/notification-register.svg'}}" alt="{{__('messages.t_notification_banner')}}"
                    class="w-full h-[400px] object-cover rounded-lg">
            </div>
            <div class="flex-1  mt-4 md:mt-0 flex">
                <div
                    class="bg-[#14312d] text-white p-6 rounded-lg shadow-lg w-full h-full flex justify-center flex-col">
                    <h1 class="text-2xl font-bold border-b  border-gray-400  pb-5 text-center">
                        {{__('messages.t_our_website_is_under_construction')}}</h1>
                    <p class="mt-4 text-xl text-center font-semibold  border-b  border-gray-400 pb-5">
                        {{__('messages.t_a_website_with_aaa_level_accessibility')}}</p>
                    <p class="mt-4 text-xl text-center">{{__('messages.t_notification_register_description')}}</p>
                </div>
            </div>
        </main>
    </div>

    <section class=" py-12 container mx-auto px-5 ">
        <h2 class="text-2xl md:text-3xl font-semibold mb-6 text-center">
            {{__('messages.t_register_to_participate_in_this_experience')}}</h2>
        <form class=" mx-auto space-y-4" wire:submit.prevent='register' class="space-y-6" >
            <div class="md:flex justify-center  md:space-x-6 space-y-6 md:space-y-0">
                <!-- Name Input -->
                @csrf
                <div class="md:w-72">
                    <x-label for="name" :value="__('messages.t_your_name')" class="text-gray-700 dark:text-gray-300" />

                    <x-filament::input.wrapper class="mt-1 !ring-gray-400 ">
                        <x-filament::input id="name" type="text" name="name" :value="old('name')" required
                            class="h-10 rounded-lg notification-registration-focus"  wire:model.live.debounce.250ms='name'  aria-describedby="name-error" />
                    </x-filament::input.wrapper>
                    <p data-validation-error="" id="name-error" class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400">@error('name') {{ $message }} @enderror</p>
                    <div class="text-red-500"></div>

                </div>
                <!-- Email Input -->

                <div class="md:w-72">
                    <x-label for="email" :value="__('messages.t_your_email')" class="text-gray-700 dark:text-gray-300" />
                    <div class="mt-1">
                        <x-filament::input.wrapper class="mt-1 !ring-gray-400 ">
                            <x-filament::input id="email" wire:model.live.debounce.250ms='email' type="text" name="email"
                                :value="old('email')" autocomplete="email" required  class="h-10 rounded-lg notification-registration-focus" aria-describedby="email-error" />
                        </x-filament::input.wrapper>
                        <p data-validation-error="" id="email-error" class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400" >@error('email') {{ $message }} @enderror</p>

                    </div>
                </div>
                <!-- Submit Button -->
                <div>
                    <button @disabled((!$name) || (!$email)) type="submit"
                        class="bg-[#ff914d]  w-full  text-center text-black font-bold py-[8px] mt-5 disabled:text-white  px-6 rounded-full text-base md:text-lg transition-all border-black  disabled:bg-gray-500 notification-registration-focus">
                        <x-loading-animation wire:target="register" />
                        {{__('messages.t_register')}}
                    </button>
                </div>
            </div>
        </form>
    </section>

    <div class="container mx-auto px-5">
        <footer class=" border-t border-black py-6 dark:border-white/20">
            <div
                class="max-w-7xl mx-auto  text-center mt-4 md:mt-8 space-y-4 md:space-y-0 md:flex md:justify-center gap-x-8 md:items-center">
                @if ($this->notificationRegistrationSettings->notification_email)

                <div class="flex items-center space-x-2 dark:text-white text-black">
                    <!-- Email Icon (use appropriate email icon here) -->
                    <svg class="w-6 h-6 " fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20" aria-hidden="true" aria-label="email">
                        <path
                            d="M1.574 5.286l7.5 4.029c.252.135.578.199.906.199.328 0 .654-.064.906-.199l7.5-4.029c.489-.263.951-1.286.054-1.286H1.521c-.897 0-.435 1.023.053 1.286zm17.039 2.203l-7.727 4.027c-.34.178-.578.199-.906.199s-.566-.021-.906-.199-7.133-3.739-7.688-4.028C.996 7.284 1 7.523 1 7.707V15c0 .42.566 1 1 1h16c.434 0 1-.58 1-1V7.708c0-.184.004-.423-.387-.219z">
                        </path>
                    </svg>
                    <a href="mailto:{{$this->notificationRegistrationSettings->notification_email}}"
                        class="flex items-center space-x-2  text-base md:text-lg notification-registration-focus rounded-lg">
                        {{$this->notificationRegistrationSettings->notification_email}}</a>

                </div>
                @endif

                @if ($this->notificationRegistrationSettings->instagram_username)

                <div class="flex items-center space-x-2 dark:text-white text-black">
                    <!-- Instagram Icon (use appropriate Instagram icon here) -->

                    <svg class="w-6 h-6 " viewBox="0 0 48 48" fill="none"
                        xmlns="http://www.w3.org/2000/svg" aria-label="instagram">
                        <rect width="48" height="48" fill="white" fill-opacity="0.01"></rect>
                        <path
                            d="M34 6H14C9.58172 6 6 9.58172 6 14V34C6 38.4183 9.58172 42 14 42H34C38.4183 42 42 38.4183 42 34V14C42 9.58172 38.4183 6 34 6Z"
                            fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linejoin="round"></path>
                        <path class="dark:fill-gray-700"
                            d="M24 32C28.4183 32 32 28.4183 32 24C32 19.5817 28.4183 16 24 16C19.5817 16 16 19.5817 16 24C16 28.4183 19.5817 32 24 32Z"
                            fill="#fff" stroke="#fff" stroke-width="2" stroke-linejoin="round"></path>
                        <path class="dark:fill-gray-700"
                            d="M35 15C36.1046 15 37 14.1046 37 13C37 11.8954 36.1046 11 35 11C33.8954 11 33 11.8954 33 13C33 14.1046 33.8954 15 35 15Z"
                            fill="#fff"></path>
                    </svg>
                    <span class=" text-base md:text-lg"><a target="_blank" class="notification-registration-focus rounded-lg"
                            href="https://www.instagram.com/{{$this->notificationRegistrationSettings->instagram_username}}">{{$this->notificationRegistrationSettings->instagram_username}}</a></span>
                </div>
                @endif
            </div>
    </div>
    <div class="px-10 md:px-0">
        <p class="text-center ">
            © {{ now()->year }} {{ $generalSettings->site_name }}. {{ __('messages.t_all_rights_reserved') }}
        </p>
    </div>
    </footer>
</div>
@if($this->notificationRegistrationSettings->auto_focus_enabled)
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const autoFocusEnabled = true;
    const inputElement = document.getElementById('name');

    if (autoFocusEnabled) {
        inputElement.focus();

        // Optionally, you can also set the 'autofocus' attribute for future reference
        inputElement.setAttribute('autofocus', 'autofocus');
    }

});
</script>
@endif
</div>
