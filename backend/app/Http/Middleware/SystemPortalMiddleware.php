<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemPortalMiddleware
{
    /**
     * @param  string  $portal  customer|admin|reception
     */
    public function handle(Request $request, Closure $next, string $portal): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        $role = $request->user()->loadMissing('systemRole')->systemRole;
        if ($role === null) {
            abort(403);
        }

        return match ($portal) {
            'customer' => $role->is_customer ? $next($request) : abort(403),
            'admin' => $role->can_access_admin ? $next($request) : abort(403),
            'reception' => $role->can_access_reception ? $next($request) : abort(403),
            default => abort(403),
        };
    }
}
