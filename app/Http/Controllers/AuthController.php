<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoUsers = User::with('roles')->where('is_active', true)->take(6)->get();

        return view('auth.login', compact('demoUsers'));
    }

    public function login(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is deactivated. Please contact the administrator.',
                ]);
            }

            // Initialize active company and project
            $defaultCompany = $user->companies()->first() ?? \App\Models\Company::first();
            if ($defaultCompany) {
                $tenantManager->setCompany($defaultCompany);
            }

            if ($user->current_project_id) {
                $tenantManager->setProject($user->current_project_id);
            }

            // Set locale
            $locale = $user->preferred_locale && $user->preferred_locale !== 'bn' ? $user->preferred_locale : 'en';
            $request->session()->put('locale', $locale);

            // Record audit log
            AuditLog::create([
                'company_id' => $tenantManager->currentCompanyId(),
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'login',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'description' => "User {$user->name} logged in successfully.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::create([
                'company_id' => $tenantManager->currentCompanyId(),
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'logout',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'description' => "User {$user->name} logged out.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
