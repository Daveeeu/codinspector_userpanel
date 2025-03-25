<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\DeleteStoreService;
use App\Stripe\StripeService;
use Illuminate\Console\Command;
use Stripe\Exception\OAuth\InvalidRequestException;

class CheckSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        StripeService::setApiKey(config('services.stripe.secret'));
        $deleteStoreService = new DeleteStoreService();
        $subscriptions = Subscription::query()->get();
            foreach ($subscriptions as $subscription) {
                try {
                    $stripeSubscription = StripeService::getSubscription($subscription->stripe_subscription_id);
                    if($stripeSubscription['canceled_at'] && !$subscription->store->billingInfo['next_package_id'] && $stripeSubscription['status'] !== "active") {
                        $deleteStoreService->deleteStore($subscription->store['store_id'], false);
                    }
                }catch(\Exception $e){
                    $deleteStoreService->deleteStore($subscription->store['store_id'], false);
                    echo $e->getMessage();
                }
            }
    }
}
