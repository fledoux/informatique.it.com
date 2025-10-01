<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <title>{{ __('passwords.Reset Password') }}</title>

    <style>
        :root {
            color-scheme: light only;
        }
        
        html,
        body {
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color-scheme: light only !important;
        }

        /* Forcer absolument le mode clair */
        * {
            color-scheme: light only !important;
        }

        /* Forcer les couleurs même en mode sombre - plus agressif */
        @media (prefers-color-scheme: dark) {
            :root {
                color-scheme: light only !important;
            }
            html, body {
                background-color: #f8f9fa !important;
                color: #212529 !important;
                color-scheme: light only !important;
            }
            * {
                background-color: inherit !important;
                color: inherit !important;
            }
        }

        /* Support pour tous les clients email connus */
        [data-ogsc] html, [data-ogsc] body,
        [data-outlook-cycle] html, [data-outlook-cycle] body {
            background-color: #f8f9fa !important;
            color-scheme: light only !important;
        }

        /* Styles pour le bouton */
        .btn-orange {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ff4c00 !important;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 0.375rem;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        
        .btn-orange:hover {
            background-color: #e63900 !important;
        }
    </style>
</head>

<body style="background-color: #f8f9fa !important; margin: 0; padding: 0; color-scheme: light only !important;" bgcolor="#f8f9fa">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa !important;" bgcolor="#f8f9fa">
        <tr>
            <td style="background-color: #f8f9fa !important; padding: 20px;" bgcolor="#f8f9fa">
                <!-- Conteneur avec coins arrondis et ombre -->
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff !important; border: 1px solid #dee2e6; border-radius: 0.375rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); padding: 1.5rem; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color-scheme: light only !important;" bgcolor="#ffffff">
                    
                    <!-- Logo -->
                    <img src="{{ $message->embed(public_path('assets/img/logo/logo-horizontal.svg')) }}" alt="informatique.it.com"
                        style="width:240px; margin-bottom: 40px !important;">
                    
                    <!-- Titre -->
                    <h1 style="color: #212529 !important; font-size: 24px; font-weight: bold; margin-bottom: 1.5rem; margin-top: 20px;">
                        <span style="color: #ff4c00 !important;">🔐</span> {{ __('passwords.Reset Password') }}
                    </h1>
                    
                    <!-- Salutation -->
                    <p style="color: #6c757d !important; font-size: 16px; line-height: 1.6; margin-bottom: 1rem;">
                        {{ App\Helpers\Helper::getGreeting() }} {{ $user->firstname ?? $user->name }},
                    </p>
                    
                    <!-- Message principal -->
                    <p style="color: #212529 !important; font-size: 16px; line-height: 1.6; margin-bottom: 1.5rem;">
                        {{ __('passwords.You are receiving this email because we received a password reset request for your account') }}.
                    </p>
                    
                    <!-- Bouton -->
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}" 
                           class="btn-orange" 
                           style="display: inline-block; padding: 12px 24px; background-color: #ff4c00 !important; color: #ffffff !important; text-decoration: none; border-radius: 0.375rem; font-weight: 600;">
                            {{ __('passwords.Reset Password') }}
                        </a>
                    </div>
                    
                    <!-- Informations d'expiration -->
                    <div style="background-color: #fff3cd !important; border: 1px solid #ffeaa7; border-radius: 0.375rem; padding: 15px; margin: 20px 0;" bgcolor="#fff3cd">
                        <p style="color: #856404 !important; font-size: 14px; line-height: 1.5; margin: 0;">
                            <strong>⚠️ {{ __('passwords.Important') }} :</strong> 
                            {{ __('passwords.This password reset link will expire in') }} 
                            {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} 
                            {{ __('passwords.minutes') }}.
                        </p>
                    </div>
                    
                    <!-- Note de sécurité -->
                    <p style="color: #6c757d !important; font-size: 14px; line-height: 1.5; margin-bottom: 1.5rem;">
                        {{ __('passwords.If you did not request a password reset, no further action is required') }}.
                    </p>
                    
                    <!-- Signature -->
                    <p style="color: #212529 !important; font-size: 16px; line-height: 1.6; margin-bottom: 0;">
                        {{ __('global.Thanks') }},<br>
                        <strong>{{ config('app.name') }}</strong>
                    </p>
                </div>

                <!-- Footer en dehors du conteneur principal -->
                <div style="max-width: 600px; margin: 0 auto; padding: 1.5rem; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    @include('emails._baseline')
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
