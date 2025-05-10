@if($adPlacement)
<div class="pb-5 w-full">
    @if ($adPlacement->ad_type == 'images')
        @php
        $data = collect($adPlacement->images)->map(function ($item) {
            $item['image'] = Storage::url($item['image']);
            return $item;
        })->toArray();
        @endphp
            @include('components.ad-placements.alpine-slider',['data' => $data])
    @else
        {!! $adPlacement->value !!}
    @endif
</div>
@endif
