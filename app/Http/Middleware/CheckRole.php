<?php
// File: app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
public function handle(Request $request, Closure $next, ...$roles)
{
    // Jika tidak login, arahkan ke halaman login
    if (!Auth::check()) {
        return redirect('login');
    }

    $user = Auth::user();

    // Periksa apakah user memiliki role yang sesuai
    foreach ($roles as $role) {
        if ($user->role == $role) {
            return $next($request);
        }
    }

    // Redirect berdasarkan role jika tidak sesuai
    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses');
        case 'owner':
            return redirect()->route('owner.dashboard')->with('error', 'Anda tidak memiliki akses');
        case 'photographer':
            return redirect()->route('photographers.dashboard')->with('error', 'Anda tidak memiliki akses');
        default:
            return redirect()->route('welcome')->with('error', 'Anda tidak memiliki akses');
    }
}
}
