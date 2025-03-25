<?php

    namespace App\Http\Controllers;

    use App\Services\DeleteStoreService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;

    class AccountController extends Controller
    {
        /**
         * Fiók beállítások megjelenítése.
         */
        public function showSettings()
        {
            return view('account.settings');
        }

        /**
         * Fiókadatok frissítése.
         */
        public function updateSettings(Request $request)
        {
            $user = Auth::user();

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone_number' => 'required|string|max:20',
                'password' => 'nullable|min:8|confirmed',
            ]);

            $originalData = $user->only(['first_name', 'last_name', 'email', 'phone_number', 'two_factor_enabled']);

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->two_factor_enabled = $request->has('2fa');

            $user->save();

            // Naplózás a Spatie activitylog használatával
            activity()
                ->causedBy($user) // Az aktuális felhasználó
                ->performedOn($user) // Az érintett modell
                ->withProperties([
                    'attributes' => $user->only(['first_name', 'last_name', 'email', 'phone_number', 'two_factor_enabled']), // Új értékek
                    'old' => $originalData, // Korábbi értékek
                ])
                ->log('User updated account settings');

            return redirect()->route('account.settings')->with('status', 'Adatok sikeresen frissítve.');
        }

// TODO, ha bekerül a forrás kezelő
        public function destroy()
        {
            $user = Auth::user();

            $deleteStoreService = new DeleteStoreService();
            foreach ($user->stores as $store) {
                $deleteStoreService->deleteStore($store->id, false);
            }

            $user->delete();

            return redirect()->route('login')->with('success', 'A fiókod sikeresen törölve lett.');
        }

    }
