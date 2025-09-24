<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(Request $request): View
    {
        Mail::to('fledoux@yellowcactus.com')->send(new \App\Mail\globalMail('Titre', 'Contenu du message'));

        Log::info('1/3 Login page', [
            'ip' => $request->ip()
        ]);
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request): RedirectResponse
    {
        Log::info('2/3 Tentative login', [
            'ip' => $request->ip(),
            'email' => $request->input('email'),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            Log::info('3/3 Successful login', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
            ]);

            return redirect()->intended(route('dashboard'))->with('success', __('login.Welcome back!'));
        } else {
            Log::info('Failed login', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
            ]);
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email')->with('error', __('login.Your credentials are not recognized.'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Logout', [
            'ip' => $request->ip()
        ]);

        return redirect()->route('home')->with('success', __('login.Logout successful'));
    }
}
