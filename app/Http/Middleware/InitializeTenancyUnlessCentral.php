<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's routes are shared by the central and tenant domains. This initializes
 * tenancy on tenant domains and leaves central requests untouched, so one login
 * pipeline can serve both.
 */
class InitializeTenancyUnlessCentral
{
    public function __construct(private InitializeTenancyByDomain $initializeTenancy) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            return $next($request);
        }

        return $this->initializeTenancy->handle($request, $next);
    }
}
