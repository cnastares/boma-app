<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full"
>
    <head>
        <meta charset="utf-8">
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <meta
            name="csrf-token"
            content="{{ csrf_token() }}"
        >
        <meta
            name="robots"
            content="noindex, nofollow"
        >

        <title>@yield('title') - {{ config('app.name') }}</title>

        <!-- Favicon -->
        <link
            rel="icon"
            href="{{ getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png')) }}"
        >

        <!-- Fonts -->
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family={{str_replace(' ', '+',$appearanceSettings?->font)??'DM+Sans'}}:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <!-- Styles -->
        @vite('resources/css/app.css')
        @php
        $primaryColor = $appearanceSettings?->primary_color ?? '#FDae4B'; // Default to a fallback color if not set
        $primaryRgb = hexToRgb($primaryColor);
        $darkerPrimaryRgb1 =  adjustBrightness($primaryRgb, 60);
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
            --font:{{$appearanceSettings?->font?? 'DM Sans'}}
        }
        {!! $styleSettings->custom_style !!}

    </style>
    </head>
    <body class="antialiased font-sans h-full">
        <div class="min-h-full pt-16 pb-12 flex flex-col bg-white">
            <main class="flex-grow flex flex-col justify-center max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex-shrink-0 flex justify-center">
                    <a href="/">
                        <span class="sr-only">{{ config('app.name') }}</span>
                        <img
                            src="{{ getSettingMediaUrl('general.logo_path', 'logo', asset('images/logo.svg')) }}"
                            alt="{{ config('app.name') }}"
                            class="h-8 w-auto"
                        >
                    </a>
                </div>
                <div class="mt-1 py-16">
                    <div class="text-center">
                        <p class="text-sm font-semibold text-primary-600 uppercase tracking-wide">@yield('code') error</p>
                        <h1 class="mt-2 text-4xl font-bold text-slate-900 tracking-tight sm:text-5xl">@yield('title').</h1>
                        <p class="mt-2 text-base text-slate-500">@yield('message').</p>
                        <div class="mt-6">
                            <a
                                href="/"
                                class="text-base font-medium text-primary-600 hover:text-primary-400"
                            >
                            {{ __('messages.t_go_back_home') }}<span aria-hidden="true"> &rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
