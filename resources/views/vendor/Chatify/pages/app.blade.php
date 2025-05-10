@include('Chatify::layouts.headLinks')

<body
    class="bg-gray-50  font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white classic:bg-gray-100 classic:text-black" style="margin-bottom: 0px !important;">
    <script>
        function goBack() {
            window.history.back(); // This will take the user to the previous page in their history
        }
        </script>
    <style>
        .fi-input-wrp > div {
            border-right: 1px solid #D1D5D8;
            color: #71717A;
        }
    </style>
    <main class="messenger">
        {{-- ----------------------Users/Groups lists side---------------------- --}}
        <div class="messenger-listView dark:bg-black ltr:border-r rtl:border-l dark:border-zinc-700">

            {{-- Header and search bar --}}
            <div class="m-header">

                {{-- Back to homepage --}}
                <div class="py-4 px-5 border-b dark:border-zinc-700 dark:bg-black">
                    <div class="relative flex space-x-3 rtl:space-x-reverse group">
                        <button type="button" onclick="goBack()" class="cursor-pointer" aria-label="{{__('messages.t_aria_label_back')}}" >
                            <span
                                class="h-8 w-8 rounded-full bg-black group-hover:bg-black dark:bg-zinc-600 dark:group-hover:bg-slate-200 flex items-center justify-center rtl:-rotate-180 ">
                                <svg class="h-6 w-6 !text-white dark:!text-zinc-400 group-hover:!text-zinc-200 dark:group-hover:!text-zinc-600"
                                    stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <nav role="navigation" aria-label="{{__('messages.t_aria_label_back')}}" class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-slate-500 dark:text-white">
                                    @lang('messages.t_messages')
                                </p>
                            </div>
                            <div class="whitespace-nowrap text-right">
                                <div class="listView-x">
                                    <svg class="!text-slate-500 dark:!text-slate-300 h-3.5 w-3.5 mt-0.5"
                                        stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>


            </div>

            {{-- tabs and lists --}}
            <div class="m-body contacts-container dark:bg-black">
                {{-- Lists [Users/Group] --}}
                {{-- ---------------- [ User Tab ] ---------------- --}}
                <div class="@if ($type == 'user') show @endif messenger-tab users-tab app-scroll"
                    data-view="users">



                    {{-- Contact --}}
                    <div class="listOfContacts py-1" style="width: 100%;height: calc(100% - 200px);position: relative;">
                    </div>

                </div>

                {{-- ---------------- [ Group Tab ] ---------------- --}}
                <div class="@if ($type == 'group') show @endif messenger-tab groups-tab app-scroll"
                    data-view="groups">
                    {{-- items --}}
                    <p style="text-align: center;color:grey;margin-top:30px">
                        <a target="_blank" style="color:{{ $messengerColor }};" href="#">Click here</a> for more
                        info!
                    </p>
                </div>

                {{-- ---------------- [ Search Tab ] ---------------- --}}
                <div class="messenger-tab search-tab app-scroll" data-view="search">
                    <p class="messenger-title !px-5 mb-2">@lang('messages.t_search')</p>
                    <div class="search-records">
                        <p class="message-hint mt-20"><span>@lang('messages.t_type_to_search_in_ur_contacts')</span></p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ----------------------Messaging side---------------------- --}}
        <div class="messenger-messagingView dark:bg-black">

            {{-- header title [conversation name] amd buttons --}}
            <div class="m-header m-header-messaging py-5 border-b dark:border-zinc-700 px-4 dark:bg-black" style="display: none">
                <nav class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">

                    {{-- header back button, avatar and user name --}}
                    <div class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">

                        {{-- Go back --}}
                        <button type="button" class="md:hidden show-listView" aria-label="{{__('messages.t_aria_label_back')}}" >
                            <svg aria-hidden="true" class="h-6 w-6 dark:text-white reflection" stroke="currentColor" fill="currentColor" stroke-width="0"
                                viewBox="0 0 20 20" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        {{-- Avatar --}}
                        <div class="relative">
                            <div class="avatar av-s header-avatar"
                                style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                            </div>
                            <div class="user-avatar">
                            </div>
                        </div>

                        {{-- Username --}}
                        <div>
                            <span
                            class="user-name show-infoSide cursor-pointer dark:!text-white">{{ config('app.name') }}</span>
                            <p class=" text-sm ">
                                <a id="ad-link" href="" target="__blank">{{__('messages.t_view_ad')}}</a>
                            </p>
                        </div>

                    </div>

                </nav>
            </div>

            {{-- Messaging area --}}
            <div class="m-body messages-container app-scroll dark:bg-black">

                {{-- Internet connection --}}
                <div class="internet-connection">

                    {{-- Connected status --}}
                    <span class="ic-connected">@lang('messages.t_chat_connected')</span>

                    {{-- Connecting status --}}
                    <span class="ic-connecting">@lang('messages.t_chat_connecting')</span>

                    {{-- No internet access status --}}
                    <span class="ic-noInternet">@lang('messages.t_chat_no_internet_access')</span>

                </div>

                <div class="messages space-y-5">
                    <p class="center-el font-bold">
                        <span> @lang('messages.t_safety_tips_title')</span>
                    </p>
                    <p class="center-el">
                        <span>@lang('messages.t_safety_tips_content')</span>
                    </p>
                </div>
                {{-- Typing indicator --}}
                <div class="typing-indicator">
                    <div class="message-card typing px-5 !mx-0 !mt-4">
                        <p>
                            <span class="typing-dots">
                                <span class="dot dot-1"></span>
                                <span class="dot dot-2"></span>
                                <span class="dot dot-3"></span>
                            </span>
                        </p>
                    </div>
                </div>

            </div>
            <div class=" bg-white dark:bg-black rounded-ss-xl rounded-se-xl" x-data="{chatNav: 1, arrow: true,}">
            {{-- @if (canReceiveMessage(getReceiverIdFromConversation($id))) --}}
                @include('Chatify::layouts.sendForm')
            {{-- @else --}}
            <div class="hide-sendform" style="display: none">
                <p class="message-hint "><span>@lang('messages.t_seller_reached_interaction_message')</span></p>
            </div>
            {{-- @endif --}}
            </div>
        </div>

        {{-- offer popup  --}}
        <x-filament::modal id="offer-popup" width="3xl" class=" bg-black/40">
            <x-slot name="heading" class=" flex items-center">
                Select reason for not accepting the offer.
            </x-slot>
            <label class=" flex items-center gap-x-3">
                <x-filament::input.radio name="price" />
                <span>My last price is:</span>
            </label>
            <x-filament::input.wrapper class=" w-full border border-[#D1D5D8]">
                <x-slot name="prefix">
                    $
                </x-slot>
                <x-filament::input type="text" placeholder="Type your offer" />
            </x-filament::input.wrapper>
            <label class=" flex items-center gap-x-3">
                <x-filament::input.radio name="price" />
                <span>Give me a better offer</span>
            </label>
            <div class=" flex items-center gap-x-3">
                <x-filament::button class=" whitespace-nowrap !bg-black px-5">
                    Send
                </x-filament::button>
                <x-filament::button @click="$dispatch('close-modal', { id: 'offer-popup' })" color="gray"
                    class=" !border !border-black">
                    Cancel
                </x-filament::button>
            </div>
        </x-filament::modal>

        {{-- ---------------------- Info side ---------------------- --}}
        <div class="border-l messenger-infoView app-scroll " style="display: none">
            {{-- nav actions --}}
            <nav class="py-4">

                {{-- Close --}}
                <a class="cursor-pointer flex justify-end">
                    <svg class="h-6 w-6 text-gray-400 hover:text-gray-600" stroke="currentColor" fill="currentColor"
                        stroke-width="0" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>

            </nav>
            <div class="w-full px-6 flex flex-col space-y-8">
                {!! view('Chatify::layouts.info')->render() !!}
            </div>
        </div>

    </main>
    @include('Chatify::layouts.modals')
    @include('Chatify::layouts.footerLinks')
</body>

</html>
