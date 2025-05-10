<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ config()->get('direction') }}"
    x-data="{ theme: {{ ($appearanceSettings->enable_theme_switcher||$appearanceSettings->enable_contrast_toggle) ? '$persist(\'' . ($appearanceSettings->default_theme ?? 'light') . '\')' : '\'' . ($appearanceSettings->default_theme ?? 'light') . '\'' }}, isMobile: window.innerWidth < 1024 }"
    x-init="theme = new URL(window.location.href).searchParams.get('theme') || theme"
    :class="{ 'dark': theme === 'dark', 'classic': theme === 'classic' }" @resize.window="isMobile = window.innerWidth < 1024">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf_token" value="{{ csrf_token() }}"/>

        {{-- Generate seo tags --}}
        {!! SEO::generate() !!}
        {{-- {!! JsonLd::generate() !!} --}}

        <link rel="icon" type="image/png" href="{{ getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png')) }}">


        <link rel="preconnect" href="https://fonts.googleapis.com">
        <!-- PWA  -->
        <meta name="theme-color" content="#6777ef"/>
        @foreach ($pwaSettings->icons as $item)
            <link rel="apple-touch-icon" sizes="{{$item['sizes']}}" href="{{\Storage::url($item['src']) }}">
        @endforeach
        <link rel="manifest" href="/manifest.json?v={{ time() }}"  >


        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $appearanceSettings?->font ?? 'DM+Sans') }}:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        @if (app('filament')->hasPlugin('google-location-kit') && $googleSettings->status)
        <script src="https://maps.googleapis.com/maps/api/js?key={{$googleSettings?->api_key }}&libraries={{ isMapViewEnabled() ? 'marker,places' : 'places' }}"
            referrerpolicy="no-referrer-when-downgrade"
            loading="async">
        </script>
        @endif

        @filamentStyles
        @vite('resources/css/app.css')


        <!-- Insert Custom Script in Head -->
        {!! $scriptSettings->custom_script_head !!}

        {!! GoogleReCaptchaV3::init() !!}

        {{-- Styles --}}
        @stack('styles')

        @if ($styleSettings->custom_style)
        <style>
            {!! $styleSettings->custom_style !!}
        </style>
        @endif

        @if($generalSettings->europa_cookie_consent_enabled)
            @cookieconsentscripts
        @endif
    </head>
    <body x-on:close-modal.window="document.documentElement.classList.add('flow-auto')" x-on:open-modal.window="document.documentElement.classList.remove('flow-auto')" class="bg-gray-50  font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white classic:bg-gray-100 classic:text-black">
        <!-- Noscript tag for users with JavaScript disabled -->
        <noscript>
            <div class="fixed inset-0 flex items-center justify-center bg-gray-200 text-gray-900 p-5">
                <p class="text-lg font-semibold">
                    JavaScript is required. Please enable it in your browser settings.
                </p>
            </div>
        </noscript>


        @if($generalSettings->europa_cookie_consent_enabled)
            @cookieconsentview
        @endif



        {{ $slot }}

         @livewire('notifications')

        @if($generalSettings->cookie_consent_enabled)
            <x-cookie-consent />
        @endif

        @livewire('notifications')
        @filamentScripts
        <!-- Insert Custom Script in Body -->
        {!! $scriptSettings->custom_script_body !!}
        @php
            $primaryColor = $appearanceSettings?->primary_color ?? '#FDae4B'; // Default to a fallback color if not set
            $primaryRgb = hexToRgb($primaryColor);
            $darkerPrimaryRgb1 =  adjustBrightness($primaryRgb, 60);
            $darkerPrimaryRgb10 = adjustBrightness($primaryRgb, -10);
            $darkerPrimaryRgb20 = adjustBrightness($primaryRgb, -20);
            $darkerPrimaryRgb25 = adjustBrightness($primaryRgb, -25);
        @endphp

        <style wire:ignore>
            :root {
                --primary-50: {{ $darkerPrimaryRgb1 }};
                --primary-400: {{ $primaryRgb }};
                --primary-500: {{ $darkerPrimaryRgb10 }};
                --primary-600: {{ $darkerPrimaryRgb20 }};
                --primary-700: {{ $darkerPrimaryRgb25 }};
                --primary  : {{ $appearanceSettings?->primary_color?? 'rgba(253, 174, 75, 1)' }};
                --secondary-200  : {{ isset(getSecondaryColorShades()['50'])?'rgb('.getSecondaryColorShades()['50'].')':'#FEBD69' }};
                --secondary-600  : {{ $appearanceSettings?->secondary_color?? '#FEBD69' }};
                --font:{{$appearanceSettings?->font?? 'DM Sans'}}
            },
            *{
                direction: @php echo config()->get('direction'); @endphp;
            }
        </style>

        <script src="{{ asset('js/service-worker.js') }}"></script>
        @stack('scripts')
    </body>
</html>
