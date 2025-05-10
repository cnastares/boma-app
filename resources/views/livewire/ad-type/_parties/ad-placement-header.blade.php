@if ($adPlacementSettings->after_header)
<div x-show="!showFullScreenMap" x-cloak>
    <div  class="container mx-auto px-4 flex items-center justify-center md:pt-8 pt-6 " id="header-ad" role="complementary" aria-label="{{ __('messages.t_aria_label_header_advertisement')}}">
        {!! $adPlacementSettings->after_header !!}
    </div>
</div>
@endif
