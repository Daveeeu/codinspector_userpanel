<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\QuotaLimitReachedMail;
use App\Mail\QuotaWarningMail;
use App\Models\Exception;
use App\Models\Feedback;
use App\Models\Query;
use App\Models\Store;
use App\NormalizeData;
use App\Stripe\StripeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Subscription;

class ThresholdController extends Controller
{
    use NormalizeData;

    public function index(Request $request)
    {
        activity()
            ->withProperties([
                'request_data' => $request->all(),
            ])
            ->log('Threshold query');
        StripeService::setApiKey(config('services.stripe.secret'));

        $apiKey = $request->header('X-Api-Key');
        $apiSecret = $request->header('X-Api-Secret');

        // Validate input
        $validated = $request->validate([
            'phone' => ['nullable', 'regex:/^\+?[0-9]{10,15}$/'],
            'email' => ['nullable', 'email'],
        ]);

        // Ensure at least one of phone or email is provided
        if (empty($validated['phone']) && empty($validated['email'])) {
            return response()->json(['message' => 'Either phone or email must be provided.'], 422);
        }

        if (!$apiKey && !$apiSecret) {
            activity()
                ->withProperties([
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret,
                    'request_data' => $request->all(),
                ])
                ->log('Unauthorized access attempt: Missing API key or secret. threshold');
            return response()->json(['message' => 'Missing authorized key'], 402);
        }

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
                ->log('Store not found for provided API credentials. threshold');
            return response()->json(['message' => 'Threshold not found'], 404);
        }

        $stripeSubscription = $store->subscription['stripe_subscription_id'];
        $subscription = StripeService::getSubscription($stripeSubscription);
        $subscriptionCurrentPeriodStart = Carbon::createFromTimestamp($subscription['current_period_start']);
        $subscriptionCurrentPeriodEnd = Carbon::createFromTimestamp($subscription['current_period_end']);
        $currentDate = Carbon::now('Europe/Budapest');

        if ($subscriptionCurrentPeriodEnd->lessThan($currentDate)) {
            activity()
                ->withProperties([
                    'store_id' => $store['store_id'],
                    'subscription_id' => $stripeSubscription,
                    'subscription_end_date' => $subscriptionCurrentPeriodEnd,
                ])
                ->log('Subscription expired for store. threshold');
            return response()->json(['message' => 'Threshold has expired'], 400);
        }

        if ($store->subscription->package['cost_per_query']) {
            try {
                $stripeSubscriptionId = $store->subscription['stripe_subscription_id'];
                $subscription = StripeService::getSubscription($stripeSubscriptionId);

                Subscription::update(
                    $stripeSubscriptionId,
                    [
                        'items' => [
                            [
                                'id' => $subscription['items']['data'][0]['id'],
                                'quantity' => $subscription['items']['data'][0]['quantity'] + 1,
                            ],
                        ],
                    ]
                );

            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to update subscription: ' . $e->getMessage()], 500);
            }
        } else {
            $queryLimit = $store->subscription->package['query_limit'];
            $queriesCount = Query::query()
                ->where('store_id', $store['store_id'])
                ->whereBetween('created_at', [$subscriptionCurrentPeriodStart, $subscriptionCurrentPeriodEnd])
                ->count();

            if ($queryLimit <= $queriesCount) {
                $user = $store->user();
                $renewalDate = Carbon::createFromFormat('Y-m-d', $store->subscription()->end_date)->format('F j, Y');
                Mail::to($user->email)->send(new QuotaLimitReachedMail($user, $store, $renewalDate));
                return response()->json(['message' => 'Threshold reached'], 400);
            }elseif (($queryLimit * 0.8)  <= $queriesCount) {
                $user = $store->user();
                $renewalDate = Carbon::createFromFormat('Y-m-d', $store->subscription()->end_date)->format('F j, Y');
                Mail::to($user->email)->send(new QuotaWarningMail($user, $store, $renewalDate));
            }
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

        // Check exceptions
        $status = 0;
        $exceptions = Exception::query()->where('store_id', $store['store_id']);
        $exceptionPhone = !empty($phoneHash) ? $exceptions->where('phone_hash', $phoneHash)->first() : null;
        $exceptionEmail = !empty($emailHash) ? $exceptions->where('email_hash', $emailHash)->first() : null;

        if ($exceptionEmail && $exceptionEmail['type'] === "allow") {
            $status = 1;
        }
        if ($exceptionPhone && $exceptionPhone['type'] === "allow") {
            $status = 1;
        }
        if ($exceptionEmail && $exceptionEmail['type'] === "deny") {
            $status = 0;
        }
        if ($exceptionPhone && $exceptionPhone['type'] === "deny") {
            $status = 0;
        }

        // Calculate reputation if no exceptions apply
        if ($status !== 1 && !($exceptionPhone && $exceptionPhone['type'] === "deny" ||
                $exceptionEmail && $exceptionEmail['type'] === "deny")) {
            $feedbacks = Feedback::query()
                ->orWhere(function ($query) use ($emailHash) {
                    if (!empty($emailHash)) {
                        return $query->where('email', '=', $emailHash);
                    }
                })
                ->orWhere(function ($query) use ($phoneHash) {
                    if (!empty($phoneHash)) {
                        return $query->where('phone', '=', $phoneHash);
                    }
                })
                ->get();

            // Calculate good and bad feedback counts
            if (!empty($feedbacks)) {
                $good = (int)$feedbacks->where('is_received', true)->count();
                $bad = (int)$feedbacks->where('is_received', false)->count();

                $reputation = $this->calculateReputation($good, $bad);

                $status = $reputation < $store['threshold'];
            }
        }


        Query::query()->create([
            'store_id' => $store['store_id'],
            'email' => $emailHash,
            'phone' => $phoneHash,
            'created_at' => Carbon::now(),
            'status' => $status
        ]);

        return response()->json([
            'threshold' => $reputation ?? -1,
            'status' => $status,
            'message' => 'Threshold API is working',
        ],200);
    }

    /**
     * Calculate reputation generation for an email address.
     */
    private function calculateReputation(int $good, int $bad): float
    {
        if ($good + $bad === 0) {
            return 0;
        }
        return round(($good - $bad) / ($good + $bad), 2);
    }

}
