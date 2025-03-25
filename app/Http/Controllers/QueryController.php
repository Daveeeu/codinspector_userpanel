<?php

namespace App\Http\Controllers;

use App\Models\Query;
use App\Services\StorePermissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QueryController extends Controller
{

    protected $storePermissionService;

    public function __construct(StorePermissionService $storePermissionService)
    {
        $this->storePermissionService = $storePermissionService;
    }

    public function index()
    {

        $storesWithPermission = $this->storePermissionService->getStoresWithPermission('query-list');

            $startDate = Carbon::now()->subMonth()->startOfDay();
            $endDate = Carbon::now()->endOfDay();


            $queries = Query::whereIn('store_id', $storesWithPermission->pluck('store_id'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->paginate(10);

            $queries->getCollection()->transform(function ($query) {
                return [
                    'email' => Str::limit($query->email, 15, '...'),
                    'phone' => Str::limit($query->phone, 15, '...'),
                    'store_domain' => $query->store->domain,
                    'status' => $query->status,
                    'created_at' => Carbon::parse($query->created_at)->format('Y. m. d.'),
                ];
            });

            return view('queries.index', compact( 'queries' ,'storesWithPermission'));
    }

    public function filter(Request $request)
    {
        $storesWithPermission = $this->storePermissionService->getStoresWithPermission('query-list');
        $storeIds = $storesWithPermission->pluck('store_id')->toArray();

        $query = Query::whereIn('store_id', $storeIds);

        if ($request->filled('data')) {
            $hashedData = hash('sha256',$request->data);

            $query->where(function($q) use ($hashedData) {
                $q->where('email', $hashedData)
                  ->orWhere('phone', $hashedData);
            });
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_range')) {
            $dates = explode(' to ', $request->get('date_range'));
            if (count($dates) === 2) {
                $startDate = Carbon::parse($dates[0])->startOfDay();
                $endDate = Carbon::parse($dates[1])->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        $queries = $query->paginate(10)->appends($request->except('page'));

        $queries->getCollection()->transform(function ($query) {
            return [
                'email' => Str::limit($query->email, 20, '...'),
                'phone' => Str::limit($query->phone, 15, '...'),
                'store_domain' => $query->store->domain,
                'status' => $query->status,
                'created_at' => Carbon::parse($query->created_at)->format('Y. m. d.'),
            ];
        });

        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'filters' => $request->only([
                    'data', 'store_id', 'status', 'date_range',
                ]),
                'result_count' => $queries->total(),
            ])
            ->log('User filtered queries');

        return view('queries.table', compact('queries'));
    }
}
