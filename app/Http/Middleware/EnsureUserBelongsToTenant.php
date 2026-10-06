<?php

namespace App\Http\Middleware;

use App\Enums\LoginPortal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only the current tenant's admins, managers and staff may use its pages.
 */
class EnsureUserBelongsToTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(LoginPortal::Tenant->allows($request->user()), Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
