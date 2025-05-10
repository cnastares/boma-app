<div>
    <!-- Skip links -->
    @include('components.skip-links',['links'=>[
        'main-content'=> __('messages.t_skip_to_main_content')
    ]])
    <livewire:layout.header isMobileHidden lazy />
    <x-page-header title="{{ __('messages.t_modification_requests') }}" isMobileHidden :$referrer />

    <!-- Main content -->
    <main id="main-content" class="sticky-scroll-margin container mx-auto px-4 py-10 ">
        <h1 class="md:text-2xl text-xl font-semibold mb-2 hidden md:block">
            {{ __('messages.t_modification_requests') }}
        </h1>
        <div>
            {{ $this->table }}
        </div>
    </main>
</div>
