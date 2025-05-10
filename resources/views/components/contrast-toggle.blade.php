<div class="w-fit">
    @php
    $mode=$appearanceSettings->contrast_mode;
    $modes=explode('_',$mode);
    @endphp
    <button @click="theme == '{{$modes[1]}}'?theme='{{$modes[0]}}':theme='{{$modes[1]}}';" type="button"
    x-data x-tooltip="{
        content: '{{__('messages.t_tooltip_theme_switcher')}}',
        theme: $store.theme,
    }"
    role="switch"
    aria-label="{{__('messages.t_aria_label_theme_switcher')}}"
    >

        <div class="dark-mode-toggle-container border-gray-950/5 border classic:border-black"
            x-bind:class="theme == '{{$modes[1]}}'? theme +' active':''">
            <x-icon-light x-show="{{json_encode($modes)}}.includes('light')" class="w-5 h-5 light dark:text-gray-500"
            x-bind:class="'light' == '{{$modes[0]}}'?'mode-1':'mode-2'" aria-hidden="true"
             />
            <x-icon-dark x-show="{{json_encode($modes)}}.includes('dark')" class="w-6 h-6 dark pb-1"
            x-bind:class="'dark' == '{{$modes[0]}}'?'mode-1':'mode-2'" aria-hidden="true"
             />
            <x-heroicon-o-square-3-stack-3d x-show="{{json_encode($modes)}}.includes('classic')" class="w-6 h-6 classic pb-1"
            x-bind:class="'classic' == '{{$modes[0]}}'?'mode-1':'mode-2'" aria-hidden="true"
             />
        </div>
    </button>
    <style>
        .dark-mode-toggle-container {
            width: 62px;
            height: 40px;
            position: relative;
            display: block;
            background: #ebebeb;
            border-radius: 20px;
            border: 0.1rem solid;
            cursor: pointer;
            transition: 0.5s;
            right: 0px;

            &:after {
                content: "";
                width: 25px;
                height: 25px;
                position: absolute;
                top: 0.35rem;
                left: 4px;
                background: linear-gradient(180deg, #ffcc89, #d8860b);
                border-radius: 18px;
                box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);
                transition: 0.5s;
            }

            svg {
                position: absolute;
                width: 18px;
                top: 0.5rem;
                z-index: 10;

                &.mode-1 {
                    left: 8px;
                    transition: 0.5s;
                }

                &.mode-2 {
                    color: #000;
                    right: 8px;
                    fill: #7e7e7e;
                    transition: 0.5s;
                }
            }
        }

        .darkmode-toggle:checked {
            background: #242424;

        }

        .darkmode-toggle {
            width: 10px;
            height: 10px;
            visibility: hidden;

            &:active:after {
                width: 260px;
            }
        }

        .active.light {
            background: #242424;

            &:after {
                left: 64px;
                transform: translateX(-100%);
                background: linear-gradient(180deg, #777, #52525b);
            }

            svg {
                &.mode-1 {
                    fill: #fff;
                }


                &.mode-2.dark {
                    fill: #fff;
                }
                &.mode-2.classic {
                    fill: #fff;
                }
            }
        }
        .active.dark {
            background: #ebebeb;

            &:after {
                left: 58px;
                transform: translateX(-100%);
                background: linear-gradient(180deg, #777, #52525b);
            }

            svg {
                &.mode-1 {
                    fill: #000;

                }

                &.mode-2 {
                    fill: #fff;
                }
            }
        }
        .active.classic{
            background: #fff ;
            &:after {
                left: 55px;
                transform: translateX(-100%);
                background: linear-gradient(180deg, #777, #E5E7EB);
            }

            svg {
                &.mode-1 {
                    fill: #fff;
                }
                &.mode-1.light {
                    color: #000;
                    fill: #fff;
                }
                &.mode-1.dark {
                    fill: #000;
                }
                &.mode-2 {
                    fill: #fff;
                }
            }
        }
    </style>

</div>
