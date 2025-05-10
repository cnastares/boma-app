@if ($adPlacementSettings->before_footer)
<div x-show="!showFullScreenMap" x-cloak :class="{'w-0 h-0':showFullScreenMap}" role="complementary" aria-label="{{ __('messages.t_aria_label_footer_advertisement')}}">
    <div class="container mx-auto px-4 flex items-center justify-center md:py-10 py-24">
        {!! $adPlacementSettings->before_footer !!}
    </div>
</div>
@endif
