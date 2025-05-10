@props(['disabled' => false, 'type' => 'checkbox'])

<input type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'focus:ring-black h-4 w-4 text-black border-slate-300 rounded']) !!}>
