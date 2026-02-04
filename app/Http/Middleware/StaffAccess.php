<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $staff = session('current_staff');
        $position = data_get($staff, 'position');

        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        if ($staff && $position) {
            if (empty($roles) || in_array($position, $roles)) {
                return $next($request);
            }
            return abort(403, "Akses ditolak untuk posisi: $position");
        }

        return redirect()->route('role-login');
    }
}
