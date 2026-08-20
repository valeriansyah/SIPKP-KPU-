<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return abort(401, 'Unauthorized');
        }

        $user = Auth::user();

        // Convert the user's role name to snake case to match the parameter
        // For example: "Sub Operator" -> "sub_operator", "Pelapor" -> "pelapor"
        $userRoleStr = Str::slug($user->role->role_name, '_');
        $expectedRoleStr = Str::slug($role, '_');

        if ($userRoleStr !== $expectedRoleStr) {
            return abort(403, 'Forbidden: Role not authorized.');
        }

        return $next($request);
    }
}
