<?php

namespace App\Services;

use App\Mail\DeletedStore;
use App\Models\Store;
use App\Models\UserNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Stripe\StripeService;
use Illuminate\Support\Facades\Log;
use Mail;


class DeleteStoreService
{
    public function deleteStore(int $storeId, bool $sendMail = true): bool
    {
        DB::beginTransaction();

        try {
            $store = Store::findOrFail($storeId);

            $subscription = $store->subscription;
            try {
                StripeService::cancelSubscription($subscription->stripe_subscription_id);
            }catch (Exception $e) {}


            foreach ($store->feedbacks as $feedback) {
                $feedback->update(
                    [
                        'store_id' => null,
                    ]
                );
            }

            foreach ($store->queries as $query) {
                $query->update(
                    [
                        'store_id' => null,
                    ]
                );
            }

            $deletedStore = $store->replicate();
            $user = $store->user;

            $store->delete();
            $subscription->delete();
            DB::commit();

            activity()
                ->performedOn($deletedStore)
                ->withProperties($deletedStore->toArray())
                ->log('Store deleted');


            if ($sendMail) {
                Mail::to(users: $user->email)->send(new DeletedStore($user, $deletedStore));

                UserNotification::create([
                    'user_id' => $user->id,
                    'event'   => 'store_deleted',
                    'deleted_store_domain' => $deletedStore->domain,
                ]);
            }

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Store deletion failed: ' . $e->getMessage(), [
                'store_id' => $storeId,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
