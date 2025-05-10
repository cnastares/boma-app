@if (mapMarkerDisplayType() !='count' && isMapViewEnabled())
<div class="md:hidden   flex justify-center items-center  fixed w-full h-[100px] bottom-12 "
    style="z-index: 2">
    <button type="button"
        class="flex bg-white py-1 px-3 rounded-md transition-all dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 classic:ring-black  items-center gap-1"
        x-on:click="showFullScreenMap=!showFullScreenMap">
        <x-heroicon-o-map-pin x-show="!showFullScreenMap" class="h-5 w-5" x-cloak teleport />
        <x-heroicon-o-adjustments-horizontal x-show="showFullScreenMap" x-cloak teleport class="h-5 w-5" />
        <span x-show="!showFullScreenMap" x-cloak teleport> {{ __('messages.t_map_view') }}</span>
        <span x-show="showFullScreenMap" x-cloak teleport> {{ __('messages.t_list_view') }}</span>
    </button>
</div>
@endif
