<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Store;
use App\NormalizeData;
use App\Stripe\StripeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    use NormalizeData;

    public function index(Request $request)
    {
        activity()
            ->withProperties([
                'request_data' => $request->all(),
            ])
            ->log('Feedback query');
        StripeService::setApiKey(config('services.stripe.secret'));

        // Validate input
        $validated = $request->validate([
            'phone' => ['nullable', 'regex:/^\+?[0-9]{10,15}$/'],
            'email' => ['nullable', 'email'],
            'orderId' => ['required'],
            'outcome' => ['required'],
        ]);

        // Ensure at least one of phone or email is provided
        if (empty($validated['phone']) && empty($validated['email'])) {
            return response()->json(['message' => 'Either phone or email must be provided.'], 422);
        }

        // Extract API keys from headers
        $apiKey = $request->header('X-Api-Key');
        $apiSecret = $request->header('X-Api-Secret');

        if (!$apiKey || !$apiSecret) {
            activity()
                ->withProperties([
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret,
                    'request_data' => $request->all(),
                ])
                ->log('Unauthorized access attempt: Missing API key or secret.');

            return response()->json(['message' => 'Missing authorized key'], 402);
        }

        // Find store by API credentials
        $store = Store::query()
            ->where('api_key', $apiKey)
            ->where('api_secret', $apiSecret)
            ->first();

        if (!$store) {
            activity()
                ->withProperties([
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret,
                ])
                ->log('Store not found for provided API credentials.');

            return response()->json(['message' => 'Threshold not found'], 404);
        }

        // Retrieve subscription details from Stripe
        $stripeSubscription = $store->subscription['stripe_subscription_id'];
        $subscription = StripeService::getSubscription($stripeSubscription);
        $subscriptionCurrentPeriodEnd = Carbon::createFromTimestamp($subscription['current_period_end']);
        $currentDate = Carbon::now('Europe/Budapest');

        if ($subscriptionCurrentPeriodEnd->lessThan($currentDate)) {
            activity()
                ->withProperties([
                    'store_id' => $store['store_id'],
                    'subscription_id' => $stripeSubscription,
                    'subscription_end_date' => $subscriptionCurrentPeriodEnd,
                ])
                ->log('Subscription expired for store.');

            return response()->json(['message' => 'Threshold has expired'], 400);
        }

        // Normalize phone and email data
        if (!empty($validated['phone'])) {
            $normalizedPhone = $this->normalizePhoneNumber($validated['phone']);
            $phoneHash = hash('sha256', $normalizedPhone);
        } else {
            $phoneHash = null;
        }

        if (!empty($validated['email'])) {
            $normalizedEmail = $this->normalizeEmail($validated['email']);
            $emailHash = hash('sha256', $normalizedEmail);
        } else {
            $emailHash = null;
        }

        // Log feedback creation attempt
        activity()
            ->withProperties([
                'store_id' => $store['store_id'],
                'email_hash' => $emailHash,
                'phone_hash' => $phoneHash,
                'order_id' => $validated['orderId'],
                'is_received' => $validated['outcome'] == 1 ? 1 : 0,
            ])
            ->log('Creating feedback entry.');

        // Save feedback to database
        Feedback::query()->create([
            'store_id' => $store['store_id'],
            'email' => $emailHash,
            'phone' => $phoneHash,
            'order_identifier' => $validated['orderId'],
            'is_received' => $validated['outcome'] == 1 ? 1 : 0,
            'created_at' => Carbon::now(),
        ]);

        // Log successful feedback creation
        activity()
            ->withProperties([
                'store_id' => $store['store_id'],
                'feedback_data' => [
                    'email_hash' => $emailHash,
                    'phone_hash' => $phoneHash,
                    'order_id' => $validated['orderId'],
                    'is_received' => $validated['outcome'] == 1 ? 1 : 0,
                ],
            ])
            ->log('Feedback successfully created.');

        return response()->json([
            'message' => 'Successfully created feedback!',
        ], 200);
    }
}
