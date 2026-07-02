<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Coarse route-group gate: may this role reach this route at all?
     * (Per-instance / list-filtering checks live in Policies and query scopes.)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $role = $user?->role;

        $roleValue = $role instanceof UserRole ? $role->value : $role;

        abort_unless(in_array($roleValue, $roles, true), 403);

        return $next($request);
    }
}
