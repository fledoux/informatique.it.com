<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Le champ doit être accepté.',
    'accepted_if' => 'Le champ doit être accepté quand :other est :value.',
    'active_url' => 'Le champ n\'est pas une URL valide.',
    'after' => 'Le champ doit être une date postérieure au :date.',
    'after_or_equal' => 'Le champ doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ doit contenir uniquement des lettres.',
    'alpha_dash' => 'Le champ doit contenir uniquement des lettres, des chiffres et des tirets.',
    'alpha_num' => 'Le champ doit contenir uniquement des chiffres et des lettres.',
    'array' => 'Le champ doit être un tableau.',
    'before' => 'Le champ doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ doit être une date antérieure ou égale au :date.',
    'between' => [
        'numeric' => 'La valeur de doit être comprise entre :min et :max.',
        'file' => 'La taille du fichier de doit être comprise entre :min et :max kilo-octets.',
        'string' => 'Le texte doit contenir entre :min et :max caractères.',
        'array' => 'Le tableau doit contenir entre :min et :max éléments.',
    ],
    'boolean' => 'Le champ doit être vrai ou faux.',
    'confirmed' => 'Le champ de confirmation ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ n\'est pas une date valide.',
    'date_equals' => 'Le champ doit être une date égale à :date.',
    'date_format' => 'Le champ ne correspond pas au format :format.',
    'declined' => 'Le champ doit être décliné.',
    'declined_if' => 'Le champ doit être décliné quand :other est :value.',
    'different' => 'Les champs et :other doivent être différents.',
    'digits' => 'Le champ doit contenir :digits chiffres.',
    'digits_between' => 'Le champ doit contenir entre :min et :max chiffres.',
    'dimensions' => 'La taille de l\'image n\'est pas conforme.',
    'distinct' => 'Le champ a une valeur en double.',
    'email' => 'Le champ doit être une adresse e-mail valide.',
    'ends_with' => 'Le champ doit se terminer par une des valeurs suivantes : :values',
    'enum' => 'Le champ sélectionné n\'est pas valide.',
    'exists' => 'Le champ sélectionné n\'est pas valide.',
    'file' => 'Le champ doit être un fichier.',
    'filled' => 'Le champ doit avoir une valeur.',
    'gt' => [
        'numeric' => 'La valeur de doit être supérieure à :value.',
        'file' => 'La taille du fichier de doit être supérieure à :value kilo-octets.',
        'string' => 'Le texte doit contenir plus de :value caractères.',
        'array' => 'Le tableau doit contenir plus de :value éléments.',
    ],
    'gte' => [
        'numeric' => 'La valeur de doit être supérieure ou égale à :value.',
        'file' => 'La taille du fichier de doit être supérieure ou égale à :value kilo-octets.',
        'string' => 'Le texte doit contenir au moins :value caractères.',
        'array' => 'Le tableau doit contenir au moins :value éléments.',
    ],
    'image' => 'Le champ doit être une image.',
    'in' => 'Le champ est invalide.',
    'in_array' => 'Le champ n\'existe pas dans :other.',
    'integer' => 'Le champ doit être un entier.',
    'ip' => 'Le champ doit être une adresse IP valide.',
    'ipv4' => 'Le champ doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ doit être une adresse IPv6 valide.',
    'json' => 'Le champ doit être un document JSON valide.',
    'lt' => [
        'numeric' => 'La valeur de doit être inférieure à :value.',
        'file' => 'La taille du fichier de doit être inférieure à :value kilo-octets.',
        'string' => 'Le texte doit contenir moins de :value caractères.',
        'array' => 'Le tableau doit contenir moins de :value éléments.',
    ],
    'lte' => [
        'numeric' => 'La valeur de doit être inférieure ou égale à :value.',
        'file' => 'La taille du fichier de doit être inférieure ou égale à :value kilo-octets.',
        'string' => 'Le texte doit contenir au plus :value caractères.',
        'array' => 'Le tableau ne doit pas contenir plus de :value éléments.',
    ],
    'mac_address' => 'Le champ doit être une adresse MAC valide.',
    'max' => [
        'numeric' => 'La valeur de ne peut être supérieure à :max.',
        'file' => 'La taille du fichier de ne peut pas dépasser :max kilo-octets.',
        'string' => 'Le texte de ne peut contenir plus de :max caractères.',
        'array' => 'Le tableau ne peut contenir plus de :max éléments.',
    ],
    'mimes' => 'Le champ doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ doit être un fichier de type : :values.',
    'min' => [
        'numeric' => 'La valeur de doit être supérieure ou égale à :min.',
        'file' => 'La taille du fichier de doit être supérieure à :min kilo-octets.',
        'string' => 'Le texte doit contenir au moins :min caractères.',
        'array' => 'Le tableau doit contenir au moins :min éléments.',
    ],
    'multiple_of' => 'La valeur de doit être un multiple de :value',
    'not_in' => 'Le champ sélectionné n\'est pas valide.',
    'not_regex' => 'Le format du champ n\'est pas valide.',
    'numeric' => 'Le champ doit contenir un nombre.',
    'password' => 'Le mot de passe est incorrect.',
    'present' => 'Le champ doit être présent.',
    'prohibited' => 'Le champ est interdit.',
    'prohibited_if' => 'Le champ est interdit quand :other est :value.',
    'prohibited_unless' => 'Le champ est interdit sauf si :other est dans :values.',
    'prohibits' => 'Le champ interdit :other d\'être présent.',
    'regex' => 'Le format du champ est invalide.',
    'required' => 'Le champ est obligatoire.',
    'required_array_keys' => 'Le champ doit contenir des entrées pour : :values.',
    'required_if' => 'Le champ est obligatoire quand :other est :value.',
    'required_unless' => 'Le champ est obligatoire sauf si :other est dans :values.',
    'required_with' => 'Le champ est obligatoire quand :values est présent.',
    'required_with_all' => 'Le champ est obligatoire quand :values sont présents.',
    'required_without' => 'Le champ est obligatoire quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ est requis quand aucun des :values n\'est présent.',
    'same' => 'Les champs et :other doivent être identiques.',
    'size' => [
        'numeric' => 'La valeur de doit être :size.',
        'file' => 'La taille du fichier de doit être de :size kilo-octets.',
        'string' => 'Le texte de doit contenir :size caractères.',
        'array' => 'Le tableau doit contenir :size éléments.',
    ],
    'starts_with' => 'Le champ doit commencer par une des valeurs suivantes : :values',
    'string' => 'Le champ doit être une chaîne de caractères.',
    'timezone' => 'Le champ doit être un fuseau horaire valide.',
    'unique' => 'La valeur du champ est déjà utilisée.',
    'uploaded' => 'Le fichier du champ n\'a pu être téléversé.',
    'url' => 'Le champ doit être une URL valide.',
    'uuid' => 'Le champ doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Password Validation Messages
    |--------------------------------------------------------------------------
    */
    
    'password_min_length' => 'Le mot de passe doit contenir au moins :min caractères',
    'password_lowercase' => 'Le mot de passe doit contenir au moins une lettre minuscule',
    'password_uppercase' => 'Le mot de passe doit contenir au moins une lettre majuscule',
    'password_number' => 'Le mot de passe doit contenir au moins un chiffre',
    'password_special' => 'Le mot de passe doit contenir au moins un caractère spécial (:chars)',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];