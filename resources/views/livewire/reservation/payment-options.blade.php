<div>
    <h2 class="mb-10 text-xl font-semibold">{{ __('messages.t_payment_options')}}</h2>
    @if(count($this->initializePaymentOptions()) >= 1)
    <form wire:submit>
        <div class="mb-5 payment-methods">
            {{ $this->form }}
        </div>
    </form>
    @else
    @include('components.empty-payment')
    @endif
</div>
