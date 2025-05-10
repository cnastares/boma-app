<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family={{str_replace(' ', '+',$appearanceSettings?->font)??'DM+Sans'}}:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        @filamentStyles
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
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
                --font:{{$appearanceSettings?->font?? 'DM Sans'}};
                --primary-50: {{ $darkerPrimaryRgb1 }};
                --primary-400: {{ $primaryRgb }};
                --primary-500: {{ $darkerPrimaryRgb10 }};
                --primary-600: {{ $darkerPrimaryRgb20 }};
                --primary-700: {{ $darkerPrimaryRgb25 }};
                --primary  : {{ $appearanceSettings?->primary_color?? 'rgba(253, 174, 75, 1)' }};
                --secondary  : {{ $appearanceSettings?->secondary_color?? '#FEBD69' }};
                --font:{{$appearanceSettings?->font?? 'DM Sans'}}
            }
        </style>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-brand />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        @filamentScripts

    </body>
</html>
