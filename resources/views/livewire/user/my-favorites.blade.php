<div>
    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
        'main-content'=> __('messages.t_skip_to_main_content')
    ]])
    
    <livewire:layout.header isMobileHidden lazy />
    <x-page-header title="{{ __('messages.t_my_favourites') }}" isMobileHidden :$referrer />

    <x-user-navigation />

    <main id="main-content" class="sticky-scroll-margin" >
    <div class="container mx-auto px-4 py-10">
        @if($ads->isEmpty())
          <x-not-found description="{{ __('messages.t_no_favourite_ads') }}" />
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-4">
                @foreach($ads as $ad)
                    <livewire:ad.ad-item :$ad wire:key="$ad->id" ref="\my-favorites" lazy  />
                @endforeach
            </div>
        @endif
    </div>
    </main>
</div>
