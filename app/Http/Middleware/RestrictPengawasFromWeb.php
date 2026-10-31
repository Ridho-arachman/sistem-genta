<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictPengawasFromWeb
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to authenticated users on web routes
        if ($request->user() && $request->user()->isPengawas()) {
            // Allow API routes (mobile app uses Sanctum)
            if ($request->is('api/*')) {
                return $next($request);
            }

            // Logout and redirect to login with message
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun pengawas hanya dapat diakses melalui aplikasi mobile.']);
        }

        return $next($request);
    }
}