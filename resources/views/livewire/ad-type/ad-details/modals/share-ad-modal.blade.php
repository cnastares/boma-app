<x-modal.index id="share-ad" alignment="start" width="4xl">

    {{-- Header --}}
    <x-slot name="heading">{{ __('messages.t_share_this_ad') }}</x-slot>

    {{-- Content --}}
    <div>
        <div class="items-center justify-center md:flex md:space-y-0 space-y-4">

            {{-- Facebook --}}
            <div class="grid items-center justify-center mx-4">
                <a href="https://www.facebook.com/share.php?u={{ url('ad', $this->ad->slug) }}&t={{ $this->ad->title }}"
                    target="_blank">
                 <span class="grid justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#3b5998]  mx-auto">
                    <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%"
                        xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                        xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <path
                            d="M374.244,285.825l14.105,-91.961l-88.233,0l0,-59.677c0,-25.159 12.325,-49.682 51.845,-49.682l40.116,0l0,-78.291c0,0 -36.407,-6.214 -71.213,-6.214c-72.67,0 -120.165,44.042 -120.165,123.775l0,70.089l-80.777,0l0,91.961l80.777,0l0,222.31c16.197,2.541 32.798,3.865 49.709,3.865c16.911,0 33.511,-1.324 49.708,-3.865l0,-222.31l74.128,0Z" />
                    </svg>
                </span>
                <span
                class="inline-block uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_facebook') }}</span>
            </a>
            </div>

            {{-- Twitter --}}
            <div class="grid items-center justify-center mx-4">
                <a href="https://twitter.com/intent/tweet?text={{ $this->ad->title }}%20-%20{{ url('ad', $this->ad->slug) }}%20"
                    target="_blank">
                <span
                    class="grid justify-center items-center h-12 w-12 border border-transparent rounded-full  mx-auto">
                    <svg width="48px" height="48px" viewBox="0 0 30 30" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect width="30" height="30" rx="15" fill="#3F3F46" />
                        <path d="M19.7447 7.54297H22.2748L16.7473 13.8605L23.25
                    22.4574H18.1584L14.1705 17.2435L9.60746 22.4574H7.07582L12.9881 15.7L6.75
                    7.54297H11.9708L15.5755 12.3087L19.7447 7.54297ZM18.8567 20.943H20.2587L11.209
                    8.97782H9.7046L18.8567 20.943Z" fill="white" />
                    </svg>
                </span>
                <span
                class="inline-block uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_twitter') }}</span>
            </a>
            </div>

            {{-- Linkedin --}}
            <div class="grid items-center justify-center mx-4">
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url('ad', $this->ad->slug) }}&title={{ $this->ad->title }}&summary={{ $this->ad->title }}"
                    target="_blank" >
                  <span  class="grid justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#0a66c2] ] mx-auto">
                    <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%"
                        xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                        xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <path
                            d="M473.305,-1.353c20.88,0 37.885,16.533 37.885,36.926l0,438.251c0,20.393 -17.005,36.954 -37.885,36.954l-436.459,0c-20.839,0 -37.773,-16.561 -37.773,-36.954l0,-438.251c0,-20.393 16.934,-36.926 37.773,-36.926l436.459,0Zm-37.829,436.389l0,-134.034c0,-65.822 -14.212,-116.427 -91.12,-116.427c-36.955,0 -61.739,20.263 -71.867,39.476l-1.04,0l0,-33.411l-72.811,0l0,244.396l75.866,0l0,-120.878c0,-31.883 6.031,-62.773 45.554,-62.773c38.981,0 39.468,36.461 39.468,64.802l0,118.849l75.95,0Zm-284.489,-244.396l-76.034,0l0,244.396l76.034,0l0,-244.396Zm-37.997,-121.489c-24.395,0 -44.066,19.735 -44.066,44.047c0,24.318 19.671,44.052 44.066,44.052c24.299,0 44.026,-19.734 44.026,-44.052c0,-24.312 -19.727,-44.047 -44.026,-44.047Z"
                            style="fill-rule:nonzero;" />
                    </svg>
                </span>
                <span
                class="inline-block uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_linkedin') }}</span>
            </a>
            </div>

            {{-- Whatsapp --}}
            <div class="grid items-center justify-center mx-4">
                <a href="https://api.whatsapp.com/send?text={{ $this->ad->title }}%20{{ url('ad', $this->ad->slug) }}"
                    target="_blank" >
                 <div class=" grid justify-center items-center h-12 w-12 border border-transparent rounded-full bg-[#25d366] ] mx-auto">
                    <svg class="h-5 w-5 fill-white" version="1.1" viewBox="0 0 512 512" width="100%"
                        xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                        xmlns:serif="http://www.serif.com/" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <path
                            d="M373.295,307.064c-6.37,-3.188 -37.687,-18.596 -43.526,-20.724c-5.838,-2.126 -10.084,-3.187 -14.331,3.188c-4.246,6.376 -16.454,20.725 -20.17,24.976c-3.715,4.251 -7.431,4.785 -13.8,1.594c-6.37,-3.187 -26.895,-9.913 -51.225,-31.616c-18.935,-16.89 -31.72,-37.749 -35.435,-44.126c-3.716,-6.377 -0.397,-9.824 2.792,-13c2.867,-2.854 6.371,-7.44 9.555,-11.16c3.186,-3.718 4.247,-6.377 6.37,-10.626c2.123,-4.252 1.062,-7.971 -0.532,-11.159c-1.591,-3.188 -14.33,-34.542 -19.638,-47.298c-5.171,-12.419 -10.422,-10.737 -14.332,-10.934c-3.711,-0.184 -7.963,-0.223 -12.208,-0.223c-4.246,0 -11.148,1.594 -16.987,7.969c-5.838,6.377 -22.293,21.789 -22.293,53.14c0,31.355 22.824,61.642 26.009,65.894c3.185,4.252 44.916,68.59 108.816,96.181c15.196,6.564 27.062,10.483 36.312,13.418c15.259,4.849 29.145,4.165 40.121,2.524c12.238,-1.827 37.686,-15.408 42.995,-30.286c5.307,-14.882 5.307,-27.635 3.715,-30.292c-1.592,-2.657 -5.838,-4.251 -12.208,-7.44m-116.224,158.693l-0.086,0c-38.022,-0.015 -75.313,-10.23 -107.845,-29.535l-7.738,-4.592l-80.194,21.037l21.405,-78.19l-5.037,-8.017c-21.211,-33.735 -32.414,-72.726 -32.397,-112.763c0.047,-116.825 95.1,-211.87 211.976,-211.87c56.595,0.019 109.795,22.088 149.801,62.139c40.005,40.05 62.023,93.286 62.001,149.902c-0.048,116.834 -95.1,211.889 -211.886,211.889m180.332,-392.224c-48.131,-48.186 -112.138,-74.735 -180.335,-74.763c-140.514,0 -254.875,114.354 -254.932,254.911c-0.018,44.932 11.72,88.786 34.03,127.448l-36.166,132.102l135.141,-35.45c37.236,20.31 79.159,31.015 121.826,31.029l0.105,0c140.499,0 254.87,-114.366 254.928,-254.925c0.026,-68.117 -26.467,-132.166 -74.597,-180.352"
                            id="WhatsApp-Logo" />
                    </svg>
                </div>
                <span
                class="inline-block uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_whatsapp') }}</span>
            </a>
            </div>

            {{-- Copy link --}}
            <div class="grid items-center justify-center mx-4">
                <button type="button" x-on:click="copy" aria-label="{{__('messages.t_aria_label_copy_link')}}"
                    class="inline-flex justify-center items-center h-12 w-12 border border-transparent rounded-full bg-gray-400 ] mx-auto">
                    <svg aria-hidden="true" class="h-5 w-5 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <title />
                        <path
                            d="M17.3,13.35a1,1,0,0,1-.7-.29,1,1,0,0,1,0-1.41l2.12-2.12a2,2,0,0,0,0-2.83L17.3,5.28a2.06,2.06,0,0,0-2.83,0L12.35,7.4A1,1,0,0,1,10.94,6l2.12-2.12a4.1,4.1,0,0,1,5.66,0l1.41,1.41a4,4,0,0,1,0,5.66L18,13.06A1,1,0,0,1,17.3,13.35Z" />
                        <path
                            d="M8.11,21.3a4,4,0,0,1-2.83-1.17L3.87,18.72a4,4,0,0,1,0-5.66L6,10.94A1,1,0,0,1,7.4,12.35L5.28,14.47a2,2,0,0,0,0,2.83L6.7,18.72a2.06,2.06,0,0,0,2.83,0l2.12-2.12A1,1,0,1,1,13.06,18l-2.12,2.12A4,4,0,0,1,8.11,21.3Z" />
                        <path
                            d="M8.82,16.18a1,1,0,0,1-.71-.29,1,1,0,0,1,0-1.42l6.37-6.36a1,1,0,0,1,1.41,0,1,1,0,0,1,0,1.42L9.52,15.89A1,1,0,0,1,8.82,16.18Z" />
                    </svg>
                </button>
                <template x-if="!isCopied">
                    <span
                        class="uppercase font-normal text-xs text-gray-500 mt-4 tracking-widest">{{ __('messages.t_copy_link') }}</span>
                </template>
                <template x-if="isCopied">
                    <span
                        class="uppercase font-normal text-xs text-green-500 mt-4 tracking-widest">{{ __('messages.t_copied') }}</span>
                </template>
            </div>
        </div>
    </div>
</x-modal.index>
