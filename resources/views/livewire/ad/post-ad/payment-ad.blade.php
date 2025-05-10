<div>
    <div class="bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 p-4 shadow-sm rounded-lg mb-6 border border-gray-200  dark:border-white/10 classic:border-black">
        <!-- Summary Header -->
        <h3 class="text-lg font-semibold border-b pb-2 mb-4 border-gray-200  dark:border-white/10 classic:border-black">{{ __('messages.t_summary') }}</h3>

        <!-- Dynamic Promotion Details -->
        @foreach ($promotions as $promotion)
            <div wire:key='summary-promotion-{{ $promotion->id }}' class="flex justify-between py-1">
                <span>{{ $promotion->name }}  - {{ $promotion->duration }} days</span>
                <span>{{ formatPriceWithCurrency($promotion->price) }}</span>
            </div>
        @endforeach

        <!-- Divider -->
        <div class="border-t my-3 border-gray-100  dark:border-white/10 classic:border-black"></div>

        <!-- Subtotal -->
        <div class="flex justify-between py-1">
            <span class="font-semibold">{{ __('messages.t_subtotal') }}</span>
            <span>{{ formatPriceWithCurrency($subtotal) }}</span>
        </div>

        <!-- Tax -->
        <div class="flex justify-between py-1">
            <span>{{ __('messages.t_tax') }}</span>
            <span>{{ formatPriceWithCurrency($tax) }}</span>
        </div>

        <!-- Divider -->
        <div class="border-t my-3 border-gray-100  dark:border-white/10 classic:border-black"></div>

        <!-- Total -->
        <div class="flex justify-between py-1">
            <span class="font-semibold text-lg">{{ __('messages.t_total') }}</span>
            <span class="font-semibold text-lg">{{ formatPriceWithCurrency($total) }}</span>
        </div>

        {{-- Exchange Rate --}}
        @if ($this->defaultCurrency && $this->isDifferentRate)
            <div class="flex justify-between py-1">
                <span class="font-semibold text-lg">{{ __('messages.t_total_including_exchange_rate') }}</span>
                <span class="font-semibold text-lg">{{ formatPriceWithCurrency($this->convertedTotal) }}</span>
            </div>
        @endif
    </div>




    <h3 class="mb-6">{{ __('messages.t_choose_payment_label') }}</h3>
    @if(count($this->initializePaymentOptions()) >= 1)
    <form wire:submit>
        <div class="mb-5 payment-methods">
            {{ $this->form }}
        </div>
        @if ($currentPayment)
          <livewire:dynamic-component  :key="$currentPayment" :component="$currentPayment" :$type  :data="$this->paymentData"   :total="$this->defaultCurrency && $this->isDifferentRate ? $this->convertedTotal : $total"  :$id :$subtotal :$tax :$selectedPaymentMethod  />
        @endif
    </form>
    @else
    @include('components.empty-payment')
    @endif
</div>
