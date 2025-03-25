<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function show()
    {
        $user = auth()->user(); // Feltételezzük, hogy a felhasználó be van jelentkezve
        return view('referral', ['user' => $user]);
    }

    public function generate(Request $request)
    {
        $user = auth()->user();

        // Ha már van referral_code, nem generál új kódot
        if (!$user->referral_code) {
            $user->referral_code = strtoupper(Str::random(10));
            $user->save();
        }

        return redirect()->route('referral.show')->with('success', 'Az ajánlói kód sikeresen létrehozva!');
    }
}
