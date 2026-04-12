<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Fledoux\LaravelOAuth\OAuthController;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(Request $request): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request (traditional email/password login)
     */
    public function login(Request $request): RedirectResponse
    {
        Log::info('Login attempt', ['email' => $request->input('email')]);

        $validationRules = [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];

        // Add CAPTCHA validation if configured
        if (config('services.turnstile.secret_key')) {
            $validationRules['cf-turnstile-response'] = ['required', new \App\Rules\ValidTurnstile()];
        }

        $credentials = $request->validate($validationRules);

        // Check if user exists and has verified email
        $user = \App\Models\User::where('email', $credentials['email'])
                                ->whereNotNull('email_verified_at')
                                ->first();

        if (!$user) {
            return back()->withErrors(['email' => __('auth.email_not_verified')])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Log::info('Login successful', ['email' => $credentials['email']]);
            return redirect()->intended(route('dashboard'))->with('success', __('login.Welcome back!'));
        }

        Log::info('Login failed', ['email' => $credentials['email']]);
        return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
    }

    /**
     * Handle logout request
     * Directly calls the OIDC logout to preserve session data (access token)
     */
    public function logout(Request $request)
    {
        // Call OIDC logout directly without redirecting (to preserve session)
        return app(OAuthController::class)->logout($request);
    }
}
