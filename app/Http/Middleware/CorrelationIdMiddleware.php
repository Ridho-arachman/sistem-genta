<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CorrelationIdMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->header('X-Request-ID');

        if (! filled($correlationId)) {
            $correlationId = (string) Str::uuid();
        }

        // Tambah ke konteks log supaya setiap baris log dalam request ini
        // bisa ditemukan dengan correlation ID yang sama (termasuk queue job).
        Log::withContext(['correlation_id' => $correlationId]);

        $request->attributes->set('correlation_id', $correlationId);

        $response = $next($request);

        return $response->header('X-Request-ID', $correlationId);
    }
}
