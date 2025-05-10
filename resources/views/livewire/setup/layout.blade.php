<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}"  >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf_token" value="{{ csrf_token() }}"/>

        <!-- Scripts -->
        <title>{{ config('app.name', 'AdFox') }}</title>


        <link rel="preconnect" href="https://fonts.googleapis.com">

        <!-- PWA  -->
        <meta name="theme-color" content="#6777ef"/>
        <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
        <link rel="manifest" href="/manifest.json?v={{ time() }}"  >


        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

        @filamentStyles
        @vite('resources/css/app.css')

        <style>
            :root {
                 --font:'DM Sans'
             }
         </style>
    </head>
    <body class="bg-gray-50  font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white classic:bg-gray-100 classic:text-black">


        @yield('content')

        @php
            $primaryColor = '#FDae4B';
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
                --primary  : {{  $primaryColor }};
                --secondary  : {{ '#FEBD69' }};
            }
        </style>

        @filamentScripts
        @stack('scripts')
    </body>
</html>
