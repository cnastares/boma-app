<div x-data="{
    slides: {{json_encode($data)}},
    currentSlideIndex: 1,
    previous() {
        if (this.currentSlideIndex > 1) {
            this.currentSlideIndex = this.currentSlideIndex - 1
        } else {
            // If it's the first slide, go to the last slide
            this.currentSlideIndex = this.slides.length
        }
    },
    next() {
        if (this.currentSlideIndex < this.slides.length) {
            this.currentSlideIndex = this.currentSlideIndex + 1
        } else {
            // If it's the last slide, go to the first slide
            this.currentSlideIndex = 1
        }
    },
    canDisplayButtons: {{count($data) > 1 ? 'true' : 'false'}},
}" class="relative  overflow-x-auto overflow-y-hidden h-48 rounded-lg max-w-full" style="width: {{$categoryAdSettings->image_width}}px; height: {{$categoryAdSettings->image_height}}px;">

<!-- previous button -->
    <button aria-label="{{__('messages.t_aria_label_previous')}}" x-show="canDisplayButtons" x-cloak type="button" class="absolute left-3 top-1/2 z-20 flex rounded-full -translate-y-1/2 items-center justify-center bg-white/40 p-1 text-slate-700 transition hover:bg-white/60 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 active:outline-offset-0 dark:bg-slate-900/40 dark:text-slate-300 dark:hover:bg-slate-900/60 " x-on:click="previous()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="3" class="size-5 md:size-6 pr-0.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
    </button>

    <!-- next button -->
    <button aria-label="{{__('messages.t_aria_label_next')}}" x-show="canDisplayButtons" x-cloak type="button" class="absolute right-3 top-1/2 z-20 flex rounded-full -translate-y-1/2 items-center justify-center bg-white/40 p-1 text-slate-700 transition hover:bg-white/60 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 active:outline-offset-0 dark:bg-slate-900/40 dark:text-slate-300 dark:hover:bg-slate-900/60 "  x-on:click="next()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="3" class="size-5 md:size-6 pl-0.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>

    <!-- slides -->
    <!-- Change min-h-[50svh] to your preferred height size -->
    <div class="relative min-h-[50svh] w-full">
        <template x-for="(slide, index) in slides">
            <div x-show="currentSlideIndex == index + 1" class="absolute inset-0" x-transition.opacity.duration.1000ms>
                <img class="absolute w-full h-full  inset-0 object-fill text-slate-700 dark:text-slate-300" x-bind:src="slide.image" x-bind:alt="slide.alt ? slide.alt : '{{__('messages.t_banner')}}'" x-on:click="slide.link ? window.open(slide.link, '_blank'): ''" x-bind:title="slide.link" />
            </div>
        </template>
    </div>

    <!-- indicators -->
    <div x-show="canDisplayButtons" x-cloak class="absolute rounded-xl bottom-3 md:bottom-5 left-1/2 z-20 flex -translate-x-1/2 gap-4 md:gap-3 bg-white/75 px-1.5 py-1 md:px-2 dark:bg-slate-900/75" role="group" aria-label="slides" >
        <template x-for="(slide, index) in slides">
            <button type="button" class="size-2 cursor-pointer rounded-full transition bg-slate-700 dark:bg-slate-300" x-on:click="currentSlideIndex = index + 1" x-bind:class="[currentSlideIndex === index + 1 ? 'bg-slate-700 dark:bg-slate-300' : 'bg-slate-700/50 dark:bg-slate-300/50']" x-bind:aria-label="'slide ' + (index + 1)"></button>
        </template>
    </div>
</div>
