<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class IpWhitelist
{
    public function handle(Request $request, Closure $next)
    {
        // Fetch allowed IPs from .env file
        $allowedIps = array_filter(array_map('trim', explode(',', env('ALLOWED_IPS', ''))));

        // If no IPs are defined, deny by default (for safety)
        if (empty($allowedIps)) {
            abort(403, 'Access denied: no IPs configured.');
        }

        $clientIp = $request->ip();

        if (!IpUtils::checkIp($clientIp, $allowedIps)) {
            abort(403, "Access denied for IP: {$clientIp}");
        }

        return $next($request);
    }
}
