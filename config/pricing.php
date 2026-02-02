<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ticket Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration centralisée des prix unitaires HT et de la validité
    | pour chaque catégorie de tickets. Ces valeurs sont utilisées dans
    | les vues (tarifs, simulateur) et les calculs de facturation.
    |
    */

    'prices' => [
        'unit' => (float) env('TICKET_PRICE_UNIT'),
        'p10' => (float) env('TICKET_PRICE_P10'),
        'p50' => (float) env('TICKET_PRICE_P50'),
        'p100' => (float) env('TICKET_PRICE_P100'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ticket Validity Configuration
    |--------------------------------------------------------------------------
    |
    | Validité des tickets par catégorie (en mois).
    | Utilisé pour afficher la durée de validité dans les vues.
    |
    */

    'validity' => [
        'unit' => (int) env('TICKET_VALIDITY_UNIT'),
        'p10' => (int) env('TICKET_VALIDITY_P10'),
        'p50' => (int) env('TICKET_VALIDITY_P50'),
        'p100' => (int) env('TICKET_VALIDITY_P100'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ticket Discount Configuration
    |--------------------------------------------------------------------------
    |
    | Taux de remise par rapport au ticket unitaire (en pourcentage).
    | Calculés automatiquement à partir des prix.
    |
    */

    'discounts' => (function() {
        $priceUnit = (float) env('TICKET_PRICE_UNIT');
        $prices = [
            'unit' => $priceUnit,
            'p10' => (float) env('TICKET_PRICE_P10'),
            'p50' => (float) env('TICKET_PRICE_P50'),
            'p100' => (float) env('TICKET_PRICE_P100'),
        ];
        
        $discounts = [];
        foreach ($prices as $key => $price) {
            if ($key === 'unit' || $priceUnit <= 0) {
                $discounts[$key] = 0;
            } else {
                $rawDiscount = (($priceUnit - $price) / $priceUnit) * 100;
                // Arrondi à l'entier
                $discounts[$key] = round($rawDiscount);
            }
        }
        
        return $discounts;
    })(),

    /*
    |--------------------------------------------------------------------------
    | TVA Rate
    |--------------------------------------------------------------------------
    |
    | Taux de TVA appliqué aux tickets (20% en France).
    |
    */

    'tva_rate' => 0.20,

];
