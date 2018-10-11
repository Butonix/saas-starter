<?php

namespace App\Http\Middleware;

use Closure;
use Hyn\Tenancy\Environment;

class EnsureTenantExists
{
    public function handle($request, Closure $next)
    {
        if ($request->getHttpHost() === config('app.fqdn')) {
            return $next($request);
        }

        if ($tenant = app(Environment::class)->tenant()) {
            config(['app.url' => 'https://' . $tenant->hostnames->first()->fqdn]);
            return $next($request);
        }

        return redirect(config('app.url'));
    }
}
