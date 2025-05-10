<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family={{str_replace(' ', '+',$appearanceSettings?->font)??'DM+Sans'}}:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            :root {
                --font:{{$appearanceSettings?->font?? 'DM Sans'}}
            }
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: var(--font),ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 36px;
                padding: 20px;
            }
        </style>
    </head>
    <body>
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
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title">
                    @yield('message')
                </div>
            </div>
        </div>
    </body>
</html>
