<div>
    <div class="form-group mt-4">
        <label for="payment_method">{{ __('add_payment_payment_method_label') }}</label>
        <div id="card-element"></div>
        <small class="text-muted">{{ __('add_payment_card_data_secure_message') }}</small>
        @error('payment_method') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div class="row mt-4">
        <div class="col-1 d-flex justify-content-center align-items-center">
            <button type="button" wire:click="prevStep" class="btn btn-secondary mt-2">
                <i class="bi bi-arrow-left"></i>
            </button>
        </div>
        <div class="col-11">
            <button type="button" onclick="f1()" class="btn btn-primary mt-2 w-100">
                {{ __('add_payment_add_payment_method_button') }}
            </button>
        </div>
    </div>
</div>
<script src="https://js.stripe.com/v3/"></script>

<script>
    let stripe;
    let elements;
    let cardElement;

    stripe = Stripe('{{ config('services.stripe.key') }}');
    elements = stripe.elements();
    cardElement = elements.create('card');
    cardElement.mount('#card-element');

    async function f1() {
        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            alert(error.message);
        } else {
            console.log('asdas')
            @this.call('storePaymentMethod', paymentMethod.id);
        }
    }

</script>
