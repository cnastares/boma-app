<a {{ $attributes->merge(['href' => '/', 'class' => '']) }} >
    <style>
        @media (max-width: 767px) {
            .custom-logo {
                height: @php echo $generalSettings->logo_height_mobile.'rem'@endphp;
            }
        }

        @media (min-width: 768px) {
            .custom-logo {
                height: @php echo $generalSettings->logo_height_desktop.'rem' @endphp;
            }
        }
    </style>
    <img src="{{ getSettingMediaUrl('general.logo_path', 'logo', asset('images/logo.svg')) }}" alt="{{config('app.name')}}"
         class="custom-logo dark:filter dark:invert " />
</a>
