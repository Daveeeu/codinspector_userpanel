<?php

namespace App\Stripe;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Coupon;
use Stripe\Subscription as StripeSubscription;
use Stripe\SubscriptionSchedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\Subscription;

class StripeService
{
    public static function setApiKey($key)
    {
        Stripe::setApiKey($key);
    }

    public static function createCustomerIfNotExists($user)
    {
        if (!$user->stripe_customer_id) {
            self::setApiKey(env('STRIPE_KEY'));

            $customer = Customer::create([
                'email' => $user->email,
                'name' => "{$user->first_name} {$user->last_name}",
                'metadata' => [
                    'domain_id' => 'inspectorramburs.ro'
                ],
            ]);

            $user->update(['stripe_customer_id' => $customer->id]);
            return $customer;
        }

        return Customer::retrieve($user->stripe_customer_id);
    }

    public static function createSubscription($customer, $package, $paymentFrequency, $trialDays, $couponCode = null)
    {
        return StripeSubscription::create([
            'customer' => $customer->id,
            'items' => [[
                'price' => $paymentFrequency === "monthly" ? $package->stripe_price_id : $package->stripe_price_yearly_id,
            ]],
            'coupon' => $couponCode,
            'trial_period_days' => $trialDays,
            'expand' => ['latest_invoice.payment_intent'],
        ]);
    }

    public static function createUsageBasedSubscription($customer, $package, $trialDays, $couponCode = null)
    {
        return StripeSubscription::create([
            'customer' => $customer->id,
            'items' => [[
                'price' => $package->stripe_price_id,
                'billing_thresholds' => [
                    'usage_gte' => 1,
                ],
            ]],
            'trial_period_days' => $trialDays,
            'coupon' => $couponCode,
            'expand' => ['latest_invoice.payment_intent'],
        ]);
    }

    public static function retrieveCoupon($couponCode)
    {
        return Coupon::retrieve($couponCode);
    }

    public static function storePaymentMethod($paymentMethodId, $customer)
    {
        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

        if (!$paymentMethod) {
            throw new \Exception('Invalid payment method ID');
        }

        $paymentMethod->attach(['customer' => $customer->id]);

        Customer::update($customer->id, [
            'invoice_settings' => ['default_payment_method' => $paymentMethod->id],
        ]);

        return $paymentMethod;
    }

    public static function createSubscriptionSchedule($customer, $package, $startDate)
    {
        return SubscriptionSchedule::create([
            'customer' => $customer->id,
            'start_date' => $startDate,
            'end_behavior' => 'release',
            'phases' => [[
                'items' => [
                    ['price' => $package->stripe_price_id, 'quantity' => 1],
                ],
            ]],
        ]);
    }

    public static function cancelSubscription($subscriptionId, $cancelAtPeriodEnd = true)
    {
        $subscription = StripeSubscription::retrieve($subscriptionId);

        if (!$subscription) {
            throw new \Exception('Subscription not found.');
        }

        $subscription->cancel_at_period_end = $cancelAtPeriodEnd;
        $subscription->save();

        return $subscription;
    }

    public static function cancelSubscriptionSchedule($scheduleId)
    {
        $schedule = SubscriptionSchedule::retrieve($scheduleId);

        if (!$schedule) {
            throw new \Exception('Subscription schedule not found.');
        }

        $schedule->cancel();
        return $schedule;
    }

    public static function reactivateSubscription($subscriptionId)
    {
        $subscription = StripeSubscription::retrieve($subscriptionId);

        if (!$subscription || $subscription->status !== 'active') {
            throw new \Exception('Cannot reactivate subscription.');
        }

        $subscription->cancel_at_period_end = false;
        $subscription->save();

        return $subscription;
    }


    public static function getSubscription($subscriptionId){
        return StripeSubscription::retrieve($subscriptionId, []);
    }
}
