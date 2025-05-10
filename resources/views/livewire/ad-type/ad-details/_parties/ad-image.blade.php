
@if (optional(current($customizationSettings->ad_detail_page))['enable_carousel'])
<x-ad.carousel-gallery :ad="$ad" :images="$ad->images()" :videoLink="$ad->video_link" class="" :image_properties="$ad->image_properties"
    :ad_title="$ad->title" />
@else
<x-ad.gallery :images="$ad->images()" :videoLink="$ad->video_link" class="" :image_properties="$ad->image_properties"
    :ad_title="$ad->title" :adId="$ad->id" :$isFavourited  />
@endif


@if ($isUrgent)
<div x-tooltip="{content: '{{__('messages.t_tooltip_urgent_ad')}}', theme: $store.theme}"
    class="px-2 py-1 text-sm font-medium border border-black absolute {{ $isUrgent && $isFeatured ? 'top-16' : 'top-6' }} left-6 bg-red-600 z-10 text-black"
    @if ($urgentAdColors)
    style="{{$urgentAdColors->background_color?'background:'.$urgentAdColors->background_color:'#DC2626'}} ;{{$urgentAdColors->text_color?'color:'.$urgentAdColors->text_color:'#000000;'}}"
    @endif>
    {{ __('messages.t_urgent_ad') }}
</div>
@endif

@if ($isFeatured)
<div x-tooltip="{content: '{{__('messages.t_tooltip_featured_ad')}}', theme: $store.theme,}"
    class="px-2 py-1 text-sm font-medium border border-black absolute top-6 left-6 bg-yellow-400 z-[1] text-black"
    @if ($featureAdColors)
    style="{{$featureAdColors->background_color?'background:'.$featureAdColors->background_color:'#FACC15'}}; {{$featureAdColors->text_color?'color:'.$featureAdColors->text_color:'#000000;'}}"
    @endif>
    {{ __('messages.t_featured_ad') }}
</div>
@endif
