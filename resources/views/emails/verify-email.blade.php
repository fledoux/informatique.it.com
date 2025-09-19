<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Register.Confirm your email') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #ff6b35;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            background-color: #ff6b35;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ __('Register.Confirm your email') }}</h1>
        </div>
        
        <div class="content">
            <h2>{{ __('Register.Hello') }}!</h2>
            
            <p>{{ __('Register.Please confirm your email address by clicking the following link') }}:</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">
                    {{ __('Register.Confirm my email') }}
                </a>
            </div>
            
            <p>{{ __('Register.This link will expire in :minutes minutes', ['minutes' => $expires]) }}.</p>
            
            <p>{{ __('Register.If you did not create an account, no further action is required') }}.</p>
            
            <p>{{ __('Register.Cheers') }}!</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} informatique.it.com - {{ __('Register.All rights reserved') }}</p>
        </div>
    </div>
</body>
</html>