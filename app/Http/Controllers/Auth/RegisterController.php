<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\AllowDomainRegistration;
use App\Helpers\Helper;
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
        if (env('APP_ENV') == 'local') {
            $registrationOpen = true;
        }

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
        $registrationOpen = now()->gte('2025-11-14'); // Ouverture: 14 novembre 2025
        if (env('APP_ENV') == 'local') {
            $registrationOpen = true;
        }

        if (!$registrationOpen) {
            return redirect()->route('register')
                ->with('error', __('register.Registration not open yet'));
        }

        // Valider la force du mot de passe
        $passwordValidation = Helper::validatePasswordStrength($request->password);
        if (!$passwordValidation['valid']) {
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['password' => $passwordValidation['errors']]);
        }

        // Construire le name automatiquement si vide
        $name = $request->name;
        if (empty($name)) {
            $name = trim(($request->firstname ?? '') . ' ' . ($request->lastname ?? ''));
            // Si firstname et lastname sont aussi vides, utiliser la partie avant @ de l'email
            if (empty($name)) {
                $name = explode('@', $request->email)[0];
            }
        }

        // Vérifier si le domaine email permet une inscription automatique
        $company = AllowDomainRegistration::findCompanyByEmailDomain($request->email);
        $isNewCompany = false;
        
        // Si pas de société trouvée ET si les champs société sont renseignés, créer une nouvelle société
        // MAIS on n'ajoute PAS automatiquement le domaine pour éviter les problèmes de sécurité
        if (!$company && $request->filled('company')) {
            $company = \App\Models\Company::create([
                'name' => $request->company,
                'status' => 'active',
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'zip' => $request->zip,
                'city' => $request->city,
            ]);
            $isNewCompany = true; // Marquer qu'on vient de créer cette société
            // Note: On ne crée pas automatiquement l'AllowDomainRegistration
            // L'admin pourra l'ajouter manuellement après validation
        }
        
        // Create the user
        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone' => $request->phone,
            'agree_terms' => $request->agree_terms,
            'company_id' => $company?->id, // Rattacher automatiquement
            'email_verified_at' => null, // Will be set when email is verified
        ]);

        // Assign role based on company situation
        if ($isNewCompany) {
            // Nouvelle société créée = premier utilisateur = admin de sa société
            $user->assignRole('admin');
        } else {
            // Société existante (domaine reconnu) = utilisateur standard qui rejoint
            $user->assignRole('user');
        }

        // Send verification email manually
        $user->sendEmailVerificationNotification();

        return redirect()->route('register.pending')
            ->with('success', __('register.Check your email'));
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
                ->with('verify_email_error', __('register.Invalid verification link'));
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('success', __('register.Email already verified'));
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        // Log the user in after verification
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', __('register.Email verified successfully'));
    }

    /**
     * Resend email verification.
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->with('error', __('register.User not found'));
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', __('register.Email already verified'));
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', __('register.Verification email sent'));
    }
}
