<?php

namespace Modules\Access\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()?->hasPermissionTo($permission)) {
            abort(403, 'Insufficient permission.');
        }

        return $next($request);
    }
}
