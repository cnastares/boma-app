@if ($adType?->enable_filters)
<div x-trap.noscroll="show"
    :class="{ 'block fixed left-0 right-0 top-0 bottom-0 z-30': show, 'hidden': !show }"
    class="col-span-3 {{ !(isMapViewEnabled()) ? 'md:block' : (!isMapViewShowFilterPopup() ? 'md:block md:col-span-3' : '') }}"
    x-cloak>
    <!-- Filter by ad -->
    <livewire:ad-type.filter-by-ad :$filters :fieldData="$fieldFilter" :selectFilterData="$selectFieldFilter" :$categorySlug :$subCategorySlug :$adType />

    <!-- Ad placement -->
    @include('livewire.ad-type._parties.ad-placement')
</div>
@endif


