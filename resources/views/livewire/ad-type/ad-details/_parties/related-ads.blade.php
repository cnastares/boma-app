@if (!$relatedAds->isEmpty())
<section class=" pt-6 pb-36 md:pb-6">
    <div class="container mx-auto px-4">
        <h2 class="text-xl md:text-2xl text-left mb-6 font-semibold">{{ __('messages.t_related_ads') }}
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-y-4  gap-x-4">
            @foreach ($relatedAds as $ad)
            <livewire:ad.ad-item :$ad wire:key="related-{{ $ad->id }}" lazy />
            @endforeach
        </div>
    </div>
</section>
@endif