<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  string  ...$roles  Allowed roles, e.g. middleware('role:admin') or role:admin,hr
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        $allowed = $this->normalizeRoles($roles);

        if (! in_array($request->user()->role, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }

    /**
     * @param  array<int, string>  $roles
     * @return array<int, string>
     */
    private function normalizeRoles(array $roles): array
    {
        if ($roles === []) {
            return [];
        }

        $flat = [];
        foreach ($roles as $part) {
            foreach (explode(',', $part) as $role) {
                $role = trim($role);
                if ($role !== '') {
                    $flat[] = $role;
                }
            }
        }

        return array_values(array_unique($flat));
    }
}
