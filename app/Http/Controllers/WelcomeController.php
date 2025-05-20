<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WelcomeService;

class WelcomeController extends Controller
{
    protected $welcomeService;

    // Inject the service in the constructor
    public function __construct(WelcomeService $welcomeService)
    {
        $this->welcomeService = $welcomeService;
    }

    public function index()
    {
        if (auth()->check()) {
            return $this->redirectBasedOnRole(auth()->user());
        }

        // Now this will work because $welcomeService is properly initialized
        $viewData = $this->welcomeService->getWelcomePageData();

        return view('welcome', $viewData);
    }

    private function redirectBasedOnRole($user)
    {
        $roleRedirects = [
            'admin' => 'admin.pos.index',
            'owner' => 'owner.dashboard',
            'user' => 'users.dashboard',
            'photographer' => 'photographers.dashboard'
        ];

        if (isset($roleRedirects[$user->role])) {
            return redirect()->route($roleRedirects[$user->role]);
        }

        return redirect()->route('users.dashboard');
    }
}
