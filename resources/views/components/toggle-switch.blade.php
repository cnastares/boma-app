@props(['enabled' => false, 'label' => ''])

<label {!! $attributes->merge(['class' => 'relative inline-flex items-center cursor-pointer']) !!} >
    <input type="checkbox" value="" class="sr-only peer" {{ $enabled ? 'checked' : '' }}>
    <div class="w-11 h-6 bg-gray-200 border  peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-black"></div>
    <span class="ml-3 text-sm md:text-base  text-gray-900 dark:text-gray-300">{{ $label }}</span>
</label>
