<?php

namespace App\Http\Controllers;

use App\Models\PartnerRequest;
use Illuminate\Http\Request;

class PartnerProgramController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $partnerRequest = PartnerRequest::where('user_id', $user->id)->first();
        return view('partner_program.index', compact('partnerRequest'));
    }

    // TODO FIZETÉSNÉL LEVONÁSA, kupon létrehozás (stripe)

    public function store(Request $request)
    {
        $user = auth()->user();

        // Ellenőrizzük, hogy már létezik-e kérés
        if (PartnerRequest::where('user_id', $user->id)->exists()) {
            // Log activity for duplicate request attempt
            activity()
                ->causedBy($user)
                ->withProperties([
                    'action' => 'duplicate_request_attempt',
                ])
                ->log('User attempted to request partner program again');

            return redirect()->back()->with('error', 'Már igényelted a partner programot.');
        }

        // Létrehozunk egy új kérelmet
        PartnerRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Log activity for new partner program request
        activity()
            ->causedBy($user)
            ->withProperties([
                'request_status' => 'pending',
            ])
            ->log('User requested partner program');

        return redirect()->back()->with('success', 'A partner program iránti kérelmed elküldve!');
    }

}
