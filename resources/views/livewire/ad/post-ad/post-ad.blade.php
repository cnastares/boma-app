<div>
    <x-slot:title>
        Post Your Ad on {{ $generalSettings->site_name }} - Reach Thousands of Buyers
    </x-slot:title>

    <x-slot:description>
       List your items quickly and effortlessly. Post your ad on {{ $generalSettings->site_name }} today and connect with thousands of potential buyers.
    </x-slot:description>


    <livewire:ad.post-ad.header :$isWebView  :$id  :$current :title="$this->getTitle()"  :stepIndex="$this->getCurrentStepIndex()" :isLastStep="$this->isLastStep()"  />


    <main class="md:w-3/4 lg:w-1/2 mx-auto px-4 pt-6 pb-20 md:pb-6">
       <livewire:dynamic-component  :key="$current" :$ad :$id  :component="$current" :$promotionIds :isLastStep="$this->isLastStep()"  />
       @if($current === 'ad.post-ad.ad-detail' || $current === 'livewire.vehicle-ad-detail')
       <livewire:ad.post-ad.dynamic-field :$id />
        @endif
    </main>
</div>


