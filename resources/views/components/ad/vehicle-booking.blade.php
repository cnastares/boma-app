@props(['ad'])

@php
    // Retrieve all the vehicle bookings associated with the ad
    // dd($ad->vehicleBooking());
    $vehicleBookings = $ad?->vehicleBooking()?->get();
    // Initialize an array to store disabled date ranges
    $disabledDates = [];

    // Loop through the bookings and collect the start and end date ranges
    if (isset($vehicleBookings)) {
        foreach ($vehicleBookings as $booking) {
            $disabledDates[] = [
                'from' => \Carbon\Carbon::parse($booking->start_date)
                    ->subDay()
                    ->format('Y-m-d'),
                'to' => \Carbon\Carbon::parse($booking->end_date)->format('Y-m-d'), // Add one day to end_date
            ];
        }
    }
@endphp

<!-- Pass the disabled date ranges to JavaScript -->
<script>
    const disabledDateRanges = @json($disabledDates);
</script>
<div class="">
    <!-- Date Pickers Section -->
    <div class="text-center space-y-2 relative">
        <h3 class="text-lg font-semibold text-start">{{ __('messages.t_trip_dates') }}</h3>
        <div class="flex items-center justify-between  relative ">
            <div class="flex items-center space-x-2 border border-gray-200 dark:border-white/20 classic:border-black rounded-md px-3 py-2 cursor-pointer w-[45%]"
                id="date-time-box">
                <x-heroicon-o-calendar-date-range class="w-5 h-5" />
                <p id="formatted-start-date" class="text-sm" wire:ignore><span
                        class="text-muted text-sm">{{ __('messages.t_start_date') }}</span></p>
            </div>

            <x-heroicon-o-arrow-right class="text-muted h-4 w-5" />

            <div class="flex items-center space-x-2 border border-gray-200 dark:border-white/20 classic:border-black rounded-md px-3 py-2 cursor-pointer w-[45%]"
                id="end-date-time-box">
                <x-heroicon-o-calendar-date-range class="w-5 h-5" />
                <span id="end-dateformatted" class="text-sm" wire:ignore><span
                        class="text-muted text-sm">{{ __('messages.t_end_date') }}</span></span>
            </div>
        </div>
        <div id="start-date"></div>
        <div id="end-date"></div>

        <!-- Trip dates -->
        <h3 class="text-lg font-semibold text-start" style="margin-top: 20px;">{{ __('messages.t_trip_time') }}</h3>

        <div class="flex items-center justify-between  relative text-sm" wire:ignore>
            <div x-data="{
                isOpen: false,
                startTime: ''
            }"
                class="flex  relative items-center gap-1 border focus-within:outline-none focus-within:border-none focus-within:ring-2 focus-within:ring-primary-600 dark:bg-gray-900 border-gray-200 dark:border-white/20 classic:border-black rounded-md px-3 py-2 cursor-pointer w-[45%] ">

                <button type="button" class="flex space-x-2 items-center w-full" @click="isOpen = true">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    <div class="flex justify-between items-center w-full">

                        <div class=" text-sm" type="button"
                            x-bind:class="startTime ? 'text-black dark:text-white' : 'text-muted'"
                            x-text="startTime?startTime:'{{ __('messages.t_start_time') }}'">
                            {{ __('messages.t_start_time') }}
                        </div>
                        <x-heroicon-o-chevron-down class="w-5 h-4" />
                    </div>
                </button>

                <div x-show="isOpen" class="origin-top-right absolute right-0 mt-2 w-full rounded-md shadow-lg">

                    <div class="dark:bg-gray-900 rounded-md bg-white shadow-xs " x-on:click.outside="isOpen = false">
                        <ul class="dark:bg-gray-900 absolute z-10 mt-3 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm classic:border-black border dark:border-white/20 "
                            tabindex="-1" role="listbox" aria-labelledby="listbox-label"
                            aria-activedescendant="listbox-option-3" id="startTime">
                        </ul>
                    </div>
                </div>


            </div>
            <x-heroicon-o-arrow-right class="text-muted h-4 w-5" />

            <div x-data="{
                isOpen: false,
                startTime: ''
            }"
                class="flex  relative gap-1 items-center  border focus-within:outline-none focus-within:border-none focus-within:ring-2 focus-within:ring-primary-600 dark:bg-gray-900 border-gray-200 dark:border-white/20 classic:border-black rounded-md px-3 py-2 cursor-pointer w-[45%] ">

                <button type="button" class="flex space-x-2 items-center w-full" @click="isOpen = true">
                    <x-heroicon-o-clock class="w-5 h-5" />
                    <div class="flex justify-between items-center w-full">
                        <div class=" text-sm" type="button"
                            x-bind:class="startTime ? 'text-black dark:text-white' : 'text-muted'"
                            x-text="startTime?startTime:'{{ __('messages.t_end_time') }}'">
                            {{ __('messages.t_end_time') }}
                        </div>
                        <x-heroicon-o-chevron-down class="w-5 h-4" />
                    </div>
                </button>
                <div x-show="isOpen" class="origin-top-right absolute right-0 mt-3 w-full rounded-md shadow-lg">

                    <div class="dark:bg-gray-900  rounded-md bg-white shadow-xs" x-on:click.outside="isOpen = false">
                        <ul class="dark:bg-gray-900  absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm classic:border-black border dark:border-white/20 "
                            tabindex="-1" role="listbox" aria-labelledby="listbox-label"
                            aria-activedescendant="listbox-option-3" id="endTime">
                        </ul>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- Include Flatpickr CSS for styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        .flatpickr-day:hover {
            background-color: black;
            color: white !important;
        }

        .nextMonthDay:hover {
            background-color: black !important;
            color: white !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr for the start date input
            const today = new Date();
            const minSelectableDate = today > new Date({{ $ad->start_date }}) ? today : {{ $ad->start_date }};

            const startDatePicker = flatpickr("#start-date", {
                disableMobile: "true",
                dateFormat: "Y-m-d",
                minDate: minSelectableDate,
                maxDate: "{{ $ad->end_date }}", // Set the maximum selectable date
                disable: disabledDateRanges.map(range => {
                    return {
                        from: new Date(range.from),
                        to: new Date(range.to)
                    };
                }),
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        const selectedDate = selectedDates[0];
                        endDatePicker.set('minDate', selectedDate);
                        const dateOptions = {
                            weekday: 'short',
                            month: 'short',
                            day: 'numeric'
                        };
                        const formattedDate = selectedDate.toLocaleDateString('en-US', dateOptions);
                        const formattedDispatchDate = flatpickr.formatDate(selectedDate, "Y-m-d");

                        Livewire.dispatch('startDate', {
                            date: formattedDispatchDate
                        });
                        document.getElementById('formatted-start-date').textContent = formattedDate;

                        document.querySelectorAll('.selected').forEach(function(day) {
                            day.style.background = 'black';
                        });
                    }
                }
            });

            // Trigger start-date Flatpickr when clicking the div
            document.getElementById('date-time-box').addEventListener('click', function() {
                startDatePicker.open();
                document.querySelectorAll('.flatpickr-calendar').forEach(function(calendar) {
                    calendar.style.zIndex = '0';
                });
                document.querySelectorAll('.flatpickr-day').forEach(function(day) {
                    day.style.borderRadius = '0.375rem';
                });
                document.querySelectorAll('.selected').forEach(function(day) {
                    day.style.background = 'black';
                });

                // addClass('.flatpickr-next-month','  border classic:border-black ');
                addClass('.flatpickr-months',
                    '  border dark:bg-gray-900 dark:text-white classic:border-black rounded-t-md dark:text-white'
                );
                addClass('.flatpickr-innerContainer',
                    ' border dark:bg-gray-900 dark:text-white classic:border-black border-t-0 rounded-b-md dark:text-white'
                );
                addClass('.flatpickr-day',
                    ' dark:text-white'
                );
                addClass('.flatpickr-day:hover',
                    ' dark:!bg-black'
                );
                addClass('.flatpickr-weekday',
                    ' dark:text-white'
                );
                addClass('.flatpickr-month',
                    ' dark:!text-white'
                );
                addClass('.numInput cur-year',
                    ' dark:!text-white'
                );
                addClass('.flatpickr-next-month',
                    ' dark:!fill-white'
                );
                addClass('.flatpickr-prev-month',
                    ' dark:!fill-white'
                );
                // addClass('.flatpickr-monthDropdown-months {',
                //     ' dark:!bg-black'
                // );
            });

            //
            // Initialize Flatpickr for the end date input
            const endDatePicker = flatpickr("#end-date", {
                // enableTime: true,
                disableMobile: "true",
                dateFormat: "Y-m-d H:i",
                minDate: minSelectableDate,
                maxDate: "{{ $ad->end_date }}",
                defaultHour: 0,
                defaultMinute: 0,
                disable: disabledDateRanges.map(range => {
                    return {
                        from: new Date(range.from),
                        to: new Date(range.to)
                    };
                }),
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        const selectedDate = selectedDates[0];
                        startDatePicker.set('maxDate',
                            selectedDate); // Set the maxDate for startDatePicker
                        const dateOptions = {
                            weekday: 'short',
                            month: 'short',
                            day: 'numeric'
                        };
                        const formattedDate = selectedDate.toLocaleDateString('en-US', dateOptions);
                        const formattedDispatchDate = flatpickr.formatDate(selectedDate, "Y-m-d");

                        Livewire.dispatch('endDate', {
                            date: formattedDispatchDate
                        });
                        document.getElementById('end-dateformatted').textContent = formattedDate;
                        document.querySelectorAll('.selected').forEach(function(day) {
                            day.style.background = 'black';
                        });
                    }
                }
            });

            // Trigger end-date Flatpickr when clicking the div
            document.getElementById('end-date-time-box').addEventListener('click', function() {
                endDatePicker.open();
                document.querySelectorAll('.flatpickr-calendar').forEach(function(calendar) {
                    calendar.style.zIndex = '0';
                });
                document.querySelectorAll('.flatpickr-day').forEach(function(day) {
                    day.style.borderRadius = '0.375rem';
                });
                document.querySelectorAll('.selected').forEach(function(day) {
                    day.style.background = 'black';
                });

                // addClass('.flatpickr-next-month','  border');
                addClass('.flatpickr-months',
                    '  border dark:bg-gray-900 dark:text-white classic:border-black rounded-t-md dark:text-white'
                );
                addClass('.flatpickr-innerContainer',
                    ' border dark:bg-gray-900 dark:text-white classic:border-black border-t-0 rounded-b-md dark:text-white'
                );
                addClass('.flatpickr-day',
                    ' dark:text-white'
                );
                addClass('.flatpickr-day:hover',
                    ' dark:!bg-black'
                );
                addClass('.flatpickr-weekday',
                    ' dark:text-white'
                );
                addClass('.flatpickr-month',
                    ' dark:!text-white'
                );
                addClass('.numInput cur-year',
                    ' dark:!text-white'
                );
                addClass('.flatpickr-next-month',
                    ' dark:!fill-white'
                );
                addClass('.flatpickr-prev-month',
                    ' dark:!fill-white'
                );
                // addClass('.flatpickr-monthDropdown-months {',
                //     ' dark:!bg-black'
                // );
            });

            function generateTimeOptions(selectId) {
                const select = document.getElementById(selectId);
                const interval = {{ $vehicleRentalSettings->time_interval }}; // 30-minute interval
                let timeOptions = "";


                // Generate time slots
                // for (let hour = 0; hour < 24; hour++) {
                //     for (let minute = 0; minute < 60; minute += interval) {
                //         let hour12 = hour % 12 || 12; // Convert to 12-hour format, use 12 instead of 0
                //         const minuteString = minute.toString().padStart(2, '0');
                //         const amPm = hour < 12 ? 'AM' :
                //             'PM'; // AM for hours less than 12, PM for hours 12 and above
                //         const timeString = `${hour12}:${minuteString} ${amPm}`;

                //         // Generate the <li> item
                //         timeOptions += `
            // <li class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-gray-900 hover:bg-black hover:text-white dark:text-white" role="option" wire:key='${selectId+timeString}' x-on:click="$wire.${selectId}='${timeString}';isOpen=false;startTime='${timeString}'">
            //         <span class="ml-3 block truncate font-normal text-sm" >${timeString}</span>
            // </li>`;
                //     }
                // }
                for (let hour = 0; hour < 24; hour++) {
                    for (let minute = 0; minute < 60; minute += interval) {
                        const hourString = hour.toString().padStart(2, '0'); // Ensure hours are two digits
                        const minuteString = minute.toString().padStart(2, '0');
                        const timeString = `${hourString}:${minuteString}`; // Format as HH:MM for 24-hour time

                        // Generate the <li> item
                        timeOptions += `
            <li class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-gray-900 hover:bg-black hover:text-white dark:text-white" role="option" wire:key='${selectId+timeString}' x-on:click="$wire.${selectId}='${timeString}';isOpen=false;startTime='${timeString}'">
                <span class="ml-3 block truncate font-normal text-sm">${timeString}</span>
            </li>`;
                    }
                }


                select.innerHTML = timeOptions;
            }
            generateTimeOptions('startTime');
            generateTimeOptions('endTime');

            function addClass(selector, classes) {
                document.querySelectorAll(selector).forEach(function(element) {
                    element.className += classes;
                });
            }
            // Call the function to generate the time options when the page loads
        });
    </script>



    <!-- Pricing Section -->
    <div class="my-4 text-left flex items-center gap-1">
        <span class="text-3xl font-bold">{{config('app.currency_symbol')}}{{ $ad->price }}</span>
        <span class="font-normal">/</span>
        <span class="font-normal">Per day</span>
    </div>
    <!-- Book Now Button -->
    <x-button.secondary size="lg" wire:click="bookNowPage()"
        class="w-full mb-4 bg-black block text-white py-2 px-4 rounded-xl dark:!bg-primary-600 dark:text-black">{{ __('messages.t_car_book') }}
    </x-button.secondary>



</div>
