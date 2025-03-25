<?php

    // app/Http/Controllers/StoreController.php
    namespace App\Http\Controllers;

    use App\Http\Requests\StoreFormRequest;
    use App\Models\Package;
    use App\Models\Store;
    use App\Models\BillingInfo;
    use App\Models\Platform;
    use App\Models\Subscription;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class StoreController extends Controller
    {

        public function index()
        {
            $user = Auth::user();
            if($user->stores->count() === 0) {
                return redirect()->route('store.create');
            }
            return view('store.index', compact('user'));
        }
        public function create()
        {
            $platforms = Platform::all();
            $subscriptions = Subscription::all();

            return view('store.create', compact('platforms', 'subscriptions'));
        }

        public function store(StoreFormRequest $request)
        {
            // Számlázási adatok létrehozása
            $billingInfo = BillingInfo::create([
                'user_id' => auth()->user()->id,
                'company_name' => $request->company_name,
                'tax_id' => $request->tax_id,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
            ]);

            // Webáruház hozzáadása
            $store = Store::create([
                'user_id' => auth()->user()->id,
                'platform_id' => $request->platform_id,
                'domain' => $request->domain,
                'lost_package_cost' => $request->lost_package_cost,
                'subscription_id' => $request->subscription_id,
                'billing_id' => $billingInfo->billing_id,
            ]);

            return redirect()->route('store.show', $store);
        }

        public function show(Store $store)
        {
            $platforms = Platform::all();
            $packages = Package::all();

            $id = $store->store_id;

            return view('store.details', compact('store', 'platforms', 'packages', 'id'));
        }


        public function update(Request $request, Store $store)
        {

            $id = $store->store_id;

            // Ellenőrizzük a bemeneti adatokat
            $validated = $request->validate([
                'platform' => 'required|exists:platforms,platform_id',
                'loss_unclaimed_package' => 'required|numeric|min:0',
            ]);

            // Adatok lekérdezése
            $store = Store::query()->where('store_id', $id)->firstOrFail();

            // Adatok frissítése
            $store->platform_id = $validated['platform'];
            $store->lost_package_cost = $validated['loss_unclaimed_package'];

            $store->save();

            // Átirányítás vissza a részletek oldalra üzenettel
            return redirect()->route('store.details', $id)->with('success', 'Adatok sikeresen frissítve!');
        }


        public function selectPackage(Store $store)
        {
            $packages = Package::all();
            $id = $store->store_id;
            return view('store.select-package', compact('packages', 'id'));
        }
    }
