<!-- Skip Links Container -->
<nav class="skip-link-container" aria-label="{{__('messages.t_aria_label_skip_links')}}">
    <!-- Skip Links -->
    @foreach ($links as $id => $link)
    <a
        href="#{{$id}}"
        id="main-link"
        tabindex="1"
        class="absolute -top-[9999px] -left-[9999px] focus:top-4 focus:left-4 bg-black text-white px-4 py-2 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 z-50"
        aria-label="{{$link}}"
        accesskey="{{ strtolower(substr($id, 0, 1)) }}"
    >
        {{$link}}
    </a>
    @endforeach
</nav>
