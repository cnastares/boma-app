@props(['user'])

{{-- <a href="{{ route('feedback', ['id' => $user->id]) }}"  class="flex items-center bg-gray-50 mt-4 cursor-pointer justify-between rounded-xl w-full py-1 px-2 border border-gray-200 dark:border-white/20 dark:bg-gray-900 classic:border-black" >
    <!-- Phone Icon -->
    <div class="p-2 flex gap-x-2 items-center">
          <!-- Phone Number Display -->
        <div class="font-medium">
            <span>
        {{$user->feedbackCount() ? pluralize($user->feedbackCount(), __('messages.t_feedback'),__('messages.t_feedbacks')) : __('messages.t_feedback')}}
        </span>
        </div>
    </div>

    <!-- Reveal Button -->
    <button class="px-3 py-1 text-sm text-primary-600 underline" >
        {{ __('messages.t_view_all') }}
    </button>
</a> --}}
<a href="{{ route('feedback', ['id' => $user->id]) }}" class="flex items-end gap-x-2 mt-2">
    <x-heroicon-o-chat-bubble-bottom-center-text aria-hidden="true"  class="w-6 h-6" />
    <p class="text-3xl font-bold translate-y-1">{{$user->rating}}</p>
    <div class="flex items-end gap-x-1">
        <x-star-rating :rating="$user->rating" :id="$user->id" :name="$user->id" />
        <span class="text-sm">({{$user->feedbackCount()}})</span>
    </div>
    <p class="text-sm underline text-blue-600 cursor-pointer">{{__('messages.t_view_reviews')}}</p>
</a>
