<div class="flex items-center gap-x-2">
    {{-- @if(!request()->is('admin*'))
    <a href="{{route('home')}}" tabindex="1" >
        <span class="sr-only">{{ __('messages.t_aria_label_back_to_home') }}</span>
        <x-icon-arrow-left-1 class="w-6 h-6 cursor-pointer" aria-hidden="true" />
    </a>
    @endif --}}
    @if ($appearanceSettings->display_site_name)
    <p class="text-2xl font-bold">{{ $generalSettings->site_name }}</p>
    @else
    <img src="{{ getSettingMediaUrl('general.logo_path', 'logo', asset('images/logo.svg')) }}" alt="Logo"
        class="h-5 dark:filter dash-logo " />
    @endif

</div>
