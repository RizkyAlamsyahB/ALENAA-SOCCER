<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        session(['user_role' => $user->role]);

        // Define a role-to-dashboard mapping
        $dashboards = [
            'admin' => 'admin.dashboard',
            'owner' => 'owner.dashboard',
            'photographer' => 'photographers.dashboard',
            'user' => 'users.dashboard',
        ];

        // Redirect to appropriate dashboard or default
        $route = $dashboards[$user->role] ?? 'home';
        return redirect()->intended(route($route));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
