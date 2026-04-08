<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Fledoux\LaravelSSO\Services\SSOAuditService;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(Request $request): View
    {
        Log::info('1/3 Login page', [
            'ip' => $request->ip()
        ]);
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request, SSOAuditService $auditService): RedirectResponse
    {
        Log::info('2/3 Tentative login', [
            'ip' => $request->ip(),
            'email' => $request->input('email'),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Vérifier d'abord si l'utilisateur existe et a un email vérifié
        $user = \App\Models\User::where('email', $credentials['email'])
                                ->whereNotNull('email_verified_at')
                                ->first();

        if (!$user) {
            // L'utilisateur n'existe pas ou email non vérifié
            $auditService->logAuthentication($credentials['email'], 'native', false, 'email_not_verified');
            
            return back()->withErrors([
                'email' => __('auth.email_not_verified'),
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            Log::info('3/3 Successful login', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
            ]);

            // Log successful native authentication
            $auditService->logAuthentication($credentials['email'], 'native', true);

            return redirect()->intended(route('dashboard'))->with('success', __('login.Welcome back!'));
        } else {

            // Envoyer une alerte par email en cas de tentative de connexion échouée
            //Mail::to('fledoux@yellowcactus.com')->send(new \App\Mail\globalMail('Failed login', $request->ip() . ' - ' . $request->input('email')));

            Log::info('Failed login', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
            ]);

            // Log failed authentication
            $auditService->logAuthentication($credentials['email'], 'native', false, 'invalid_credentials');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email')->with('error', __('login.Your credentials are not recognized.'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request, SSOAuditService $auditService): RedirectResponse
    {
        $user = Auth::user();
        
        if ($user) {
            $auditService->logAuthentication($user->email, 'native', true, 'logout');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Logout', [
            'ip' => $request->ip()
        ]);

        return redirect()->route('home')->with('success', __('login.Logout successful'));
    }
}
