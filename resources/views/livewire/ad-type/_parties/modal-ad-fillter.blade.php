@if (isMapViewShowFilterPopup())
<div :class="{'w-0 h-0':showFullScreenMap}">
    <!-- Filter modal  -->

    <x-modal.index id="ad-filter-modal" width="2xl">
        <x-slot name="heading">
            {{ __('messages.t_filter') }}
        </x-slot>
        <div>
            <livewire:ad-type.filter-by-ad :$filters :fieldData="$fieldFilter" :selectFilterData="$selectFieldFilter" :$categorySlug :$subCategorySlug :$adType />
        </div>
    </x-modal.index>
</div>
@endif
