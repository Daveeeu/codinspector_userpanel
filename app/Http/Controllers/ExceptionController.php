<?php

    namespace App\Http\Controllers;

    use App\Models\Exception;
    use App\Models\Feedback;
    use App\Models\Package;
    use App\Models\Store;
    use App\NormalizeData;
    use App\Services\StorePermissionService;
    use Carbon\Carbon;
    use http\Exception\InvalidArgumentException;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Spatie\Permission\Models\Role;

    class ExceptionController extends Controller
    {

        use NormalizeData;
        protected $storePermissionService;

        public function __construct(StorePermissionService $storePermissionService)
        {
            $this->storePermissionService = $storePermissionService;
        }

        public function index()
        {
            $storesWithPermission = $this->storePermissionService->getStoresWithPermission('add-exception');
            $storeIds = $storesWithPermission->pluck('store_id')->toArray();

            $exceptions = Exception::whereIn('store_id', $storeIds)->paginate(10);

            $exceptions->getCollection()->transform(function ($exception) {
                return [
                    'id'=> $exception->id,
                    'store_id' => $exception->store_id,
                    'email' => $exception->email_hash ? Str::limit($exception->email_hash, 15, '...') : 'Nincs adat',
                    'phone' => $exception->phone_hash ? Str::limit($exception->phone_hash, 15, '...') : 'Nincs adat',
                    'store_domain' => $exception->store->domain,
                    'status' => $exception->type === "allow",
                    'created_date' => Carbon::parse($exception->created_at)->format('Y. m. d.'),
                ];
            });

            return view('exceptions.index', compact('exceptions', 'storesWithPermission'));
        }

        public function store(Request $request)
        {

            $this->authorize('create', [Exception::class, $request->store_id]);

            // Validáció: legalább az egyik mező kötelező
            $validated = $request->validate([
                'store_id' => 'required|exists:stores,store_id',
                'email' => 'nullable|email',
                'phone' => ['nullable', 'regex:/^\+[1-9]\d{1,14}$/'],
                'type' => 'required|in:allow,deny',
            ]);

            // Ellenőrizzük, hogy legalább az egyik adat meg van-e adva
            if (!$validated['email'] && !$validated['phone']) {
                return redirect()->back()->withErrors(['error' => 'Legalább egy mezőt (e-mail vagy telefonszám) meg kell adni.']);
            }

            if ($validated['phone']) {
                $normalizedPhone = $this->normalizePhoneNumber($validated['phone']);
                $phoneHash = hash('sha256', $normalizedPhone);
            } else {
                $phoneHash = null;
            }

            if ($validated['email']) {
                $normalizedEmail = $this->normalizeEmail($validated['email']);
                $emailHash = hash('sha256', $normalizedEmail);
            } else {
                $emailHash = null;
            }

            // Ellenőrzés: létezik-e már az adott store-ban ez a kombináció?
            if ($emailHash && Exception::where('store_id', $validated['store_id'])->where('email_hash', $emailHash)->exists()) {
                return redirect()->back()->withErrors(['error' => 'Ez az e-mail cím már létezik ebben a Store-ban a kivételek között.']);
            }

            if ($phoneHash && Exception::where('store_id', $validated['store_id'])->where('phone_hash', $phoneHash)->exists()) {
                return redirect()->back()->withErrors(['error' => 'Ez a telefonszám már létezik ebben a Store-ban a kivételek között.']);
            }

            // Mentés csak a hash-ekkel és típussal
            $exception = Exception::create([
                'store_id' => $validated['store_id'],
                'email_hash' => $emailHash,
                'phone_hash' => $phoneHash,
                'type' => $validated['type'],
            ]);

            activity()
                ->causedBy(auth()->user()) // Az aktuális felhasználó
                ->performedOn($exception) // Az érintett modell
                ->withProperties([
                    'attributes' => $exception->toArray(),
                ])
                ->log('Exception added');
            return redirect()->route('exceptions.index')->with('success', 'Kivétel sikeresen hozzáadva!');
        }

        public function update(Request $request, Exception $exception)
        {
            // Validáció
            $validated = $request->validate([
                'store_id' => 'required|exists:stores,store_id',
                'type'     => 'required|in:allow,deny',
            ]);


            // Frissítjük a kivételt
            $exception->update([
                'store_id'    => $validated['store_id'],
                'type'        => $validated['type'],
            ]);

            // Naplózás
            activity()
                ->causedBy(auth()->user())
                ->performedOn($exception)
                ->withProperties(['attributes' => $exception->toArray()])
                ->log('Exception updated');

            return redirect()->route('exceptions.index')->with('success', 'A kivétel sikeresen módosítva lett.');
        }



        public function destroy(Exception $exception)
        {
            $exceptionData = $exception->toArray(); // Az adatok mentése a naplózáshoz

            $exception->delete();

            // Naplózás
            activity()
                ->causedBy(auth()->user()) // Az aktuális felhasználó
                ->withProperties([
                    'attributes' => $exceptionData, // Törölt adatok
                ])
                ->log('Exception deleted');

            return redirect()->route('exceptions.index')->with('success', 'A kivétel sikeresen törölve lett.');
        }


        public function filter(Request $request)
        {
            $storesWithPermission = $this->storePermissionService->getStoresWithPermission('add-exception');
            $storeIds = $storesWithPermission->pluck('store_id')->toArray();

            $query = Exception::whereIn('store_id', $storeIds);

            if ($request->filled('search_input')) {
                $query->where('phone_hash', 'LIKE', '%' . $request->search_input . '%')
                    ->orWhere('email_hash', 'LIKE', '%' . $request->search_input . '%');
            }

            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            if ($request->filled('type')) {

                $query->where('type', $request->type);
            }

            $exceptions = $query->paginate(10)->appends($request->except('page'));

            $exceptions->getCollection()->transform(function ($exception) {
                return [
                    'id'=> $exception->id,
                    'store_id' => $exception->store_id,
                    'email' => $exception->email_hash ? Str::limit($exception->email_hash, 15, '...') : 'Nincs adat',
                    'phone' => $exception->phone_hash ? Str::limit($exception->phone_hash, 15, '...') : 'Nincs adat',
                    'store_domain' => $exception->store->domain,
                    'status' => $exception->type === "allow",
                    'created_date' => Carbon::parse($exception->created_at)->format('Y. m. d.'),
                ];
            });


            // Naplózás
            activity()
                ->causedBy(auth()->user()) // Az aktuális felhasználó
                ->withProperties([
                    'filters' => $request->only([
                        'search_input', 'store_id', 'type'
                    ]),
                    'result_count' => $exceptions->total(), // Az eredmények száma
                ])
                ->log('Feedbacks filtered'); // Egyedi naplóüzenet
            return view('exceptions.table', compact('exceptions'));
        }


    }
