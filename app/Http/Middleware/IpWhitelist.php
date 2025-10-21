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

        // if (!IpUtils::checkIp($clientIp, $allowedIps)) {
        //     abort(403, "Access denied for IP: {$clientIp}");
        // }


        if (!IpUtils::checkIp($clientIp, $allowedIps)) {
                $html = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Access Denied</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        height: 100vh;
                        background-color: #f8f9fa;
                        font-family: 'Inter', sans-serif;
                    }
                    .card {
                        border-radius: 1rem;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                        max-width: 500px;
                    }
                </style>
            </head>
            <body>
                <div class="card p-4 text-center">
                    <h3 class="text-danger mb-3">Access Denied</h3>
                    <p class="text-muted mb-4">Your IP address is not allowed to access this application.</p>

                    <div class="input-group mb-3">
                        <input type="text" id="ipAddress" class="form-control text-center" value="{$clientIp}" readonly>
                        <button class="btn btn-outline-primary" id="copyBtn">Copy</button>
                    </div>

                    <small class="text-secondary d-block">Copy your IP and contact the administrator to whitelist it.</small>
                </div>

                <script>
                    document.getElementById('copyBtn').addEventListener('click', function() {
                        const ipField = document.getElementById('ipAddress');
                        navigator.clipboard.writeText(ipField.value)
                            .then(() => {
                                this.textContent = "Copied!";
                                this.classList.remove('btn-outline-primary');
                                this.classList.add('btn-success');
                                setTimeout(() => {
                                    this.textContent = "Copy";
                                    this.classList.remove('btn-success');
                                    this.classList.add('btn-outline-primary');
                                }, 2000);
                            });
                    });
                </script>
            </body>
            </html>
            HTML;

                return response($html, 403);
        }

        return $next($request);
    }
}
