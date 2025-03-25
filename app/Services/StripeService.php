<?php

    namespace App\Services;

    use Stripe\Stripe;
    use Stripe\Customer;
    use Stripe\PaymentMethod;
    use Stripe\Subscription;

    class StripeService
    {
        public function __construct()
        {
            Stripe::setApiKey(config('services.stripe.secret'));
        }

        public function createCustomer($user)
        {
            return Customer::create([
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
            ]);
        }

        public function attachPaymentMethod($customerId, $paymentMethodId)
        {
            // Fizetési mód csatolása a Stripe ügyfélhez
            PaymentMethod::attach($paymentMethodId, [
                'customer' => $customerId,
            ]);

            // Alapértelmezett fizetési mód beállítása
            Customer::update($customerId, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);
        }

        public function createSubscription($customerId, $priceId)
        {
            return Subscription::create([
                'customer' => $customerId,
                'items' => [['price' => $priceId]], // price_xxxx
                'expand' => ['latest_invoice.payment_intent'],
            ]);
        }
    }
