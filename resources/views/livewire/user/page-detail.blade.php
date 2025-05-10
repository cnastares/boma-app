<div>
    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
        'main-content'=> __('messages.t_skip_to_main_content')
    ]])
    <livewire:layout.header isMobileHidden lazy />
    <main class="sticky-scroll-margin" id="main-content">

    <x-page-header title="{{ $page->title }}" isMobileHidden :$referrer />

    <div class="hidden md:block py-4 md:py-6 bg-gray-200 dark:bg-gray-800 classic:bg-gray-50  classic:border-y border-black">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white ">
                {{ $page->title }}
            </h1>
        </div>
    </div>
    <div class="pt-6 pb-24 md:py-10">
        <div class="mx-auto container  leading-7 px-4">
            <div class="mt-6 prose prose-slate  max-w-none dark:prose-invert">
                {!! $page->content !!}
            </div>
        </div>
    </div>
    </main>

    <livewire:layout.footer />

    <livewire:layout.bottom-navigation />
</div>
