<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ config()->get('direction') }}"
    x-data="{ theme: {{ ($appearanceSettings->enable_theme_switcher||$appearanceSettings->enable_contrast_toggle) ? '$persist(\'' . ($appearanceSettings->default_theme ?? 'light') . '\')' : '\'' . ($appearanceSettings->default_theme ?? 'light') . '\'' }}, isMobile: window.innerWidth < 1024 }"
    x-init="theme = new URL(window.location.href).searchParams.get('theme') || theme"
    :class="{ 'dark': theme === 'dark', 'classic': theme === 'classic' }" @resize.window="isMobile = window.innerWidth < 1024">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf_token" value="{{ csrf_token() }}"/>
        <!-- Scripts -->
        <title>{{ $title ?? $generalSettings->site_name ?: config('app.name', 'AdFox') }}</title>

        <meta
            name="description"
            content="{{ $description ?? $generalSettings->site_description }}"
        />
        <link rel="icon" type="image/png" href="{{ getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png')) }}">

         <!-- PWA  -->
         <meta name="theme-color" content="#6777ef"/>
         @foreach ($pwaSettings->icons as $item)
            <link rel="apple-touch-icon" sizes="{{$item['sizes']}}" href="{{\Storage::url($item['src']) }}">
         @endforeach
         <link rel="manifest" href="/manifest.json?v={{ time() }}"  >

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        {{-- <link href="https://fonts.googleapis.com/css2?family={{str_replace(' ', '+',$appearanceSettings?->font)??'DM+Sans'}}:ital,wght@0,100..900;1,100..900&display=swap" rel="preload" as="style" onload="this.rel='stylesheet'"> --}}
        <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $appearanceSettings?->font ?? 'DM Sans') }}:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        @filamentStyles
        @vite('resources/css/app.css')
        <!-- Insert Custom Script in Head -->
        {!! $scriptSettings->custom_script_head !!}
        <style>
            :root {
                --font:{{$appearanceSettings?->font?? 'DM Sans'}}
            }
            {!! $styleSettings->custom_style !!}
        </style>
    </head>
    <body class="font-sans bg-gray-50 antialiased dark:bg-gray-950 dark:text-white classic:bg-gray-100">

        <noscript>
            <div class="fixed inset-0 flex items-center justify-center bg-gray-200 text-gray-900 p-5">
                <p class="text-lg font-semibold">
                    JavaScript is required. Please enable it in your browser settings.
                </p>
            </div>
        </noscript>


        <!-- Skip links -->
        @include('components.skip-links',['links'=>[
            'main-content'=> __('messages.t_skip_to_main_content'),
        ]])

        <div class="flex min-h-screen">
            <!-- Left Section -->
            <div class="md:w-1/2 md:flex md:flex-col p-8 w-full">
                {{ $slot }}
            </div>

            <!-- Right Section -->
            <aside class="w-1/2 hidden md:flex items-center justify-center bg-white border-l border-gray-200 dark:border-white/10 dark:bg-gray-900 classic:border-black">
                <img src="{{ asset('/images/auth.svg') }}" alt="Image" class="max-w-full h-auto" />
            </aside>
        </div>

        @if($authSettings->recaptcha_enabled)
            {!! GoogleReCaptchaV3::init() !!}
        @endif


        @filamentScripts
        @vite('resources/js/app.js')
        @stack('scripts')

         <!-- Insert Custom Script in Body -->
         {!! $scriptSettings->custom_script_body !!}

         @php
            $primaryColor = $appearanceSettings?->primary_color ?? '#FDae4B'; // Default to a fallback color if not set
            $primaryRgb = hexToRgb($primaryColor);
            $darkerPrimaryRgb1 = adjustBrightness($primaryRgb, 60);
            $darkerPrimaryRgb10 = adjustBrightness($primaryRgb, -10);
            $darkerPrimaryRgb20 = adjustBrightness($primaryRgb, -20);
            $darkerPrimaryRgb25 = adjustBrightness($primaryRgb, -25);
        @endphp


        <style>
            :root {
                --primary-50: {{ $darkerPrimaryRgb1 }};
                --primary-400: {{ $primaryRgb }};
                --primary-500: {{ $darkerPrimaryRgb10 }};
                --primary-600: {{ $darkerPrimaryRgb20 }};
                --primary-700: {{ $darkerPrimaryRgb25 }};
                --primary  : {{ $appearanceSettings?->primary_color?? 'rgba(253, 174, 75, 1)' }};
                --secondary  : {{ $appearanceSettings?->secondary_color?? '#FEBD69' }};
            }
        </style>

        <script src="{{ asset('js/service-worker.js') }}"></script>

    </body>
</html>

