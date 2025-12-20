<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
//     public function handle(Request $request, Closure $next, ...$roles): Response
// {
//     $userRole = $request->user()?->role;

//     if (in_array($userRole, $roles, true)) {
//         return $next($request);
//     }

//     return match ($userRole) {
//         'patient' => redirect()->route('pasien'),
//         'admin', 'doctor' => redirect()->route('dashboard'),
//         default => abort(403),
//     };
// }

public function handle(Request $request, Closure $next, ...$roles): Response
{
    $user = $request->user();
    if (!$user) {
        abort(401);
    }

    if (in_array($user->role, $roles)) {
        return $next($request);
    }

    abort(403);
}

}
