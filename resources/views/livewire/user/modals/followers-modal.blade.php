
    {{-- Modals (Followers || Following) --}}
    <x-modal.index id="follow-modal" alignment="start"  width="3xl">

        <x-slot name="heading">
            {{ $showFollowers ? __('messages.t_followers') : __('messages.t_following') }}
        </x-slot>
        <div class=" space-y-4">
            @if($showFollowers)
            @foreach($followersList as $follower)
            <div wire:key="follower-{{ $follower->id }}">
                <x-user.list-item :user="$follower" />
            </div>
            @endforeach
            @else
            @foreach($followingList as $following)
            <div wire:key="following-{{ $following->id }}">
                <x-user.list-item :user="$following" />
            </div>
            @endforeach
            @endif
        </div>
    </x-modal.index>
