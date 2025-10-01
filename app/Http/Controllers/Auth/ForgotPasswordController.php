<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\Helper;
use App\Mail\ResetPasswordGlobalMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm(): View
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => __('validation.required'),
            'email.email' => __('validation.email'),
            'email.exists' => __('passwords.user'),
        ]);

        // Trouver l'utilisateur
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => __('passwords.user')]);
        }

        // Générer un token de réinitialisation
        $token = Str::random(60);
        
        // Stocker le token dans la table password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Envoyer l'email avec votre système globalMail
        try {
            Mail::to($user->email)->send(new ResetPasswordGlobalMail($user, $token));
            
            return back()->with('status', __('passwords.sent'));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => __('passwords.throttled')]);
        }
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ], [
            'token.required' => __('validation.required'),
            'email.required' => __('validation.required'),
            'email.email' => __('validation.email'),
            'password.required' => __('validation.required'),
            'password.confirmed' => __('validation.confirmed'),
        ]);

        // Valider la force du mot de passe
        $passwordValidation = Helper::validatePasswordStrength($request->password);
        if (!$passwordValidation['valid']) {
            return redirect()->back()
                ->withInput($request->only('email', 'token'))
                ->withErrors(['password' => $passwordValidation['errors']]);
        }

        // Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => __('passwords.user')]);
        }

        // Vérifier le token
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors(['email' => __('passwords.token')]);
        }

        // Vérifier l'expiration (60 minutes par défaut)
        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);
        if (now()->subMinutes($expireMinutes)->isAfter($tokenData->created_at)) {
            return back()->withErrors(['email' => __('passwords.token')]);
        }

        // Réinitialiser le mot de passe
        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Déclencher l'événement
        event(new PasswordReset($user));

        return redirect()->route('login')->with('success', __('passwords.reset'));
    }
}