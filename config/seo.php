<?php

return [
    'site' => [
        'name' => 'informatique-it.com',
        'tagline' => 'Support informatique professionnel depuis +25 ans',
        'description' => 'Expert en support informatique, assistance Mac/PC, infogérance et cybersécurité pour entreprises. Intervention rapide, devis gratuit 24h. +25 ans d\'expérience, 9999+ interventions réussies.',
        'keywords' => 'support informatique, assistance informatique, infogérance, cybersécurité, maintenance informatique, assistance PC Mac, expert informatique France, assistance rapide, support technique',
        'author' => 'informatique-it.com',
        'email' => 'hello@informatique-it.com',
        'phone' => '+33 1 49 66 21 77',
        'url' => env('APP_URL', 'https://informatique-it.com'),
    ],

    'social' => [
        'twitter' => [
            'handle' => '@yellow_cactus',
            'url' => 'https://x.com/yellow_cactus',
        ],
        'linkedin' => [
            'company' => 'yellowcactus',
            'url' => 'https://www.linkedin.com/company/yellowcactus',
        ],
    ],

    'business' => [
        'rating' => [
            'value' => '4.8',
            'count' => '370',
            'max' => '5',
        ],
        'opening_hours' => 'Mo-Fr 09:00-18:00',
        'price_range' => '€€',
        'country' => 'FR',
        'locality' => 'France',
        'coordinates' => [
            'latitude' => '46.603354',
            'longitude' => '1.888334',
        ],
    ],

    'services' => [
        [
            'name' => 'Support informatique',
            'description' => 'Dépannage et assistance informatique pour PC et Mac',
        ],
        [
            'name' => 'Infogérance',
            'description' => 'Gestion et maintenance complète de votre infrastructure informatique',
        ],
        [
            'name' => 'Cybersécurité',
            'description' => 'Protection et sécurisation de vos données et systèmes informatiques',
        ],
    ],

    'images' => [
        'logo' => '/favicon.ico',
        'og_image' => '/favicon.ico',
        'twitter_image' => '/favicon.ico',
    ],
];