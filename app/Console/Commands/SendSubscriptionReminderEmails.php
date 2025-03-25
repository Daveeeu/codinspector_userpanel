<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminder;
use App\Models\Subscription;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendSubscriptionReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-subscription-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription reminder emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::where('end_date', '<=', now()->addDays(2))
        ->where('status', 'active')
        ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            $store = $subscription->store;
            $renewalDate = Carbon::createFromFormat('Y-m-d', $subscription->end_date)->format('F j, Y');


            if ($user && $store) {

                try {
                    Mail::to($user->email)->send(new SubscriptionReminder($user, $store, $renewalDate));

                    UserNotification::create([
                        'user_id' => $user->id,
                        'event'   => 'subscription_reminder',
                        'store_id' => $store->store_id,
                    ]);

                    activity()
                    ->causedByAnonymous()
                    ->performedOn($subscription)
                    ->withProperties([
                        'email' => $user->email,
                        'store' => $store,
                        'renewal_date' => $renewalDate,
                    ])
                    ->log('Subscription reminder email sent');

                } catch (\Exception $e) {
                    Log::error('Failed to send subscription reminder email', [
                        'email' => $user->email,
                        'subscription' => $subscription,
                        'store' => $store,
                        'renewal_date' => $renewalDate,
                        'error_message' => $e->getMessage(),
                    ]);

                    activity()
                        ->causedByAnonymous()
                        ->performedOn($subscription)
                        ->withProperties([
                            'email' => $user->email,
                            'subscription' => $subscription,
                            'store' => $store,
                            'renewal_date' => $renewalDate,
                            'error_message' => $e->getMessage(),
                            'failed_at' => now()->toDateTimeString(),
                        ])
                        ->log('Failed to send subscription reminder email');
                }

            }
        }
    }
}
