<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('login.Logging out') }}</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
                sans-serif;
        }

        .logout-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            text-align: center;
            max-width: 400px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        h2 {
            color: #333;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 24px;
        }

        p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            max-width: 120px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logo">
            <img src="{{ asset('assets/img/logo/logo-vertical.svg') }}"
                alt="{{ config('app.brand_name') }} by Yellow Cactus">
        </div>

        <div class="spinner"></div>

        <h2>{{ __('login.Logging out') }}</h2>
        <p>{{ __('login.Please wait while we log you out securely') }}</p>
    </div>

    {{-- Load Synology SDK --}}
    <script src="{{ config('oauth.synology_sdk_url') }}" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SDK-based logout for Synology
            if (typeof SYNOSSO !== 'undefined') {
                SYNOSSO.login({
                    app_id: '{{ config("oauth.synology_app_id") }}',
                    version: 1,
                    server_url: '{{ config("oauth.base_url") }}',
                    acs_url: '{{ route("oauth.callback") }}',
                    logout_url: '{{ route("home") }}',
                    on_query: function(base_url) {
                        console.log('SYNOSSO SDK initialized with base_url:', base_url);
                    },
                    on_logout: function() {
                        console.log('SYNOSSO logout callback triggered');
                        // Redirect to home after SDK logout completes
                        window.location.href = '{{ route("home") }}';
                    }
                });

                // Trigger logout
                console.log('Calling SYNOSSO.logout()');
                SYNOSSO.logout();

            } else {
                console.error('Synology SSO SDK not loaded, fallback to home');
                // Fallback redirect if SDK not available
                setTimeout(function() {
                    window.location.href = '{{ route("home") }}';
                }, 2000);
            }
        });
    </script>
</body>
</html>
