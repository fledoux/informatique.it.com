<?php

return [

    'client_id' => env('OIDC_CLIENT_ID'),
    'client_secret' => env('OIDC_CLIENT_SECRET'),
    'redirect' => env('OIDC_REDIRECT_URI'),
    'base_url' => env('OIDC_BASE_URL'),
    'scopes' => env('OIDC_SCOPES', 'openid email profile groups'),

    'route_prefix' => 'oauth',
    'route_middleware' => ['web'],

    'redirect_on_login' => '/dashboard',
    'redirect_on_error' => '/login',
    'redirect_on_logout' => env('OIDC_REDIRECT_ON_LOGOUT', '/'),

    'end_session_endpoint' => env('OIDC_END_SESSION_ENDPOINT'),

    'user_model' => \App\Models\User::class,
    'create_user' => true,
    'sub_column' => 'oidc_sub',

    'user_fields' => [
        'email' => 'email',
        'name' => 'name',
        'firstname' => 'given_name',
        'lastname' => 'family_name',
    ],

    'user_defaults' => [
        'status' => 'active',
    ],

    'active_check' => [
        'column' => 'status',
        'value' => 'active',
    ],

    'groups_claim' => 'groups',

    'group_roles' => [
        'app_info_it_yc' => 'super-admin',
        'app_info_it_manager' => 'manager',
        'app_info_it_admin' => 'admin',
        'app_info_it_user' => 'user',
    ],

];
