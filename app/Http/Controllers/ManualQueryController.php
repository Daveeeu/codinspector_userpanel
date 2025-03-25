<?php

    namespace App\Http\Controllers;

    use App\Models\Exception;
    use App\Models\Feedback;
    use App\Models\Store;
    use App\NormalizeData;
    use App\Services\StorePermissionService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;

    class ManualQueryController extends Controller
    {
        use NormalizeData;
        private $thresholds = [
            'nincs szűrés' => 0,
            'elnéző' => 0.5,
            'engedékeny' => 0.7,
            'szigorú' => 0.9,
            'nagyon szigorú' => 0.95,
            'egyedi' => null,
        ];

        protected $storePermissionService;

        public function __construct(StorePermissionService $storePermissionService)
        {
            $this->storePermissionService = $storePermissionService;
        }

        /**
         * Index method for manual query.
         */
        public function index()
        {
            $storesWithPermission = $this->storePermissionService->getStoresWithPermission('manual-query');

            return view('manual_query.index', compact('storesWithPermission'));
        }

        /**
         * Checks emails based on the given threshold.
         */
        public function checkEmails(Request $request)
        {
            // Validation
            $request->validate([
                'data' => 'required|string',
                'threshold' => 'required|string|in:nincs szűrés,elnéző,engedékeny,szigorú,nagyon szigorú,egyedi',
            ]);

            // Process data
            $dataList = array_filter(array_map('trim', explode("\n", $request->input('data'))));
            $dataList = array_unique($dataList);
            $threshold = $request->input('threshold');
            $customThreshold = $request->input('custom_threshold', null);

            if (count($dataList) > 150) {
                return back()->withErrors(['data' => 'Maximum 150 e-mail címet vagy telefonszámot adhatsz meg.']);
            }

            // Handle custom threshold
            if ($threshold === 'egyedi') {
                if ($customThreshold === null || !is_numeric($customThreshold) || $customThreshold < 0 || $customThreshold > 1) {
                    return back()->withErrors(['custom_threshold' => 'Az egyedi küszöbértéknek 0 és 1 között kell lennie.'])->with('threshold', 'Egyedi');
                }
                $this->thresholds['egyedi'] = floatval($customThreshold);
            }

            // Generate results
            $results = [];
            foreach ($dataList as $data) {
                if (!$this->isValidEmail($data) && !$this->isValidPhoneNumber($data)) {
                    $results[] = [
                        'data' => $data,
                        'reputation' => null,
                        'percentage' => null,
                        'delivery_rate' => null,
                        'isAboveThreshold' => null,
                        "error" => true,
                    ];
                    continue;
                }

                $normalizedData = null;
                if ($this->isValidEmail($data)) {
                    $normalizedData = $this->normalizeEmail($data);
                }

                if($this->isValidPhoneNumber($data)){
                    $normalizedData = $this->normalizePhoneNumber($data);
                }
                $normalizedDataHash = hash('sha256', $normalizedData);

                $feedbacks = Feedback::query()
                    ->where('email', $normalizedDataHash)
                    ->orWhere('phone', $normalizedDataHash)
                    ->get();


                $good = $feedbacks->where('is_received', true)->count();
                $bad = $feedbacks->where('is_received', false)->count();


                $reputation = $this->calculateReputation($good, $bad);
                $deliveryRate = $this->calculateDeliveryRate($good, $bad);

                $results[] = [
                    'data' => $data,
                    'reputation' => number_format($reputation, 2),
                    'delivery_rate' => $deliveryRate,
                    'isAboveThreshold' => $reputation > $this->thresholds[$threshold],
                    'hash' => $normalizedDataHash,
                    'accepted' => $good,
                    'rejected' => $bad,
                    "error" => false,
                ];
            }

            activity()
                ->causedBy(auth()->user()) // A műveletet végző felhasználó
                ->withProperties([
                    'input_data_count' => count($dataList), // Az elemzett adatok száma
                    'threshold' => $threshold, // A küszöbérték típusa
                    'custom_threshold' => $customThreshold, // Egyedi küszöbérték, ha van
                    'results' => array_map(function ($result) {
                        return [
                            'data' => $result['data'],
                            'reputation' => $result['reputation'],
                            'isAboveThreshold' => $result['isAboveThreshold'],
                            'error' => $result['error'],
                        ];
                    }, $results), // Az eredmények
                ])
                ->log('Email/phone reputation check performed');
            $request->session()->put('form_data', $request->input('data'));

            return redirect()->route('manual-query.index')
                ->with([
                    'results' => $results,
                    'totalEmails' => count($dataList),
                    'threshold' => ucfirst($threshold),
                    'thresholdValue' => $this->thresholds[$threshold],
                ]);
        }

        /**
         * Validates email format.
         */
        private function isValidEmail($email): bool
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        }

        /**
         * Validates email format.
         */
        private function isValidPhoneNumber($phoneNumber): bool
        {
            return preg_match('/^\+?[0-9]{7,15}$/', $phoneNumber) === 1;
        }


        /**
         * Calculate reputation generation for an email address.
         */
        private function calculateReputation(int $good, int $bad): float
        {
            if ($good + $bad === 0) {
                return 0;
            }
            return abs(round(($good - $bad) / ($good + $bad), 2));
        }

        /**
         * Átvételi arány kiszámítása.
         *
         * @param int $accepted Átvett csomagok száma.
         * @param int $rejected Nem átvett csomagok száma.
         * @return float Az átvételi arány százalékban kifejezve.
         */
        function calculateDeliveryRate(int $accepted, int $rejected): float
        {
            $total = $accepted + $rejected;

            // Ha nincs adat, az arány 0%
            if ($total === 0) {
                return 0.0;
            }

            // Átvételi arány kiszámítása
            return round(($accepted / $total) * 100, 2);
        }
    }
