<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        $loggedUser = Auth::user();
        $allowedRoles = explode(',', $roles);
        $allowedEnums = array_map(fn($role) => UserRole::tryFrom(strtoupper(trim($role))), $allowedRoles);
        $allowedEnums = array_filter($allowedEnums);
        if (!$loggedUser || !in_array($loggedUser->role, $allowedEnums, true)) {
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }
        return $next($request);
    }
}
