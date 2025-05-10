@if ((is_ecommerce_active() && isECommerceAddToCardEnabled()) && (!isEnablePointSystem()))
<a href="/cart-summary" class="relative cursor-pointer">
    <svg class="w-[1.675rem] h-[1.675rem] mr-[1rem] dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
    </svg>
    <span class="absolute top-[-0.3rem] right-[0.6rem] text-white text-xs font-semibold rounded-full px-1 bg-gray-900 block text-white dark:bg-primary-600 dark:text-black">
        {{ auth()->check() ? auth()->user()->carts()->count() ?: count(session('cart', [])) : count(session('cart', [])) }}
</span>
</a>
@endif
