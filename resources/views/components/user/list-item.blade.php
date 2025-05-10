@props(['user'])

<a  href="{{ route('view-profile', ['slug' => $user->getSlugAttribute(), 'id' => $user->id]) }}" class="flex items-center cursor-pointer outline-none group">
    <div class="bg-gray-200 dark:bg-black dark:text-gray-100 text-black border rounded-full h-10 w-10 flex items-center justify-center">
        @if($user->profile_image)
            <img src="{{ $user->profile_image }}" alt="{{ $user->name }}" class="rounded-full w-10 h-10 border border-black">
        @else
        <span>{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
        @endif
    </div>
    <span class="ltr:ml-3  rtl:mr-3 group-hover:underline">{{ $user->name }}</span>
</a>
