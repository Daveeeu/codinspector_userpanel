<?php

namespace App\Console\Commands;

use App\Mail\MonthlyStatistics;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMonthlyStatisticsEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-monthly-statistics-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send monthly statistics emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cycleStartDate = now()->subMonth()->startOfMonth();
        $cycleEndDate = now()->subMonth()->endOfMonth();

        $users = User::with(['stores.feedbacks', 'stores.queries', 'stores.subscription.package'])->get();

        foreach ($users as $user) {
            // Fetch stores for the user and calculate statistics
            $stores = $user->stores->map(function ($store) use ($cycleStartDate, $cycleEndDate) {
                $totalFeedbacks = $store->feedbacks
                    ->whereBetween('created_at', [$cycleStartDate, $cycleEndDate])
                    ->count();

                $totalQueries = $store->queries
                    ->whereBetween('created_at', [$cycleStartDate, $cycleEndDate])
                    ->count();

                $isReceivedCount = $store->feedbacks
                    ->where('is_received', 1)
                    ->whereBetween('created_at', [$cycleStartDate, $cycleEndDate])
                    ->count();

                $isNotReceivedCount = $store->feedbacks
                    ->where('is_received', 0)
                    ->whereBetween('created_at', [$cycleStartDate, $cycleEndDate])
                    ->count();

                $blockedOrder = $store->queries
                    ->where('status', 0)
                    ->whereBetween('created_at', [$cycleStartDate, $cycleEndDate])
                    ->count();

                $blockedOrders = $store->queries
                    ->where('status', 0)
                    ->whereBetween('created_at', [$cycleStartDate, $cycleEndDate]);

                $lostPackageCost = $store->lost_package_cost;
                $packageCost = $store->subscription->package->cost;

                $totalCost = $blockedOrders->count() * $lostPackageCost;
                $profit = $totalCost - $packageCost;

                return [
                    'domain' => $store->domain,
                    'profit' => $profit,
                    'orders' => $totalFeedbacks + $blockedOrder,
                    'blocked' => $blockedOrder,
                    'received' => $isReceivedCount,
                    'non_received' => $isNotReceivedCount,
                    'feedbacks' => $totalFeedbacks,
                    'queries' => $totalQueries,
                ];
            })->toArray();

            try {
                Mail::to($user->email)->send(new MonthlyStatistics($user, $cycleStartDate, $cycleEndDate, $stores));


                activity()
                    ->causedByAnonymous()
                    ->performedOn($user)
                    ->withProperties([
                        'email' => $user->email,
                        'cycle_start_date' => $cycleStartDate->toDateString(),
                        'cycle_end_date' => $cycleEndDate->toDateString(),
                        'stores' => $stores,
                    ])
                    ->log('Monthly statistics email sent successfully.');

            } catch (\Exception $e) {
                Log::error('Failed to send monthly statistics email', [
                    'email' => $user->email,
                    'error_message' => $e->getMessage(),
                ]);

                activity()
                    ->causedByAnonymous()
                    ->performedOn($user)
                    ->withProperties([
                        'email' => $user->email,
                        'cycle_start_date' => $cycleStartDate->toDateString(),
                        'cycle_end_date' => $cycleEndDate->toDateString(),
                        'stores' => $stores,
                        'error_message' => $e->getMessage(),
                        'failed_at' => now()->toDateTimeString(),
                    ])
                    ->log('Failed to send monthly statistics email.');
            }
        }
    }
}
