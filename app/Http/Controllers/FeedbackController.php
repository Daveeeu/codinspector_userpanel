<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Services\StorePermissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Str;

class FeedbackController extends Controller
{
    protected $storePermissionService;

    public function __construct(StorePermissionService $storePermissionService)
    {
        $this->storePermissionService = $storePermissionService;
    }

    public function index()
    {
        $storesWithPermission = $this->storePermissionService->getStoresWithPermission('feedback-list');


            $startDate = Carbon::now()->subMonth()->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $feedbacks = Feedback::whereIn('store_id', $storesWithPermission->pluck('store_id'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->paginate(10);

            $feedbacks->getCollection()->transform(function ($feedback) {
                return [
                    'email' => Str::limit($feedback->email, 15, '...'),
                    'phone' => Str::limit($feedback->phone, 15, '...'),
                    'order_identifier' => $feedback->order_identifier,
                    'store_domain' => $feedback->store->domain,
                    'is_received' => $feedback->is_received,
                    'created_at' => Carbon::parse($feedback->created_at)->format('Y. m. d.'),
                ];
            });

            return view('feedback.index', compact('feedbacks', 'storesWithPermission'));
    }

    public function filter(Request $request)
    {
        $storesWithPermission = $this->storePermissionService->getStoresWithPermission('feedback-list');
        $storeIds = $storesWithPermission->pluck('store_id')->toArray();

        $query = Feedback::whereIn('store_id', $storeIds);

        if ($request->filled('order_identifier')) {
            $query->where('order_identifier', 'LIKE', '%' . $request->order_identifier . '%');
        }

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

        if ($request->filled('is_received')) {
            $query->where('is_received', $request->is_received);
        }

        if ($request->has('date_range')) {
            $dates = explode(' to ', $request->get('date_range'));
            if (count($dates) === 2) {
                $startDate = Carbon::parse($dates[0])->startOfDay();
                $endDate = Carbon::parse($dates[1])->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }


        $feedbacks = $query->paginate(10)->appends($request->except('page'));


        $feedbacks->getCollection()->transform(function ($feedback) {
            return [
                'email' => Str::limit($feedback->email, 15, '...'),
                'phone' => Str::limit($feedback->phone, 15, '...'),
                'order_identifier' => $feedback->order_identifier,
                'store_domain' => $feedback->store->domain,
                'is_received' => $feedback->is_received,
                'created_at' => Carbon::parse($feedback->created_at)->format('Y. m. d.'),
            ];
        });

        // Naplózás
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'filters' => $request->only([
                    'order_identifier', 'data', 'store_id', 'is_received', 'date_range',
                ]),
                'result_count' => $feedbacks->total(),
            ])
            ->log('Feedbacks filtered');

        return view('feedback.table', compact('feedbacks'));
    }
}
