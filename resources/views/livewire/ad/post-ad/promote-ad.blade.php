<div>
    <p class="mb-6">{{ __('messages.t_boost_ad_visibility') }}</p>

    <div>
        @foreach($promotions as $promotion)
            <button aria-label="{{ $promotion->name }}" type="button" wire:click="togglePromotion({{ $promotion->id }})"
                wire:key="promotion-{{ $promotion->id }}"
                class="bg-white block w-full flex justify-between items-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 px-3 py-2 rounded-xl mb-6 cursor-pointer classic:ring-black {{$homeSettings->enable_hover_animation?'classic-hover-shadow':''}}"
                x-data="{
                    selectedPromotions: @entangle('selectedPromotions'),
                    isActive: function() {
                        return Object.keys(this.selectedPromotions).includes('{{ $promotion->id }}');
                    }
                }"
                :class="{ 'bg-white {{$homeSettings->enable_hover_animation?'classic:shadow-custom':''}} ': isActive() }">
                @php
                    $promotionStatus = $this->isActivePromotion($promotion->id);
                @endphp
                <div>
                    <div class="flex items-center gap-x-3">
                        <img src="{{ asset('images/' . $promotion->image) }}" alt="{{ $promotion->name }}" class="mx-auto w-10 h-10">
                        <div>
                            <p class="font-semibold text-left">
                               {{ $promotion->name }} - {{ $promotion->duration }} days - {{ formatPriceWithCurrency($promotion->price) }}
                            </p>
                            <p class="text-sm ">{{ $promotion->description }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    @if($promotionStatus['isActive'])
                        <div class="text-green-800 font-semibold">
                            <span>{{__('messages.t_active_status')}}: {{ \Carbon\Carbon::parse($promotionStatus['start_date'])->translatedFormat('M d') }} - {{ \Carbon\Carbon::parse($promotionStatus['end_date'])->translatedFormat('M d') }}</span>
                        </div>
                    @else
                        <label class="relative inline-flex items-center cursor-pointer" aria-hidden="true">
                            <input name="promotion" tabindex="-1" disabled  type="checkbox" value="{{ $promotion->id }}" class="sr-only peer" x-bind:checked="isActive()">
                            <div class="w-11 h-6 bg-gray-200 border  peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-black"></div>
                            <span class="ml-3 text-sm md:text-base  text-gray-900 dark:text-gray-300"></span>
                        </label>
                    @endif
                </div>
            </button>
        @endforeach
    </div>

    @if(array_key_exists(4, $selectedPromotions))
        <form wire:submit.prevent>
            <div class="mb-5">
                {{ $this->form }}
            </div>
        </form>
    @endif
</div>
