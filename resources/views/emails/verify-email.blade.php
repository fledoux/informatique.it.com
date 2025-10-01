<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <title>{{ __('register.Confirm your email') }}</title>
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

        .button {
            display: inline-block;
            background-color: #ff4c00 !important;
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            color-scheme: light only !important;
        }
    </style>
</head>

<body style="background-color: #f8f9fa !important; margin: 0; padding: 0; color-scheme: light only !important;" bgcolor="#f8f9fa">
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa !important;" bgcolor="#f8f9fa">
        <tr>
            <td style="background-color: #f8f9fa !important; padding: 20px;" bgcolor="#f8f9fa">
                <!-- Conteneur avec coins arrondis et ombre -->
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff !important; border: 1px solid #dee2e6; border-radius: 0.375rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); padding: 1.5rem; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color-scheme: light only !important;" bgcolor="#ffffff">
                    <img src="{{ $message->embed(public_path('assets/img/logo/logo-horizontal.svg')) }}" alt="informatique.it.com"
                        style="width:240px; margin-bottom: 40px !important;">
                    <h1 style="color: #212529 !important; font-size: 18px; font-weight: bold; margin-bottom: 1rem; margin-top: 20px;">
                        {{ __('register.Confirm your email') }}
                    </h1>
                    <div style="color: #6c757d !important; font-size: 1rem; line-height: 1.5; margin-bottom: 0;">
                        <h2>{{ \App\Helpers\Helper::getGreeting() }} !</h2>
                        <p>{{ __('register.Please confirm your email address by clicking the following link') }}:</p>
                        <div style="text-align: center;">
                            <a href="{{ $verificationUrl }}" class="button">
                                {{ __('register.Confirm my email') }}
                            </a>
                        </div>
                        <p>{{ __('register.This link will expire in :minutes minutes', ['minutes' => $expires]) }}.</p>
                        <p>{{ __('register.If you did not create an account, no further action is required') }}.</p>
                        <p>{!! __('register.Cheers') !!}</p>
                    </div>
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
