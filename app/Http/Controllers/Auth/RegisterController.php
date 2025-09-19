<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Display the registration form.
     */
    public function showRegistrationForm(): View
    {
        // Check if registration is open (based on the wait template logic)
        $registrationOpen = now()->gte('2025-11-14'); // Ouverture: 14 novembre 2025
        
        if (!$registrationOpen) {
            return view('auth.register-wait');
        }
        
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        // Check if registration is open
        $registrationOpen = now()->gte('2025-11-14');
        
        if (!$registrationOpen) {
            return redirect()->route('register')
                ->with('error', __('Register.Registration not open yet'));
        }

        // Create the user
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null, // Will be set when email is verified
        ]);

        // Assign default role
        $user->assignRole('manager'); // Default role based on PermissionSeeder

        // Send verification email
        event(new Registered($user));

        return redirect()->route('register.pending')
            ->with('success', __('Register.Check your email'));
    }

    /**
     * Display the pending verification page.
     */
    public function pending(): View
    {
        return view('auth.register-pending');
    }

    /**
     * Handle email verification.
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            return redirect()->route('register')
                ->with('verify_email_error', __('Register.Invalid verification link'));
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('success', __('Register.Email already verified'));
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        // Log the user in after verification
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', __('Register.Email verified successfully'));
    }
}