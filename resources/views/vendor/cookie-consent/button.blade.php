<form action="{!! $url !!}" {!! $attributes !!}>
    @csrf
    <button type="submit" class="{!! $basename !!}__link !bg-white !border-none">
        <span class="{!! $basename !!}__label !text-black">{{ $label }}</span>
    </button>
</form>
