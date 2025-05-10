@props(['images' => [], 'videoLink' => null, 'image_properties' => null, "ad_title" => null,"adId"=>null,"isFavourited"=>false])


<div x-data='{
    media: [],
    images: {!! json_encode($images) !!},
    activeMediaIndex: 0,
    videoLink: "{{ $videoLink }}",
    imageProperties: {!! e(json_encode($image_properties)) !!},
    showModal: false,
    adTitle: "{{ $ad_title }}",
    placeholder: "{{ getAdPlaceholderImage($adId) }}",
    init() {
        let videoThumbnail = this.getYouTubeThumbnail();
        this.media = videoThumbnail ? [{ type: "video", src: videoThumbnail, embedUrl: this.computedVideoLink() }].concat(this.images.map(image => ({ type: "image", src: image }))) : this.images.map(image => ({ type: "image", src: image }));
    },
    getYouTubeThumbnail() {
        if (!this.videoLink) return "";
        const videoIdMatch = this.videoLink.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
        return videoIdMatch ? `https://img.youtube.com/vi/${videoIdMatch[1]}/sddefault.jpg` : "";
    },
    computedVideoLink() {
        if (!this.videoLink) return "";
        const videoIdMatch = this.videoLink.match(/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
        return videoIdMatch ? `https://www.youtube.com/embed/${videoIdMatch[1]}?autoplay=1` : "";
    }
}' x-init="init" :class="images.length > 0 ? 'border-b dark:border-white/20 classic:border-black' : ''" >
   <!-- Main Image -->
   <div  class="w-full md:h-[25rem] h-[10rem] relative border-b border-gray-200 dark:border-white/20 classic:border-black md:ltr:rounded-tl-xl  md:rtl:rounded-tr-xl bg-black ">

        <img @click="showModal = true"  :src="media.length > 0 ? media[activeMediaIndex].src : placeholder" class="absolute cursor-pointer w-full h-full object-contain object-center md:rounded-tl-xl" :alt="media.length > 0 && media[activeMediaIndex].type === 'image' ? (imageProperties && imageProperties[`${activeMediaIndex + 1}`] || adTitle) : 'Video Thumbnail'" />

        <template x-if="media[activeMediaIndex] && media[activeMediaIndex].type === 'video'">
            <div  @click="showModal = true" class="absolute top-0 left-0 right-0 bottom-0 flex items-center justify-center cursor-pointer">
              <img src="{{ asset('/images/youtube.png') }}" class="w-20" />
            </div>
        </template>
        <!-- Left arrow -->
        <button x-show="media.length>1" @click="activeMediaIndex = activeMediaIndex === 0 ? media.length - 1 : activeMediaIndex - 1" class="absolute -mt-5 left-0 top-1/2 my-auto bg-black bg-opacity-50 p-2"
            x-tooltip="{
                content: '{{__('messages.t_tooltip_previous')}}',
                theme: $store.theme,
            }"
            aria-label="{{__('messages.t_aria_label_previous')}}"
            >
            <x-heroicon-o-arrow-left class="w-6 h-6 text-white" aria-hidden="true" />
        </button>
         <!-- Right arrow -->
        <button x-show="media.length>1" @click="activeMediaIndex = activeMediaIndex === media.length - 1 ? 0 : activeMediaIndex + 1" class="absolute -mt-5 right-0 top-1/2 my-auto bg-black bg-opacity-50 p-2"
            x-tooltip="{
                content: '{{__('messages.t_tooltip_next')}}',
                theme: $store.theme,
            }"
            aria-label="{{__('messages.t_aria_label_next')}}"
            >
            <x-heroicon-o-arrow-right class="w-6 h-6 text-white" aria-hidden="true" />
        </button>


         <!-- Maximize -->
         @if (count($images))
         <button  type="button" @click="showModal = true" class="absolute top-5 right-16 mr-2 cursor-pointer"
         x-tooltip="{
            content: '{{__('messages.t_tooltip_expand')}}',
            theme: $store.theme,
        }"
        aria-label="{{__('messages.t_aria_label_expand')}}"
        >
            <x-heroicon-o-arrows-pointing-out class=" w-7 h-7 lg:w-9 lg:h-9 text-white dark:text-gray-400" aria-hidden="true" />
        </button>
        @endif

        <!-- Favourite Button -->
        @if (!optional(current($customizationSettings->ad_detail_page))['enable_favourite_move_to_ad_action'])
        <div class="absolute top-4 right-4 z-10 text-white">
            <x-ad.favourite-ad :$isFavourited />
        </div>
        @endif
    </div>
    <!-- Thumbnails -->
    <div class="flex gap-x-4 overflow-x-auto" :class="images.length > 0 ? 'm-4' : ''" role="list" aria-label="{{__('messages.t_aria_label_thumbnails')}}">
        <template x-for="(image, index) in media" :key="index">
            <img @click="activeMediaIndex = index" :src="image.src" :alt="image.type === 'image' ? (imageProperties && imageProperties[`${index + 1}`] || adTitle) : 'Video Thumbnail'" :class="{ 'classic:border-2 classic:border-black dark:border-2 dark:border-black dark:border-primary-600': activeMediaIndex === index }" class="w-16 h-16 object-cover cursor-pointer rounded" alt="{{__('messages.t_aria_label_thumbnail')}}"
            role="listitem" :aria-selected="activeMediaIndex === index" />
        </template>
    </div>

    <!-- Image Modal -->
    <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center" x-cloak>
        <div class="relative mx-auto rounded-md">
            <div class="p-10">
                <template x-if="media[activeMediaIndex] && media[activeMediaIndex].type === 'video'">
                    <iframe class="w-[90vw] h-[90vh] rounded-md" :src="media[activeMediaIndex].embedUrl" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </template>
                <template x-if="media[activeMediaIndex] && media[activeMediaIndex].type === 'image'">
                    <img :src="media[activeMediaIndex].src" class="w-full h-[90vh] object-contain rounded-md" alt="Large Image" :alt="media.length > 0 && media[activeMediaIndex].type === 'image' ? (imageProperties && imageProperties[`${activeMediaIndex + 1}`] || adTitle) : 'Video Thumbnail'" />
                </template>
            </div>
            <!-- Left arrow -->
            <button x-show="media.length>1" @click="activeMediaIndex = activeMediaIndex === 0 ? media.length - 1 : activeMediaIndex - 1" class="fixed top-1/2 left-0 ml-2 transform -translate-y-1/2 bg-black bg-opacity-50 p-2"
                x-tooltip="{
                    content: '{{__('messages.t_tooltip_previous')}}',
                    theme: $store.theme,
                }"
                aria-label="{{__('messages.t_aria_label_previous')}}">
                <x-heroicon-o-arrow-left class="w-6 h-6 text-white" aria-hidden="true" />
            </button>

            <!-- Right arrow -->
            <button x-show="media.length>1" @click="activeMediaIndex = activeMediaIndex === media.length - 1 ? 0 : activeMediaIndex + 1" class="fixed top-1/2 right-0 mr-2 transform -translate-y-1/2 bg-black bg-opacity-50 p-2"
                x-tooltip="{
                    content: '{{__('messages.t_tooltip_next')}}',
                    theme: $store.theme,
                }"
                aria-label="{{__('messages.t_aria_label_next')}}">
                <x-heroicon-o-arrow-right class="w-6 h-6 text-white" aria-hidden="true" />
            </button>

            <!-- Close Button -->
            <button @click="showModal = false" class="fixed flex items-center justify-center top-0 right-0 mt-2 mr-2 w-10 h-10 text-white bg-black bg-opacity-50 rounded-full"
            x-tooltip="{
                content: '{{__('messages.t_tooltip_close')}}',
                theme: $store.theme,
            }"
            aria-label="{{__('messages.t_aria_label_close')}}">
                <x-heroicon-o-x-mark class="w-6 h-6" aria-hidden="true" />
            </button>
        </div>
    </div>

</div>
