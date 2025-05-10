<div>
    @if($authSettings->enable_google_login)
    <a class="flex items-center border classic:border-black dark:border-white/10 justify-center bg-[#3A67C1] text-white px-3 py-2 rounded mb-4 w-full" href="{{ route('socialite.auth', 'google') }}">
        <x-icon-google class="mr-2" /> Google
    </a>
    @endif
    @if($authSettings->enable_facebook_login)
    <a class="flex items-center border classic:border-black dark:border-white/10 justify-center bg-[#1F4E9C] text-white px-3 py-2 rounded mb-4 w-full" href="{{ route('socialite.auth', 'facebook') }}">
        <x-icon-facebook-2 class="mr-2" /> Facebook
    </a>
    @endif

    @if($loginOtpSettings->enabled && !str_contains(url()->current(),'register'))

        <a class="flex items-center border classic:border-black dark:bg-white/10 dark:border-white/10 justify-center bg-[#BEBEBEBB]  px-3 py-2 rounded mb-4 w-full"
        href="{{ route('loginWithOtp') }}">{{ __('messages.t_login_using_mobile_number') }}</a>

    @endif
    @if ($authSettings->enable_google_login || $authSettings->enable_facebook_login || $loginOtpSettings->enabled)
     <!-- Or Divider -->
     <div class="flex items-center my-10 mb-4">
         <hr class="flex-1 classic:border-black dark:border-white/10">
         <span class="mx-2 ">or</span>
         <hr class="flex-1 classic:border-black dark:border-white/10">
     </div>
     @endif
</div>
