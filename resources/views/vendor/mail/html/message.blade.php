<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
@if(isset($emailSettings) && $emailSettings->display_logo)
<img height='75px' width='150px' src="{{ getSettingMediaUrl('email.email_logo', 'email_logo', asset('images/logo.svg')) }}" alt="{{ config('app.name') }}"
class="custom-logo dark:filter dark:invert" />
@else
{{ config('app.name') }}
@endif
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. {{__('messages.t_et_all_rights_reserved')}}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
