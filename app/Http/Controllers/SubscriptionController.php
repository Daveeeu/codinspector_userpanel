<?php

    namespace app\Http\Controllers;

    use App\Models\Subscription;
    use App\Services\StripeService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class SubscriptionController extends Controller
    {
        protected $stripeService;

        public function __construct(StripeService $stripeService)
        {
            $this->stripeService = $stripeService;
        }

        public function create(Request $request)
        {
            $user = Auth::user();

            // Stripe ügyfél létrehozása, ha még nincs
            if (!$user->stripe_customer_id) {
                $customer = $this->stripeService->createCustomer($user);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            // Előfizetés létrehozása
            $subscription = $this->stripeService->createSubscription(
                $user->stripe_customer_id,
                $request->price_id
            );

            Subscription::create([
                'user_id' => $user->id,
                'package_id' => $request->package_id,
                'stripe_subscription_id' => $subscription->id,
                'price' => $request->price,
                'payment_cycle' => 'monthly',
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'auto_renewal' => true,
                'status' => 'active',
            ]);

            return response()->json(['message' => 'Subscription created successfully']);
        }

        public function cancel($id)
        {
            $subscription = Subscription::findOrFail($id);

            $this->stripeService->cancelSubscription($subscription->stripe_subscription_id);

            $subscription->update(['status' => 'cancelled']);

            return response()->json(['message' => 'Subscription cancelled successfully']);
        }
    }
