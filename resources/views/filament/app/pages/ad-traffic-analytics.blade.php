<x-filament-panels::page>
    <div class="flex md:flex-row flex-col gap-4 gap-y-8">
        <!-- Age Data -->
        @if (getSubscriptionSetting('status') &&
                getActiveSubscriptionPlan() &&
                in_array(getActiveSubscriptionPlan()->demographic_analysis_level, ['basic', 'advanced']))
            <style>
                .ageTable table {
                    border: 1px solid #D1D5D8;
                    border-collapse: separate;
                    border-radius: 14px;
                    border-spacing: 0px;
                }

                .ageTable thead {
                    display: table-header-group;
                    vertical-align: middle;
                    border-color: inherit;
                    border-collapse: separate;
                    background-color: #5939A2;
                    color: white;
                }

                .ageTable tr {
                    display: table-row;
                    vertical-align: inherit;
                    border-color: inherit;
                }

                .ageTable th,
                td {
                    padding: 5px 15px 6px 15px;
                    text-align: left;
                    vertical-align: top;
                }

                .ageTable td {
                    border-top: 1px solid #ddd;
                }

                .fi-section-content {
                    border-top: 1px solid var(--thumb-border-color, #fff);
                }

                .dark {
                    --thumb-border-color: #18181B;
                }

                .classic {
                    --thumb-border-color: #000;
                }
            </style>
            <x-filament::section class="md:w-[50%] w-full" collapsible>
                <x-slot name="heading">
                    {{ __('messages.t_age_group_data') }}
                </x-slot>
                <table class="ageTable">
                    <thead>
                        <tr>
                            <th class="text-left py-2.5 rounded-ss-[14px] border-r border-[#D1D5D8] whitespace-nowrap">
                                {{ __('messages.t_age_group') }}</th>
                            <th class="text-left py-2.5 rounded-ss-[14px] border-r border-[#D1D5D8] whitespace-nowrap">
                                {{ __('messages.t_total_views') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ageTrafficData ?? [] as $data)
                            <tr>
                                <td class="border-r border-[#D1D5D8] p-1 whitespace-nowrap">{{ $data->age_group }}</td>
                                <td class="border-r border-[#D1D5D8] p-1 whitespace-nowrap">{{ $data->total_views }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-filament::section>
        @endif

        <!-- Location Data -->
        @if (getSubscriptionSetting('status') &&
                getActiveSubscriptionPlan() &&
                getActiveSubscriptionPlan()->demographic_analysis_level == 'advanced')

            <!-- OS Data -->
            <div class="md:w-[50%] bg-white p-3 rounded-lg border classic:border-black">
                <h2 class="font-semibold text-lg">{{ __('messages.t_top_country_views') }}</h2>
                @forelse ($locationData as $location)
                    <div class="w-full mt-2 ">
                        <div class="flex justify-between">
                            <div>
                                @unless ($location['country_code'] == 'N/A')
                                    <span>{!! country2flag($location['country_code']) !!}</span>
                                @endunless
                                @if ($location['country'] == 'N/A')
                                    {{ __('messages.t_unknown') }}
                                @else
                                    <span>{{ $location['country'] }}</span>
                                @endif
                            </div>

                            <div>
                                <span class="text-sm mr-5">{{ $location['percentage'] }}%</span>
                                <span>{{ $location['total_views'] }} </span>
                            </div>
                        </div>

                        <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $location['percentage'] }}%">
                            </div>
                        </div>
                    </div>
                @empty
                    <p class='text-sm text-gray-500'>{{ __('messages.t_data_not_available') }}</p>
                @endforelse
            </div>
    </div>
    <div class="flex md:flex-row flex-col gap-4 gap-y-8">
        <!-- OS Data -->
        <div class="md:w-[50%] bg-white p-3 rounded-lg border classic:border-black">
            <h2 class="font-semibold text-lg">{{ __('messages.t_operating_system') }}</h2>
            @forelse ($osData as $os)
                <div class="w-full mt-2 ">
                    <div class="flex justify-between">
                        <span>{{ $os->os ?? __('messages.t_unknown') }}</span>
                        <div>
                            <span
                                class="text-sm mr-5">{{ round(($os->total_views / $osData->sum('total_views')) * 100, 2) }}%</span>
                            <span>{{ $os->total_views }}</span>
                        </div>
                    </div>

                    <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-gray-700">
                        <div class="bg-primary-600 h-2 rounded-full"
                            style="width: {{ round(($os->total_views / $osData->sum('total_views')) * 100, 2) }}%">
                        </div>
                    </div>
                </div>
            @empty
                <p class='text-sm text-gray-500'>{{ __('messages.t_data_not_available') }}</p>
            @endforelse
        </div>
        <!-- browser Data -->
        <div class="md:w-[50%] bg-white p-3 rounded-lg border classic:border-black">
            <h2 class="font-semibold text-lg">{{ __('messages.t_browser') }}</h2>
            @forelse ($browserData as $browser)
                <div class="w-full mt-2 ">
                    <div class="flex justify-between">
                        <span>{{ $browser->browser ?? __('messages.t_unknown') }}</span>
                        <div>
                            <span
                                class="text-sm mr-5">{{ round(($browser->total_views / $browserData->sum('total_views')) * 100, 2) }}%</span>
                            <span>{{ $browser->total_views }}</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-gray-700">
                        <div class="bg-primary-600 h-2 rounded-full"
                            style="width: {{ round(($browser->total_views / $browserData->sum('total_views')) * 100, 2) }}%">
                        </div>
                    </div>
                </div>
            @empty
                <p class='text-sm text-gray-500'>{{ __('messages.t_data_not_available') }}</p>
            @endforelse
        </div>
    </div>

    <div class="md:flex-row flex-col flex gap-8">
        <!-- Device Data -->
        <div class="md:w-[50%] bg-white p-3 rounded-lg border classic:border-black">
            <h2 class="font-semibold text-lg">{{ __('messages.t_device') }}</h2>
            @forelse ($deviceData as $device)
                <div class="w-full mt-2 mb-2">
                    <div class="flex justify-between">
                        <div class="flex items-center gap-1">
                            @if ($device->device_type == 'mobile')
                                <x-heroicon-s-device-phone-mobile class="h-5 w-5 text-gray-600" />
                            @endif
                            @if ($device->device_type == 'desktop')
                                <x-heroicon-s-computer-desktop class="h-5 w-5 text-gray-600" />
                            @endif
                            <span> {{ ucfirst($device->device_type) ?? __('messages.t_unknown') }}</span>
                        </div>
                        <div>
                            <span
                                class="text-sm mr-5">{{ round(($device->total_views / $deviceData->sum('total_views')) * 100, 2) }}%</span>
                            <span>{{ $device->total_views }}</span>
                        </div>
                    </div>

                    <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-gray-700">
                        <div class="bg-primary-600 h-2 rounded-full"
                            style="width: {{ round(($device->total_views / $deviceData->sum('total_views')) * 100, 2) }}%">
                        </div>
                    </div>
                </div>
            @empty
                <p class='text-sm text-gray-500'>{{ __('messages.t_data_not_available') }}</p>
            @endforelse
        </div>
    </div>
    @endif

    <div class="overflow-x-auto p-0.5">
        @if (getSubscriptionSetting('status') &&
            getActiveSubscriptionPlan() &&
            in_array(getActiveSubscriptionPlan()->utm_parameters_level, ['basic', 'advanced']))
        {{ $this->table }}
        @endif
    </div>
</x-filament-panels::page>
