<?php

/**
 * Synology SAML 2.0 SSO Configuration
 * Configure your SAML 2.0 connection with Synology SSO Server
 */

return [
    /**
     * Strict mode - set to false for development
     */
    'strict' => env('SAML_STRICT', false),

    /**
     * Debug mode
     */
    'debug' => env('SAML_DEBUG', false),

    /**
     * Service Provider (SP) - Your Laravel application
     */
    'sp' => [
        'entityId' => env('SAML_SP_ENTITY_ID'),
        'assertionConsumerService' => [
            'url' => env('SAML_SP_ACS_URL'),
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ],
        'singleLogoutService' => [
            'url' => env('SAML_SP_SLS_URL'),
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ],
        'x509cert' => env('SAML_SP_CERT')
            ? base64_decode(env('SAML_SP_CERT'))
            : file_get_contents(storage_path('app/sso/sp.crt')),
        'privateKey' => env('SAML_SP_KEY')
            ? base64_decode(env('SAML_SP_KEY'))
            : file_get_contents(storage_path('app/sso/sp.key')),
    ],

    /**
     * Identity Provider (IdP) - Synology SSO Server
     */
    'idp' => [
        'entityId' => env('SAML_IDP_ENTITY_ID'),
        'singleSignOnService' => [
            'url' => env('SAML_IDP_SSO_URL'),
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ],
        'singleLogoutService' => [
            'url' => env('SAML_IDP_SLS_URL'),
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ],
        'x509cert' => env('SAML_IDP_CERT')
            ? base64_decode(env('SAML_IDP_CERT'))
            : file_get_contents(storage_path('app/sso/idp.cert')),
    ],

    /**
     * Security settings
     */
    'security' => [
        'nameIdEncrypted' => false,
        'authnRequestsSigned' => false,
        'wantAssertionsSigned' => false, // Désactiver pour développement
        'wantAssertionsEncrypted' => false,
        'signMetadata' => false,
        'encryptionAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#aes256-cbc',
    ],

    /**
     * Contact information
     */
    'contactPerson' => [
        'technical' => [
            'givenName' => 'Tech Admin',
            'emailAddress' => 'tech@example.com',
        ],
        'support' => [
            'givenName' => 'Support',
            'emailAddress' => 'support@example.com',
        ],
    ],

    /**
     * Organization information
     */
    'organization' => [
        'en' => [
            'name' => 'Yellow Cactus',
            'displayname' => 'Yellow Cactus Helpdesk',
            'url' => env('APP_URL'),
        ],
    ],
];
