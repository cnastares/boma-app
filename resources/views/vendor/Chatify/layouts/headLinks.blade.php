<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}"  dir="{{ config()->get('direction') }}" x-data="{ theme: $persist('light'), isMobile: window.innerWidth < 1024 }" x-init="theme = new URL(window.location.href).searchParams.get('theme') || theme"  :class="{ 'dark': theme === 'dark', 'classic': theme === 'classic' }" @resize.window="isMobile = window.innerWidth < 1024">
<head>
{{-- Meta tags --}}
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="id" content="{{ $id }}">
<meta name="messenger-color" content="{{ $messengerColor }}">
<meta name="messenger-theme" content="{{ $dark_mode }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">


{{-- Generate seo tags --}}
{!! SEO::generate() !!}
{!! JsonLd::generate() !!}
{{-- scripts --}}


<link rel="icon" type="image/png" href="{{ getSettingMediaUrl('general.favicon_path', 'favicon', asset('images/favicon.png')) }}">


{{-- styles --}}
@vite('resources/css/app.css')

<link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>
<link rel="stylesheet" href="{{ asset('js/plugins/emojipanel/dist/emojipanel.css') }}" />
<link rel="stylesheet" href="{{ asset('js/plugins/file-icon-vectors/file-icon-vectors.min.css') }}" />
<link href="{{ asset('css/chatify/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/chatify/'.$dark_mode.'.mode.css') }}" rel="stylesheet" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family={{str_replace(' ', '+',$appearanceSettings?->font)??'DM+Sans'}}:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
{{-- Setting messenger primary color to css --}}
<style>
    :root {
        --primary-color: {{ $messengerColor }};
        --font:{{$appearanceSettings?->font?? 'DM Sans'}}
    }
</style>

{{-- Messenger Color Style--}}
@include('Chatify::layouts.messengerColor')
@vite('resources/js/app.js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/chatify/font.awesome.min.js') }}"></script>
<script src="{{ asset('js/chatify/autosize.js') }}"></script>
<script src="{{ asset('js/plugins/twemoji/twemoji.min.js') }}"></script>
<script src="{{ asset('js/plugins/nprogress/nprogress.js') }}"></script>
<script src="{{ asset('js/plugins/emoji-mart/dist/browser.js') }}"></script>

 <!-- Insert Custom Script in Head -->
 {!! $scriptSettings->custom_script_head !!}
</head>
