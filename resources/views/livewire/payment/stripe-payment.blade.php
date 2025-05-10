<div>
    @if (isset($payment_gateway_params['client_secret']) && $public_key)
        {{-- Form --}}
        <form id="stripe-payment-form" wire:ignore x-ignore
            class="dark:bg-black-700 dark:text-gray-700">
            <div id="stripe-payment-element"></div>
            {{-- Pay --}}
            <button onclick="handleStripePayment()" id="stripe-payment-button" type="submit"
                class="mt-6 w-full text-[13px] font-semibold flex justify-center
                bg-black dark:bg-primary-600 hover:bg-primary-700 dark:hover:bg-primary-500
                text-white py-4 px-8 rounded tracking-wide focus:outline-none focus:ring-2 focus:ring-primary-400
                cursor-pointer disabled:!bg-gray-200 disabled:!text-gray-600 disabled:cursor-not-allowed
                dark:disabled:!bg-gray-700 dark:disabled:!text-gray-400">
                {{ __('messages.t_pay_now') }}
            </button>
        </form>
    @endif
</div>
@assets
    <script src="https://js.stripe.com/v3/" defer></script>
@endassets

@script
    <script>
        setTimeout(function() {
            // Stripe public key
            const stripe = Stripe("{{ $public_key }}");

            const isDarkMode = document.documentElement.classList.contains('dark');
            console.log(isDarkMode);

            // Payment options
            const options = {
                clientSecret: "{{ $payment_gateway_params['client_secret'] ?? '' }}",
                appearance: {
                    theme: 'stripe',
                    variables: {
                        colorPrimaryText: '#fff',
                        colorBackground: '#ffffff',
                        colorText: isDarkMode ? '#ebecf2' : '#30313d',
                        colorDanger: '#df1b41',
                        spacingUnit: '6px',
                        borderRadius: '3px'
                    }
                },
            };

            const elements = stripe.elements(options);

            // Create and mount the Payment Element
            const paymentElement = elements.create('payment');
            paymentElement.mount('#stripe-payment-element');

            window.handleStripePayment = function() {
                document.getElementById("stripe-payment-button").disabled = true;

                stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: "{{ url('callback/stripe?action=') }}" + "{{ $type }}",
                    },
                }).then((response) => {

                    // Check if error
                    if (response.error) {
                        new FilamentNotification()
                            .title("{{ __('messages.t_error') }}")
                            .body(response.error.message)
                            .danger()
                            .send()
                    }

                    document.getElementById("stripe-payment-button").disabled = false;

                }).catch((error) => {

                    new FilamentNotification()
                        .title("{{ __('messages.t_error') }}")
                        .body(error.message)
                        .danger()
                        .send()

                    document.getElementById("stripe-payment-button").disabled = false;
                });
            }
        }, 0);
    </script>
@endscript
