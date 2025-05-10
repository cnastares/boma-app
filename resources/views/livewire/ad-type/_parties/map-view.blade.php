@if (isMapViewEnabled() && !(isShowMapInFullScreen() && $currentView!='map'))
<div wire:ignore.self id="map-component" class=" col-span-12 md:h-[100vh] md:sticky  top-0 md:block
                {{(isMapViewShowFilterPopup() ?
                (isShowMapInFullScreen() && $currentView=='map' ? 'md:col-span-12' : 'md:col-span-5')
                :(isShowMapInFullScreen() && $currentView=='map' ? 'md:col-span-9' : 'md:col-span-5'))}} "
    :class="{ 'hidden ': !showFullScreenMap }">
    <livewire:map-view :ads="$ads->items()" :$adsCountByLocation />
</div>
@endif