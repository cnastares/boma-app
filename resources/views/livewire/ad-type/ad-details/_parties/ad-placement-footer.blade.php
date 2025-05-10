@if ($adPlacementSettings->after_header)
<section class="container mx-auto px-4 py-6 flex items-center justify-center " role="complementary" aria-label="{{ __('messages.t_aria_label_footer_advertisement')}}">
    {!! $adPlacementSettings->after_header !!}
</section>
@endif
