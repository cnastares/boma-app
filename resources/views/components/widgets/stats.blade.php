@props(['title','value','description','icon'])
<div class="relative  flex flex-grow !flex-row items-center rounded-xl  border-[1px] border-gray-200 bg-white bg-clip-border shadow-md shadow-[#F3F3F3] dark:bg-gray-900 dark:border-[#ffffff33] dark:!bg-navy-800 dark:text-white dark:shadow-none classic:border-black">
    <div class="ml-3 flex h-[90px] w-auto flex-row items-center">
    <div class="rounded-full bg-lightPrimary md:p-3 p-0 dark:bg-navy-700">
        <span class="flex items-center text-brand-500 dark:text-white">
        {{-- <svg
            stroke="currentColor"
            fill="currentColor"
            stroke-width="0"
            viewBox="0 0 24 24"
            class="h-7 w-7"
            height="1em"
            width="1em"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path fill="none" d="M0 0h24v24H0z"></path>
            <path d="M4 9h4v11H4zM16 13h4v7h-4zM10 4h4v16h-4z"></path>
        </svg> --}}
        <x-dynamic-component :component="$icon" class="w-7 h-7"  />
        </span>

    </div>
    </div>
    <div class="h-50 ml-4 flex w-auto flex-col justify-center pr-2">
    <p class="font-dm text-sm font-medium text-gray-600 dark:text-gray-400">{{$title}}</p>
    <p class="text-xl font-bold text-navy-700 dark:text-white">{{$value}}</p>
    </div>
</div>
