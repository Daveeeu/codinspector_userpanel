<?php

namespace App\Console\Commands;

use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSubscriptionOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscription-old-data';

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
        $threeYearsAgo = Carbon::now()->subYears(3);

        Feedback::query()
            ->whereNull('store_id')
            ->where('created_at', '<', $threeYearsAgo)
            ->delete();
    }
}
