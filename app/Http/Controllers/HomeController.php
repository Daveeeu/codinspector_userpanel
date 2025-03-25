<?php

namespace App\Http\Controllers;

use App\Models\Query;
use App\Models\User;
use App\Services\StorePermissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    protected $storePermissionService;

    public function __construct(StorePermissionService $storePermissionService)
    {
        $this->storePermissionService = $storePermissionService;
    }
    public function index()
    {
        $userId = Auth::id();

        $storesWithPermission = $this->storePermissionService->getStoresWithPermission('manual-query');

        $user = $this->loadUserWithRelations($userId, $storesWithPermission->pluck('store_id')->toArray());


        $statistics = $this->calculateStatistics($user);
        $profitData = $this->calculateProfits($user);
        $monthlyData = $this->getMonthlyFeedbackStatistics($user);
        $usageData = $this->calculateUsageStatistics($user, $storesWithPermission->pluck('store_id')->toArray());

        $startDates = $storesWithPermission->map(function($store) {
            return Carbon::parse($store->subscription->start_date);
        });

        $minStartDate = $startDates->isEmpty() ? Carbon::now() : $startDates->min();

        $minStartDate = $minStartDate->copy()->startOfMonth();

        $currentDate = Carbon::now()->startOfMonth();

        $monthsSelect = [];
        $date = $minStartDate->copy();
        while ($date->lte($currentDate)) {
            $key = $date->format('Y-m');
            $value = $date->translatedFormat('Y. F');
            $monthsSelect[$key] = $value;
            $date->addMonth();
        }


        return view('statistics.index', array_merge(
            $statistics,
            $profitData,
            $monthlyData,
            $usageData,
            [
                "storesWithPermission" => $storesWithPermission->toArray(),
                "monthsSelect" => $monthsSelect,
            ]
        ));
    }




    public function updateCharts(Request $request)
    {
        $storeId = $request->input('store_id');
        $month = $request->input('month');
        $userId = Auth::id();

        if($storeId === 'all'){
            $storesWithPermission = $this->storePermissionService->getStoresWithPermission('manual-query');
            $storeIdArray = $storesWithPermission->pluck('store_id')->toArray();
        }else{
            $storeIdArray=[$storeId];
        }

        // Fetch data based on selected store
        $user = $this->loadUserWithRelations($userId, $storeIdArray);


        if($month && $month !== 'all'){
            $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
            $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();


            $statistics = $this->calculateStatistics($user, $startOfMonth, $endOfMonth);
            $profitData = $this->calculateProfits($user, $startOfMonth, $endOfMonth);
            $monthlyData = $this->getMonthlyFeedbackStatistics($user, $startOfMonth, $endOfMonth);
            $usageData = $this->calculateUsageStatistics($user, $storeIdArray, $startOfMonth, $endOfMonth);
        }else{
            $statistics = $this->calculateStatistics($user);
            $profitData = $this->calculateProfits($user);
            $monthlyData = $this->getMonthlyFeedbackStatistics($user);
            $usageData = $this->calculateUsageStatistics($user, $storeIdArray);
        }


        return response()->json(array_merge(
            $statistics,
            $profitData,
            $monthlyData,
            $usageData
        ));
    }


    private function loadUserWithRelations($userId, $storesWithPermission)
    {
        return User::with([
            'stores' => function($query) use ($storesWithPermission) {
                $query->whereIn('store_id', $storesWithPermission);
            },
            'stores.feedbacks' => fn($query) => $query->select('id', 'store_id', 'is_received', 'created_at'),
            'stores.queries' => fn($query) => $query->select('id', 'store_id', 'status', 'created_at'),
            'stores.subscription.package' => fn($query) => $query->select('package_id', 'cost', 'query_limit')
        ])->findOrFail($userId);
    }
    private function calculateStatistics($user, $start = null, $end = null)
    {

        $totalFeedbacks = $user->stores->sum(function($store) use ($start, $end) {
            return $this->filterByDate($store->feedbacks, 'created_at', $start, $end)->count();
        });

        $totalQueries = $user->stores->sum(function($store) use ($start, $end) {
            return $this->filterByDate($store->queries, 'created_at', $start, $end)->count();
        });

        $isReceivedCount = $user->stores->sum(function($store) use ($start, $end) {
            return $this->filterByDate($store->feedbacks, 'created_at', $start, $end)
                        ->where('is_received', 1)
                        ->count();
        });

        $isNotReceivedCount = $user->stores->sum(function($store) use ($start, $end) {
            return $this->filterByDate($store->feedbacks, 'created_at', $start, $end)
                        ->where('is_received', 0)
                        ->count();
        });

        $blockedOrder = $user->stores->sum(function($store) use ($start, $end) {
            return $this->filterByDate($store->queries, 'created_at', $start, $end)
                        ->where('status', 0)
                        ->count();
        });

        return compact('totalFeedbacks', 'totalQueries', 'isReceivedCount', 'isNotReceivedCount', 'blockedOrder');
    }

    private function calculateProfits($user, $start = null, $end = null)
    {
        // if($start && $end){
        //   //TODO: Implement this
        // }else {
            $monthlyProfits = $this->calculateMonthlyProfits($user);
            $averageProfit = $this->calculateAverageProfit($monthlyProfits);

            return compact('monthlyProfits', 'averageProfit');
        // }
    }

    private function calculateMonthlyProfits($user)
    {
        $monthlyProfits = [];
        $currentDate = Carbon::now();

        foreach ($user->stores as $store) {
            $blockedOrders = $store->queries->where('status', 0);
            $lostPackageCost = $store->lost_package_cost;
            $monthlyCost = $store->subscription->package->cost;

            $storeStartDate = Carbon::parse($store->subscription->start_date)->startOfMonth();
            $storeEndDate = $currentDate->copy()->endOfMonth();

            for ($month = $storeStartDate->copy(); $month->lessThanOrEqualTo($storeEndDate); $month->addMonth()) {
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();

                $monthlyBlockedOrders = $blockedOrders->filter(
                    fn($query) => Carbon::parse($query->created_at)->between($monthStart, $monthEnd)
                )->count();

                $formattedMonth = $monthStart->format('Y-m');

                if (!isset($monthlyProfits[$formattedMonth])) {
                    $monthlyProfits[$formattedMonth] = 0;
                }

                $monthlyProfits[$formattedMonth] += ($monthlyBlockedOrders * $lostPackageCost) - $monthlyCost;
            }
        }

        ksort($monthlyProfits);

        return $monthlyProfits;
    }

    private function calculateAverageProfit(array $monthlyProfits): float
    {
        if (empty($monthlyProfits)) {
            return 0.0;
        }

        $totalProfit = array_sum($monthlyProfits);
        $monthsCount = count($monthlyProfits);

        return round($totalProfit / $monthsCount, 2);
    }


    private function getMonthlyFeedbackStatistics($user, $start = null, $end = null)
    {
        $monthlyFeedbacks = $user->stores->flatMap(fn($store) => $store->feedbacks)
            ->groupBy(fn($feedback) => Carbon::parse($feedback->created_at)->format('Y-m'))
            ->map(fn($feedbacks) => $feedbacks->count())
            ->toArray();

        $monthlyData = $user->stores->flatMap(fn($store) => $store->feedbacks)
            ->groupBy(fn($feedback) => Carbon::parse($feedback->created_at)->format('Y-m'))
            ->map(function ($feedbacks) {
                return [
                    'is_delivered' => $feedbacks->filter(fn($feedback) => $feedback->is_received == 1)->count(),
                    'is_not_delivered' => $feedbacks->filter(fn($feedback) => $feedback->is_received == 0)->count(),
                ];
            })->toArray();

        ksort($monthlyFeedbacks);

        return compact('monthlyFeedbacks', 'monthlyData');
    }



    private function calculateUsageStatistics($user, $storesWithPermission, $start = null, $end = null)
    {
        $queryCountsByMonth = Query::whereIn('store_id', $storesWithPermission)
        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
        ->groupBy('month')
        ->pluck('total', 'month');

        $totalQueryLimit = $user->stores->sum(fn($store) => $store->subscription->package->query_limit);

        $monthlyUsagePercentage = $queryCountsByMonth->map(function ($count) use ($totalQueryLimit) {
            return $totalQueryLimit > 0 ? round(($count / $totalQueryLimit) * 100, 2) : 0;
        });

        $averageQueriesPerMonth = $queryCountsByMonth->isEmpty() ? 0 : round($queryCountsByMonth->avg());

        $averageUsagePercentage = $monthlyUsagePercentage->isEmpty() ? 0 : round($monthlyUsagePercentage->avg(), 2);

        return compact( 'averageQueriesPerMonth', 'averageUsagePercentage');
    }

    private function filterByDate(Collection $collection, string $dateField = 'created_at', Carbon $start = null, Carbon $end = null)
    {
        if ($start && $end) {
            return $collection->filter(function($item) use ($dateField, $start, $end) {
                return Carbon::parse($item->{$dateField})->between($start, $end);
            });
        }
        return $collection;
    }


}
