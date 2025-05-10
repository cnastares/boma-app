<div class="messenger-chat-design">
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
            /* WebKit */
        }

        /* Add this CSS to make the textarea non-resizable */
        .no-resize {
            resize: none;
        }
    </style>
    {{-- !is_vehicle_rental_active() ?? false --}}
    @if (false)
        <div class=" border border-[#B0B0B0] dark:border-white/10 rounded-ss-xl rounded-se-xl relative">
            <div @click="arrow = !arrow" class=" absolute -top-4 w-full">
                <div
                    class=" border border-b-0 rounded-t-full border-[#B0B0B0] dark:border-white/10 bg-white dark:bg-black flex mx-auto justify-center items-center w-8 h-4 pt-2 cursor-pointer">
                    <svg :class="arrow == true ? ' w-3.5 h-3.5 stroke-black dark:stroke-[#FFFFFF1A]' :
                        ' w-3.5 h-3.5 rotate-180 stroke-black dark:stroke-[#FFFFFF1A]'"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="10" viewBox="0 0 16 10" fill="none">
                        <path d="M14.875 1.5625L8 8.4375L1.125 1.5625" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
            <div class=" grid grid-cols-2">
                <div @click="chatNav = 1"
                    class=" flex justify-center items-center gap-x-2 relative py-3 cursor-pointer">
                    <svg :class="chatNav == '1' ? ' stroke-[#FDAE4B]' : ' stroke-[#A2A2A2]'"
                        xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                        fill="none">
                        <path
                            d="M16.875 7.5925C17.6117 7.82917 18.125 8.5325 18.125 9.34V12.9117C18.125 13.8583 17.4192 14.6617 16.475 14.7392C16.1917 14.7617 15.9083 14.7825 15.625 14.7992V17.375L13.125 14.875C11.9967 14.875 10.88 14.8292 9.775 14.7392C9.53444 14.7197 9.30044 14.6511 9.0875 14.5375M16.875 7.5925C16.7462 7.55106 16.6131 7.52449 16.4783 7.51334C14.2466 7.32807 12.0034 7.32807 9.77167 7.51334C8.82917 7.59167 8.125 8.39417 8.125 9.34V12.9117C8.125 13.6092 8.50833 14.2283 9.0875 14.5375M16.875 7.5925V6.03083C16.875 4.68 15.915 3.50917 14.575 3.335C12.8507 3.11149 11.1137 2.99959 9.375 3C7.6125 3 5.87667 3.11417 4.175 3.335C2.835 3.50917 1.875 4.68 1.875 6.03083V11.2192C1.875 12.57 2.835 13.7408 4.175 13.915C4.65583 13.9775 5.13917 14.0317 5.625 14.0767V18L9.0875 14.5375"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <h4 :class="chatNav == '1' ? ' text-[#FDAE4B] font-medium' : ' text-[#A2A2A2] font-medium'">
                        Chat
                    </h4>
                    <div
                        :class="chatNav == '1' ? ' block border-b-2 border-[#FDAE4B] absolute bottom-0 w-full' :
                            ' hidden'">
                    </div>
                </div>
                <div @click="chatNav = 2"
                    class=" flex justify-center items-center gap-x-2 relative py-3 cursor-pointer">
                    <svg :class="chatNav == '2' ? ' stroke-[#FDAE4B]' : ' stroke-[#A2A2A2]'"
                        xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                        fill="none">
                        <path
                            d="M14.2863 4.0715V11.2144M14.2863 4.0715L12.1434 1.21436M14.2863 4.0715L16.4291 1.21436M0.714844 16.2144L4.20627 19.1229C4.71971 19.5504 5.36673 19.7845 6.03484 19.7844H15.2377C15.8948 19.7844 16.4291 19.2515 16.4291 18.5944C16.4291 17.2801 15.3634 16.2129 14.0477 16.2129H7.64913M19.2863 4.0715H9.28627V11.2144H19.2863V4.0715Z"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M5.00056 14.7858L6.07199 15.8572C6.35615 16.1414 6.74155 16.301 7.14342 16.301C7.54528 16.301 7.93068 16.1414 8.21484 15.8572C8.499 15.5731 8.65864 15.1876 8.65864 14.7858C8.65864 14.3839 8.499 13.9985 8.21484 13.7144L6.55199 12.0501C6.28628 11.7847 5.97086 11.5742 5.62375 11.4308C5.27665 11.2874 4.90469 11.2139 4.52913 11.2144H0.714844"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <h4 :class="chatNav == '2' ? ' text-[#FDAE4B] font-medium' : ' text-[#A2A2A2] font-medium'">
                        Make a
                        Offer</h4>
                    <div
                        :class="chatNav == '2' ? ' block border-b-2 border-[#FDAE4B] absolute bottom-0 w-full' :
                            ' hidden'">
                    </div>
                </div>
            </div>
        </div>
        <div x-show="arrow" x-collapse>
            <div :class="chatNav == '1' ? ' flex items-center gap-x-3 px-5 pt-5 overflow-x-auto w-full hide-scrollbar' : ' hidden'"
                x-data="{
                    chatBg: null,
                }">
                <button type="button" @click="customMessage(`{{ __('messages.t_is_item_available_query') }}`);"
                    :class="chatBg == '1' ? ' text-white bg-[#FDAE4B] dark:text-white dark:bg-black' :
                        ' text-[#71717A] bg-white dark:text-white dark:bg-black'"
                    class="text-sm border border-[#71717A] dark:border-white/10 rounded-md px-4 py-1 min-w-fit">{{ __('messages.t_is_item_available_query') }}</button>
                <button type="button" @click="customMessage(`{{ __('messages.t_meetup_availability_query') }}`);"
                    :class="chatBg == '2' ? ' text-white bg-[#FDAE4B] dark:text-white dark:bg-black' :
                        ' text-[#71717A] bg-white dark:text-white dark:bg-black'"
                    class="text-sm text-[#71717A] border border-[#71717A] dark:border-white/10 rounded-md px-4 py-1 min-w-fit">{{ __('messages.t_meetup_availability_query') }}</button>
                <button type="button" @click="customMessage(`{{ __('messages.t_price_negotiation_query') }}`);"
                    :class="chatBg == '3' ? ' text-white bg-[#FDAE4B] dark:text-white dark:bg-black' :
                        ' text-[#71717A] bg-white dark:text-white dark:bg-black'"
                    class="text-sm text-[#71717A] border border-[#71717A] dark:border-white/10 rounded-md px-4 py-1 min-w-fit">{{ __('messages.t_price_negotiation_query') }}</button>
            </div>
            <div :class="chatNav == '2' ? ' flex items-center gap-x-3 px-5 pt-5 overflow-x-auto w-full hide-scrollbar' : ' hidden'"
                x-data="{
                    chatBg: null,
                }">
                @foreach (getOfferSuggestions(200) as $value)
                    <button type="button" @click="customMessage(`{{ $value }}`, 1);"
                        :class="chatBg == '1' ? ' text-white bg-[#FDAE4B] dark:text-white dark:bg-black dark:border-white/10' :
                            ' text-[#71717A] bg-white dark:text-white dark:bg-black dark:border-white/10'"
                        class="text-sm border border-[#71717A] dark:border-white/10 rounded-md px-4 py-1 min-w-fit">{{ formatPriceWithCurrency($value) }}</button>
                @endforeach

                {{-- your offer section  --}}
                {{-- <div class=" pb-5 w-full">
                                <h2 class=" text-xl text-center font-semibold">Your Offer: $140</h2>
                                <p class=" text-center pt-2.5">Waiting for seller response</p>
                            </div> --}}

                {{-- seller offer section  --}}
                {{-- <div class=" pb-5 w-full">
                            <h2 class=" text-xl text-center font-semibold">Seller Offer: $150</h2>
                            <div class=" flex items-center gap-x-3 pt-8">
                                <x-filament::button @click="$dispatch('open-modal', { id: 'offer-popup' })"
                                    color="gray" class=" !border !border-black w-full">
                                    Make a new offer
                                </x-filament::button>
                                <x-filament::button class=" whitespace-nowrap !bg-black px-5 w-full">
                                    Let’s go ahead
                                </x-filament::button>
                            </div>
                        </div> --}}

                {{-- offer accept sectiion  --}}
                {{-- <div class=" pb-5 w-full">
                            <h2 class=" text-xl text-center font-semibold">Offer Accepted: $150</h2>
                            <div class=" flex justify-center pt-8">
                                <x-filament::button class=" whitespace-nowrap !bg-black px-8">
                                Ask Contact
                            </x-filament::button>
                            </div>
                        </div> --}}

            </div>
        </div>
    @endif
    <div class="messenger-sendCard dark:bg-black" x-data="window.chatMessageHandler" x-ref="chatHandler" x-init="initialize">
        {{-- Emojis box --}}
        @if ($liveChatSettings->enable_emojis)
            <div id="emojis-box-container" style="display: none"></div>
        @endif

        {{-- Send message form --}}
        <form id="message-form" method="POST" action="{{ route('send.message') }}" enctype="multipart/form-data"
            class="items-center">

            @csrf

            {{-- Emoji container --}}
            @if ($liveChatSettings->enable_emojis)
                <div x-show="!isRecording && !audioBlob">
                    <div id="emojis-box-trigger">
                        <svg class="emoji-box-trigger-event action-svg w-5 h-5 !text-slate-500 hover:!text-slate-500 dark:!text-slate-200 dark:hover:!text-white focus:outline-none"
                            data-tooltip-target="chat-tooltip-btn-insert-emoji" stroke="currentColor" fill="none"
                            stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                        </svg>
                    </div>
                    <x-forms.tooltip id="chat-tooltip-btn-insert-emoji" :text="__('messages.t_insert_emoji')" />
                </div>
            @endif


            {{-- Attach a file --}}
            @if ($liveChatSettings->enable_uploading_attachments)
                <div x-show="!isRecording && !audioBlob">
                    <label id="attachment-file-btn">
                        <svg class="action-svg w-5 h-5 !text-slate-500 hover:!text-slate-500 dark:!text-slate-200 dark:hover:!text-white focus:outline-none"
                            data-tooltip-target="chat-tooltip-btn-insert-file" stroke="currentColor" fill="none"
                            stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48">
                            </path>
                        </svg>
                        <input disabled='disabled' type="file" class="upload-attachment" name="file" />
                    </label>
                    <x-forms.tooltip id="chat-tooltip-btn-insert-file" :text="__('messages.t_attach_a_file')" />
                </div>
            @endif

            {{-- Message content --}}
            <div class="w-full md:px-3 flex items-center justify-center">
                <textarea x-show="!isRecording && !audioBlob " x-model="message" id="live-chat-message-textarea" readonly='readonly'
                    name="message" class="m-send app-scroll focus:outline-none focus:ring-2 focus:ring-primary-600  dark:text-white dark:placeholder:text-zinc-400 placeholder:whitespace-nowrap no-resize hide-scrollbar"
                    placeholder="@lang('messages.t_type_ur_message_here')"></textarea>
            </div>

            @if ($liveChatSettings->enable_audio_recording)
                <div class="flex gap-x-2 items-center">

                    <div class="audio-controls flex items-center gap-x-2">
                        <!-- Delete Button -->
                        <button type="button" x-show="audioBlob" @click="deleteAudio" aria-label="{{__('messages.t_aria_label_delete_audio')}}"
                            class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 focus:outline-none">
                            <x-heroicon-o-trash class="h-6 w-6 text-gray-600" aria-hidden="true" />
                        </button>
                        <!-- Timer for recording -->
                        <div x-text="timer" class="timer w-12" x-show="isRecording || audioBlob"></div>

                        <!-- Play Button -->
                        <button type="button" x-show="audioBlob" @click="playAudio" aria-label="{{__('messages.t_aria_label_play_audio')}}"
                            class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 focus:outline-none">
                            <x-heroicon-o-play-circle class="h-7 w-7 text-gray-600" aria-hidden="true" />
                        </button>
                    </div>

                    <div class="flex items-center justify-center">
                        <!-- Record Button -->
                        <button type="button" @click="startRecording" x-show="!isRecording && !audioBlob" aria-label="{{__('messages.t_aria_label_record_audio')}}"
                            class="btn-start-recording !p-1 rounded-full  hover:bg-gray-300 text-white focus:outline-none">
                            {{-- <x-heroicon-s-microphone class="h-6 w-6 text-gray-600" /> --}}
                            <svg class="h-6 w-6 dark:fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor" aria-hidden="true" data-slot="icon">
                                <path d="M8.25 4.5a3.75 3.75 0 1 1 7.5 0v8.25a3.75 3.75 0 1 1-7.5 0V4.5Z"></path>
                                <path
                                    d="M6 10.5a.75.75 0 0 1 .75.75v1.5a5.25 5.25 0 1 0 10.5 0v-1.5a.75.75 0 0 1 1.5 0v1.5a6.751 6.751 0 0 1-6 6.709v2.291h3a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1 0-1.5h3v-2.291a6.751 6.751 0 0 1-6-6.709v-1.5A.75.75 0 0 1 6 10.5Z">
                                </path>
                            </svg>
                        </button>
                        <!-- Stop Button -->
                        <button type="button" @click="stopRecording" x-show="isRecording" aria-label="{{__('messages.t_aria_label_stop_recording')}}"
                            class="btn-stop-recording !p-1 rounded-full bg-gray-200 hover:bg-gray-300 focus:outline-none">
                            <x-heroicon-o-stop-circle class="h-7 w-7 text-gray-600" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            @endif

            {{-- Send --}}
            <button type="submit" aria-label="{{__('messages.t_aria_label_send_message')}}" disabled='disabled' class="focus:outline">
                <svg x-show="!isRecording" aria-hidden="true"
                    class="action-svg !h-6 !w-6 !text-primary-600 focus:outline-none rtl:-rotate-90 rtl:active:!-rotate-90"
                    stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path
                            d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z">
                        </path>
                    </g>
                </svg>
            </button>

        </form>

    </div>
    <script>
        function chatMessageHandler() {

            return {
                isRecording: false,
                audioBlob: null,
                mediaRecorder: null,
                stream: null,
                timer: '00:00',
                interval: null,

                startRecording() {
                    navigator.mediaDevices.getUserMedia({
                            audio: true
                        })
                        .then(stream => {
                            this.stream = stream;
                            this.mediaRecorder = new MediaRecorder(stream);
                            const audioChunks = [];
                            this.mediaRecorder.ondataavailable = event => {
                                audioChunks.push(event.data);
                            };
                            this.mediaRecorder.onstop = () => {
                                this.audioBlob = new Blob(audioChunks);
                                clearInterval(this.interval);
                            };
                            this.mediaRecorder.start();
                            this.isRecording = true;
                            this.startTimer();
                        }).catch(error => {
                            console.error(error);
                        });
                },

                stopRecording() {
                    this.mediaRecorder.stop();
                    this.stream.getTracks().forEach(track => track.stop());
                    this.isRecording = false;
                },

                playAudio() {
                    const audioUrl = URL.createObjectURL(this.audioBlob);
                    const audio = new Audio(audioUrl);
                    audio.play();
                },


                startTimer() {
                    let seconds = 0;
                    this.interval = setInterval(() => {
                        seconds++;
                        this.timer = new Date(seconds * 1000).toISOString().substr(14, 5);
                    }, 1000);
                },
                deleteAudio() {
                    this.audioBlob = null;
                    this.timer = '00:00';
                    clearInterval(this.interval);
                },
                clearData() {
                    this.message = null;
                    this.audioBlob = null;
                    this.timer = '00:00';
                    clearInterval(this.interval);
                    // Log to verify it's being called
                    console.log('Data cleared');
                },
                message: null,

                // Initialize emojis box
                @if ($liveChatSettings->enable_emojis)
                    emojis() {

                            // Access this
                            const _this = this;

                            // Get emoji box container
                            const emoji_container = $("#emojis-box-container");

                            // Set options
                            const options = {
                                set: 'twitter',
                                theme: "light",
                                dynamicWidth: true,
                                previewPosition: 'none',
                                i18n: {
                                    "rtl": {{ config()->get('direction') === 'rtl' ? 1 : 0 }},
                                    "search": "{{ __('messages.t_search') }}",
                                    "search_no_results_1": "{{ __('messages.t_oops') }}",
                                    "search_no_results_2": "{{ __('messages.t_no_results_found') }}",
                                    "pick": "{{ __('messages.t_pick_an_emoji') }}",
                                    "add_custom": "Add custom emoji",
                                    "categories": {
                                        "activity": "{{ __('messages.t_emojis_activity') }}",
                                        "custom": "{{ __('messages.t_emojis_custom') }}",
                                        "flags": "{{ __('messages.t_emojis_flags') }}",
                                        "foods": "{{ __('messages.t_emojis_food_drink') }}",
                                        "frequent": "{{ __('messages.t_emojis_recently_used') }}",
                                        "nature": "{{ __('messages.t_emojis_animals_nature') }}",
                                        "objects": "{{ __('messages.t_emojis_objects') }}",
                                        "people": "{{ __('messages.t_emojis_smileys') }}",
                                        "places": "{{ __('messages.t_emojis_travel') }}",
                                        "search": "{{ __('messages.t_search_results') }}",
                                        "symbols": "{{ __('messages.t_emojis_symbols') }}"
                                    },
                                    "skins": {
                                        "choose": "{{ __('messages.t_choose_default_skin_tone') }}",
                                        "1": "{{ __('messages.t_skin_default') }}",
                                        "2": "{{ __('messages.t_skin_light') }}",
                                        "3": "{{ __('messages.t_skin_medium_light') }}",
                                        "4": "{{ __('messages.t_skin_medium') }}",
                                        "5": "{{ __('messages.t_skin_medium_dark') }}",
                                        "6": "{{ __('messages.t_skin_dark') }}"
                                    }
                                },
                                exceptEmojis: [
                                    'relaxed',
                                    'smiling_face_with_tear',
                                    'face_with_open_eyes_and_hand_over_mouth',
                                    'face_with_peeking_eye',
                                    'saluting_face',
                                    'dotted_line_face',
                                    'face_in_clouds',
                                    'face_exhaling',
                                    'face_with_spiral_eyes',
                                    'disguised_face',
                                    'face_with_diagonal_mouth',
                                    'face_holding_back_tears',
                                    'rightwards_hand',
                                    'leftwards_hand',
                                    'palm_down_hand',
                                    'palm_up_hand',
                                    'pinched_fingers',
                                    'hand_with_index_finger_and_thumb_crossed',
                                    'index_pointing_at_the_viewer',
                                    'heart_hands',
                                    'anatomical_heart',
                                    'lungs',
                                    'biting_lip',
                                    'man_with_beard',
                                    'woman_with_beard',
                                    'red_haired_person',
                                    'curly_haired_person',
                                    'white_haired_person',
                                    'bald_person',
                                    'health_worker',
                                    'student',
                                    'teacher',
                                    'judge',
                                    'farmer',
                                    'cook',
                                    'mechanic',
                                    'factory_worker',
                                    'office_worker',
                                    'scientist',
                                    'technologist',
                                    'singer',
                                    'artist',
                                    'pilot',
                                    'astronaut',
                                    'firefighter',
                                    'ninja',
                                    'person_with_crown',
                                    'man_in_tuxedo',
                                    'woman_in_tuxedo',
                                    'man_with_veil',
                                    'woman_with_veil',
                                    'pregnant_man',
                                    'pregnant_person',
                                    'woman_feeding_baby',
                                    'man_feeding_baby',
                                    'person_feeding_baby',
                                    'mx_claus',
                                    'troll',
                                    'person_with_probing_cane',
                                    'person_in_motorized_wheelchair',
                                    'person_in_manual_wheelchair',
                                    'people_hugging',
                                    'heart_on_fire',
                                    'mending_heart',
                                    'black_cat',
                                    'bison',
                                    'mammoth',
                                    'beaver',
                                    'polar_bear',
                                    'dodo',
                                    'feather',
                                    'seal',
                                    'coral',
                                    'beetle',
                                    'cockroach',
                                    'fly',
                                    'worm',
                                    'lotus',
                                    'potted_plant',
                                    'empty_nest',
                                    'nest_with_eggs',
                                    'blueberries',
                                    'olive',
                                    'bell_pepper',
                                    'beans',
                                    'flatbread',
                                    'tamale',
                                    'fondue',
                                    'teapot',
                                    'pouring_liquid',
                                    'bubble_tea',
                                    'jar',
                                    'magic_wand',
                                    'hamsa',
                                    'pinata',
                                    'mirror_ball',
                                    'nesting_dolls',
                                    'sewing_needle',
                                    'knot',
                                    'rock',
                                    'wood',
                                    'hut',
                                    'playground_slide',
                                    'pickup_truck',
                                    'roller_skate',
                                    'wheel',
                                    'ring_buoy',
                                    'thong_sandal',
                                    'military_helmet',
                                    'accordion',
                                    'long_drum',
                                    'low_battery',
                                    'coin',
                                    'boomerang',
                                    'carpentry_saw',
                                    'screwdriver',
                                    'hook',
                                    'ladder',
                                    'crutch',
                                    'x-ray',
                                    'elevator',
                                    'mirror',
                                    'window',
                                    'plunger',
                                    'mouse_trap',
                                    'bucket',
                                    'bubbles',
                                    'toothbrush',
                                    'headstone',
                                    'placard',
                                    'identification_card',
                                    'heavy_equals_sign',
                                    'transgender_flag'
                                ],
                                onClickOutside(e) {

                                    // Get target
                                    const target = e.target || e.srcElement;

                                    // Check if clicked on emoji button
                                    if (target.classList.contains('emoji-box-trigger-event')) {

                                        // Toggle hidden class
                                        emoji_container.toggle();

                                    } else {

                                        // Hide the box
                                        emoji_container.hide();

                                    }

                                },
                                onEmojiSelect(selection) {

                                    // Insert the emoji
                                    _this.message = _this.message + " " + selection.native;

                                    // Now focus on the textarea
                                    document.getElementById('live-chat-message-textarea').focus();

                                }
                            };

                            // Set new picker
                            const picker = new EmojiMart.Picker(options);

                            // Insert html code
                            document.getElementById('emojis-box-container').appendChild(picker)

                        },
                @endif

                // Initialize
                initialize() {
                    @if ($liveChatSettings->enable_emojis)

                        // Initialize emojis
                        this.emojis();

                        // Listen to open/close emoji box
                        document.getElementById('emojis-box-trigger').addEventListener('click', function() {

                            $('#emojis-box-container').toggleClass('hidden');
                        }, false);
                    @endif

                    // Disable Enter button in message box
                    $("#live-chat-message-textarea").keydown(function(e) {
                        if (e.keyCode == 13 && !e.shiftKey) {
                            e.preventDefault();
                            return false;
                        }
                    });

                    this.$el.addEventListener('clear-data', () => {
                        this.clearData();
                    });

                }

            }
        }
        window.chatMessageHandler = chatMessageHandler();
    </script>


</div>
