<?php

namespace App\Console\Commands;

use App\Mail\ReferralSummary;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendReferralSummaryEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-referral-summary-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send referral summary emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();

        $subscriptions = Subscription::where('status', 'active')->get();

        foreach ($subscriptions as $subscription) {
            $startDate = $subscription->start_date;
            $daysSinceStart = abs($today->diffInDays($startDate));

            if ($daysSinceStart > 0 && $daysSinceStart % 30 == 0) {
                $cycleEndDate = $today->copy();
                $cycleStartDate = $today->copy()->subDays(30);

                $referrals = $this->getReferralsForUser($subscription->user_id, $cycleStartDate, $cycleEndDate);

                $user = $subscription->user;

                $validityDays = $user->partnerRequest->validity_days;

                if ($user) {

                    try{
                        Mail::to($user->email)->send(
                            new ReferralSummary($user, $cycleStartDate->toDateString(), $cycleEndDate->toDateString(), $referrals, $validityDays)
                        );


                        activity()
                        ->causedByAnonymous()
                        ->performedOn($subscription)
                        ->withProperties([
                            'email' => $user->email,
                            'cycle_start_date' => $cycleStartDate->toDateString(),
                            'cycle_end_date' => $cycleEndDate->toDateString(),
                            'referrals_count' => $referrals,
                            'validity_days' => $validityDays,
                        ])
                        ->log('Referral summary email sent successfully.');

                    }catch (\Exception $e) {
                        Log::error('Failed to send referral summary email', [
                            'email' => $user->email,
                            'subscription_id' => $subscription->id,
                            'cycle_start_date' => $cycleStartDate->toDateString(),
                            'cycle_end_date' => $cycleEndDate->toDateString(),
                            'referrals_count' => $referrals,
                            'validity_days' => $validityDays,
                            'error_message' => $e->getMessage(),
                        ]);

                        activity()
                            ->causedByAnonymous()
                            ->performedOn($subscription)
                            ->withProperties([
                                'email' => $user->email,
                                'subscription' => $subscription,
                                'cycle_start_date' => $cycleStartDate->toDateString(),
                                'cycle_end_date' => $cycleEndDate->toDateString(),
                                'referrals_count' => $referrals,
                                'validity_days' => $validityDays,
                                'error_message' => $e->getMessage(),
                                'failed_at' => now()->toDateTimeString(),
                            ])
                            ->log('Failed to send referral summary email.');
                    }

                }
            }
        }
    }

    private function getReferralsForUser($userId, $startDate, $endDate)
    {
        return DB::table('referrals')
            ->where('referrer_id', $userId)
            ->whereBetween('referral_date', [$startDate, $endDate])
            ->count();
    }
}
