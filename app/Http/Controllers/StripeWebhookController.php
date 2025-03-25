<?php

    namespace App\Http\Controllers;

    use App\Jobs\DeleteStore;
    use App\Jobs\DeleteStoreJob;
    use App\Jobs\SendFailedPaymentEmailJob;
    use App\Mail\FailedPayment;
    use App\Mail\SuccessfulPaymentMail;
    use App\Models\Subscription;
    use App\Models\UserNotification;
    use App\Services\DeleteStoreService;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Http\Request;
    use Mail;
    use Stripe\Stripe;
    use Stripe\Webhook;
    use Stripe\Invoice;

    class StripeWebhookController extends Controller
    {
        public function handleWebhook(Request $request)
        {
            // Set your Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Retrieve the webhook secret from your .env file
            $webhookSecret = config('services.stripe.webhook.secret');
            Log::info('Webhook received: ' . $webhookSecret);

            // Get the payload and signature header
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');

            try {
                // Verify the webhook signature
                $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } catch (\UnexpectedValueException $e) {
                // Invalid payload
                return response()->json(['error' => 'Invalid payload'], 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            // Handle the event based on its type
            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                default:
                    Log::info('Unhandled event type: ' . $event->type);
                    break;
            }

            return response()->json(['status' => 'success'], 200);
        }

        private function handlePaymentSucceeded($invoice)
        {
            // Retrieve subscription ID from the invoice object
            $stripeSubscriptionId = $invoice->subscription;

            // If subscription ID is null, fetch the full invoice from Stripe with expanded subscription
            if (!$stripeSubscriptionId) {
                $invoice = Invoice::retrieve(["id" => $invoice->id, "expand" => ["subscription"]]);
                $stripeSubscriptionId = $invoice->subscription->id ?? null;
            }

            Log::info('Subscription ID: ' . ($stripeSubscriptionId ?? 'null'));

            if ($stripeSubscriptionId) {
                // Update subscription status in your database
                Subscription::query()
                    ->where('stripe_subscription_id', $stripeSubscriptionId)
                    ->update([
                        'status' => 'active',
                        'updated_at' => now(),
                        'end_date' => now()->addMonth(),
                    ]);

                Log::info("Subscription {$stripeSubscriptionId} marked as active.");


                $subscription = Subscription::query()
                ->where('stripe_subscription_id', $stripeSubscriptionId)
                ->first();

                $user = DB::table('users')->where('id', $subscription->user_id)->first();
                $store = DB::table('stores')->where('subscription_id', $subscription->subscription_id)->first();

                if ($user && $store) {

                    try {
                        Mail::to($user->email)->send(new SuccessfulPaymentMail($user, $store));

                        UserNotification::create([
                            'user_id' => $user->id,
                            'event'   => 'successful_payment',
                            'store_id' => $store->store_id,
                        ]);

                        Log::info("Successful payment email sent to {$user->email} for subscription {$stripeSubscriptionId}.");

                        activity()
                            ->causedBy($user)
                            ->performedOn($subscription)
                            ->withProperties([
                                'subscription_id' => $subscription->id,
                                'stripe_subscription_id' => $stripeSubscriptionId,
                                'user_email' => $user->email,
                                'store_domain' => $store->domain ?? 'unknown',
                                'status' => 'active',
                                'end_date' => $subscription->end_date,
                            ])
                            ->log('Payment succeeded, subscription activated.');
                    } catch (\Exception $e) {
                        Log::error("Failed to send successful payment email to {$user->email}. Error: {$e->getMessage()}");

                        activity()
                        ->causedBy($user)
                        ->performedOn($subscription)
                        ->withProperties([
                            'subscription_id' => $subscription->id,
                            'stripe_subscription_id' => $stripeSubscriptionId,
                            'user_email' => $user->email,
                            'store_domain' => $store->domain ?? 'unknown',
                            'error' => $e->getMessage(),
                            'status' => 'active',
                            'end_date' => $subscription->end_date,
                        ])
                        ->log('Failed to send successful payment email.');
                    }

                } else {
                    Log::warning("User or store not found for subscription {$stripeSubscriptionId}.");
                }

            } else {
                Log::warning("Unable to find subscription ID for invoice: {$invoice->id}");
            }
        }

        private function handlePaymentFailed($invoice)
        {
            // Retrieve subscription ID from the invoice object
            $stripeSubscriptionId = $invoice->subscription;

            // If subscription ID is null, fetch the full invoice from Stripe with expanded subscription
            if (!$stripeSubscriptionId) {
                $invoice = Invoice::retrieve(["id" => $invoice->id, "expand" => ["subscription"]]);
                $stripeSubscriptionId = $invoice->subscription->id ?? null;
            }

            Log::info('Subscription ID: ' . ($stripeSubscriptionId ?? 'null'));

            if ($stripeSubscriptionId) {
                // Update subscription status in your database
                DB::table('subscriptions')
                    ->where('stripe_subscription_id', $stripeSubscriptionId)
                    ->update([
                        'status' => 'inactive',
                        'updated_at' => now(),
                    ]);

                Log::info("Subscription {$stripeSubscriptionId} marked as inactive.");

                $subscription = DB::table('subscriptions')
                    ->where('stripe_subscription_id', $stripeSubscriptionId)
                    ->first();

                $user = DB::table('users')->where('id', $subscription->user_id)->first();
                $store = DB::table('stores')->where('subscription_id', $subscription->subscription_id)->first();

                $user = DB::table('users')->find($subscription->user_id);
                $store = DB::table('stores')->find($subscription->store_id);

                if ($user && $store) {
                    try {
                        $expireDate = Carbon::parse($store->subscription->end_date)->addDays(30);

                        if (
                            $store->subscription->end_date === date('Y-m-d') ||
                            $store->subscription->end_date === date('Y-m-d', strtotime('-25 days')) ||
                            $store->subscription->end_date === date('Y-m-d', strtotime('-27 days'))
                        ) {

                            Mail::to($user->email)->send(new FailedPayment($user, $store, $expireDate));

                            UserNotification::create([
                                'user_id' => $user->id,
                                'event'   => 'failed_payment',
                                'store_id' => $store->store_id,
                            ]);

                            Log::info("Failed payment email sent to {$user->email} for subscription {$stripeSubscriptionId}.");
                        }


                        if($store->subscription->end_date === date('Y-m-d', strtotime('-30 days'))){
                            $deleteStoreService = new DeleteStoreService();
                            $deleteStoreService->deleteStore($store->id);
                        }


                        activity()
                            ->causedBy($user)
                            ->performedOn($subscription)
                            ->withProperties([
                                'subscription_id' => $subscription->id,
                                'stripe_subscription_id' => $stripeSubscriptionId,
                                'user_email' => $user->email,
                                'store_domain' => $store->domain ?? 'unknown',
                                'status' => 'inactive',
                                'updated_at' => now(),
                            ])
                            ->log('Payment failed, subscription marked as inactive.');
                    } catch (\Exception $e) {
                        Log::error("Failed to send failed payment email to {$user->email}. Error: {$e->getMessage()}");

                        activity()
                        ->causedBy($user)
                        ->performedOn($subscription)
                        ->withProperties([
                            'subscription_id' => $subscription->id,
                            'stripe_subscription_id' => $stripeSubscriptionId,
                            'user_email' => $user->email,
                            'store_domain' => $store->domain ?? 'unknown',
                            'error' => $e->getMessage(),
                            'status' => 'inactive',
                            'updated_at' => now(),
                        ])
                        ->log('Failed to send failed payment email.');
                    }
                } else {
                    Log::warning("User or store not found for subscription {$stripeSubscriptionId}.");
                }

            } else {
                Log::warning("Unable to find subscription ID for invoice: {$invoice->id}");
            }
        }
    }
