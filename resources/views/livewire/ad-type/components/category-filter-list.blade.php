<section class="mb-4 flex relative" x-data="{
    scroll: null,
    isScrollStart: true,
    isScrollEnd: false,
    queryString: window.location.search,
    interval: null,
    init() {
        this.scroll = this.$refs.childCategoryContainer;
        // Wait until the scroll container has a valid width and height
        if (this.scroll.clientWidth > 0 && this.scroll.clientHeight > 0) {
            this.updateScrollButtons();
        } else {
            // Recheck until dimensions are valid using requestAnimationFrame
            requestAnimationFrame(() => this.init());
        }

        this.scroll.addEventListener('scroll', () => this.updateScrollButtons());
        this.startWatching();
    },
    updateScrollButtons() {
        this.isScrollStart = this.scroll.scrollLeft <= 0;
        this.isScrollEnd = this.scroll.scrollLeft + this.scroll.clientWidth >= this.scroll.scrollWidth;
    },
    scrollLeft() {
        this.scroll.scrollBy({ left: -400, behavior: 'smooth' });
    },
    scrollRight() {
        this.scroll.scrollBy({ left: 400, behavior: 'smooth' });
    },
    startWatching() {
        this.interval = setInterval(() => {
            if (this.queryString !== window.location.search) {
                this.queryString = window.location.search
            }
        }, 100);
    },
}">
    <button type="button" x-show="!isScrollStart" aria-label="{{__('messages.t_aria_label_previous')}}"
        class="border classic:border-black border-grey-600 dark:border-white/10 bg-white dark:bg-gray-800  rounded-full p-0.5 absolute top-1/2 z-10 left-0 transform -translate-y-1/2 flex items-center gap-x-2">
        <x-heroicon-o-chevron-left aria-hidden="true" x-tooltip="{
                    content: '{{__('messages.t_tooltip_previous')}}',
                    theme: $store.theme,
                }" @click="scrollLeft" class="dark:text-white h-6 w-6 text-black p-0.5 cursor-pointer " />
    </button>

    <div class="flex pr-3 overflow-x-auto flex-grow md:gap-2 gap-1 no-scrollbar max-md:overflow-x-auto relative"
        x-ref="childCategoryContainer" >
        <button  type="button" class="border classic:border-black border-grey-600 dark:border-white/10 {{is_null($childCategorySlug)?'bg-secondary-600 text-white':' bg-white dark:bg-gray-800'}} px-[14px] text-sm py-1  rounded-lg min-w-fit"
            wire:click="clearChildCategory" >
                {{__('messages.t_all_child_categories')}}
        </button>
        @foreach ($childCategories as $childCategory)
        <a draggable="false" x-bind:href="'{{$this->getChildCategoryUrl($childCategory->id)}}'"  class="border classic:border-black border-grey-600 dark:border-white/10 {{$childCategory->slug===$childCategorySlug?'bg-secondary-600 text-white':' bg-white dark:bg-gray-800'}} px-[14px] text-sm py-1  rounded-lg min-w-fit"
        wire:click="selectChildCategory({{$childCategory->id}})" >
            {{$childCategory->name}}
        </a>
        @endforeach
    </div>

    <button type="button" x-show="!isScrollEnd" aria-label="{{__('messages.t_aria_label_next')}}"
        class="border classic:border-black border-grey-600 dark:border-white/10 bg-white dark:bg-gray-800  rounded-full p-0.5  absolute top-1/2 z-10 right-0 transform -translate-y-1/2 flex items-center gap-x-2">
        <x-heroicon-o-chevron-right aria-hidden="true" x-tooltip="{
        content: '{{__('messages.t_tooltip_next')}}',
        theme: $store.theme,
        }" @click="scrollRight" class="h-6 w-6 dark:text-white  text-black  py-0.5 cursor-pointer " />
    </button>

</section>
@script
<script>
    const scrollContainer = document.querySelector('[x-ref="childCategoryContainer"]');
            let isDragging = false;
            let startX, scrollLeft;

            // Mouse Down (Start Dragging)
            scrollContainer.addEventListener('mousedown', (e) => {
                isDragging = true;
                scrollContainer.classList.add('dragging');
                startX = e.pageX - scrollContainer.offsetLeft;
                scrollLeft = scrollContainer.scrollLeft;
            });

            // Mouse Move (Dragging)
            scrollContainer.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - scrollContainer.offsetLeft;
                const walk = (x - startX) * 2; // Adjust scroll speed
                scrollContainer.scrollLeft = scrollLeft - walk;
            });

            // Mouse Up / Leave (Stop Dragging)
            scrollContainer.addEventListener('mouseup', () => {
                isDragging = false;
                scrollContainer.classList.remove('dragging');
            });

            scrollContainer.addEventListener('mouseleave', () => {
                isDragging = false;
                scrollContainer.classList.remove('dragging');
            });

            // Optional: Add touch support for mobile
            scrollContainer.addEventListener('touchstart', (e) => {
                startX = e.touches[0].pageX - scrollContainer.offsetLeft;
                scrollLeft = scrollContainer.scrollLeft;
            });

            scrollContainer.addEventListener('touchmove', (e) => {
                const x = e.touches[0].pageX - scrollContainer.offsetLeft;
                const walk = (x - startX) * 2; // Adjust scroll speed
                scrollContainer.scrollLeft = scrollLeft - walk;
            });
</script>
@endscript
