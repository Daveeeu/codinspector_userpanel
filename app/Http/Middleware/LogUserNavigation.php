<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserNavigation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // A felhasználó aktuális URL-je (ahonnan navigál)
        $fromUrl = url()->previous();
        // A felhasználó cél URL-je (ahova navigál)
        $toUrl = $request->fullUrl();

        // Ha az URL változatlan, ne logoljunk
        if ($fromUrl !== $toUrl) {
            activity()
                ->causedBy(Auth::user()) // A felhasználó (ha Auth alatt van)
                ->withProperties([
                    'from' => $fromUrl,
                    'to' => $toUrl,
                ])
                ->log('User navigated');
        }
        return $next($request);
    }
}
