<?php

namespace App\Http\Controllers\Auth;

use Aacotroneo\Saml2\Http\Controllers\Saml2Controller;
use Aacotroneo\Saml2\Saml2Auth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Saml2SynologyController extends Saml2Controller
{
    /**
     * Process SAML Assertion Consumer Service (ACS) response with group validation
     */
    public function acs(Saml2Auth $saml2Auth, $idpName = 'synology')
    {
        $errors = $saml2Auth->acs();

        if (!empty($errors)) {
            Log::error('Saml2 ACS error', [
                'errors' => $errors,
                'reason' => $saml2Auth->getLastErrorReason(),
                'idp' => $idpName,
            ]);
            session()->flash('error', $saml2Auth->getLastErrorReason());
            return redirect(config('saml2_settings.errorRoute'));
        }

        $saml2User = $saml2Auth->getSaml2User();

        // Extract SAML attributes
        $email = $saml2User->getAttribute('mail')[0] ?? $saml2User->getAttribute('email')[0] ?? null;
        $groups = $saml2User->getAttribute('memberOf') ?? [];
        $firstname = $saml2User->getAttribute('givenName')[0] ?? '';
        $lastname = $saml2User->getAttribute('sn')[0] ?? '';
        $displayName = $saml2User->getAttribute('displayName')[0] ?? trim("$firstname $lastname");

        if (!$email) {
            Log::warning('Saml2 ACS: No email in SAML response', ['idp' => $idpName]);
            session()->flash('error', __('login.saml.no_email'));
            return redirect(config('saml2_settings.errorRoute'));
        }

        // Verify user is in one of the required app_info_it groups
        $groupMapping = [
            'app_info_it_yc' => 'super-admin',
            'app_info_it_manager' => 'manager',
            'app_info_it_admin' => 'admin',
            'app_info_it_user' => 'user',
        ];

        $userRole = null;
        foreach ($groups as $group) {
            foreach ($groupMapping as $ldapGroup => $role) {
                if (stripos($group, $ldapGroup) !== false) {
                    $userRole = $role;
                    break 2; // Exit both loops
                }
            }
        }

        if (!$userRole) {
            Log::warning('Saml2 ACS: User not in any required group', [
                'email' => $email,
                'groups_count' => count($groups),
                'required_groups' => array_keys($groupMapping),
                'idp' => $idpName,
            ]);
            session()->flash('error', __('login.saml.no_access'));
            return redirect(config('saml2_settings.errorRoute'));
        }

        // Find or create user
        $userModel = config('auth.providers.users.model', User::class);
        $user = $userModel::where('email', $email)->first();

        if (!$user) {
            try {
                $user = $userModel::create([
                    'email' => $email,
                    'name' => $displayName ?: $email,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);

                Log::info('Saml2 ACS: User created from SAML', [
                    'email' => $email,
                    'user_id' => $user->id,
                    'role' => $userRole,
                    'idp' => $idpName,
                ]);
            } catch (\Exception $e) {
                Log::error('Saml2 ACS: Failed to create user', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                    'idp' => $idpName,
                ]);
                session()->flash('error', __('login.saml.failed_to_create'));
                return redirect(config('saml2_settings.errorRoute'));
            }
        }

        // Check if user is active
        if ($user->status !== 'active') {
            Log::warning('Saml2 ACS: Inactive user attempted login', [
                'email' => $email,
                'user_id' => $user->id,
                'idp' => $idpName,
            ]);
            session()->flash('error', __('login.saml.inactive_account'));
            return redirect(config('saml2_settings.errorRoute'));
        }

        // Sync user role from LDAP group
        $user->syncRoles([$userRole]);

        Log::info('Saml2 ACS: User authenticated', [
            'email' => $email,
            'user_id' => $user->id,
            'role' => $userRole,
            'idp' => $idpName,
        ]);

        // Authenticate user
        Auth::login($user, remember: true);

        // Redirect to dashboard (or intended URL)
        $redirectUrl = $saml2User->getIntendedUrl();
        if ($redirectUrl !== null) {
            return redirect($redirectUrl);
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Handle SAML logout (SLS - Single Logout Service)
     */
    public function sls(Saml2Auth $saml2Auth, $idpName = 'synology')
    {
        Log::info('Saml2 SLS: User logout', [
            'user' => Auth::user()?->email,
            'idp' => $idpName,
        ]);

        Auth::logout();
        session()->invalidate();
        request()->session()->regenerateToken();

        return redirect(config('saml2_settings.logoutRoute', '/login'))
            ->with('success', __('login.Logout successful'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
