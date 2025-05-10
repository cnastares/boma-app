@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-none block w-full shadow-sm text-slate-900 dark:text-white text-sm sm:text-base  disabled:bg-slate-100 disabled:cursor-wait ring-1 transition duration-75 bg-white focus-within:ring-2 dark:bg-gray-900 ring-gray-950/10 focus-within:ring-primary-600 dark:ring-white/10 dark:focus-within:ring-primary-500 classic:ring-black']) !!}></textarea>
