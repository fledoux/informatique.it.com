<?php

return [
    // Messages d'erreur HTTP
    '403' => [
        'title' => 'Accès refusé',
        'message' => 'Vous n\'avez pas l\'autorisation d\'accéder à cette ressource',
        'help' => 'L\'équipe de développement a été notifiée et travaille à résoudre ce problème',
    ],

    '404' => [
        'title' => 'Page non trouvée',
        'message' => 'La page que vous recherchez n\'existe pas ou a été déplacée',
        'help' => 'Veuillez vérifier l\'URL ou utiliser la navigation pour trouver ce que vous cherchez',
    ],

    '500' => [
        'title' => 'Erreur serveur',
        'message' => 'Un problème est survenu sur nos serveurs',
        'help' => 'Notre équipe a été notifiée et travaille à résoudre ce problème',
    ],

    // Messages d'erreur Laravel courants (attention aux espaces et ponctuation exacte)
    
    // Erreurs 403
    'Invalid signature.' => 'Signature invalide.',
    'Invalid signature' => 'Signature invalide',
    'The given data was invalid.' => 'Les données fournies sont invalides.',
    'The given data was invalid' => 'Les données fournies sont invalides',
    'This action is unauthorized.' => 'Cette action n\'est pas autorisée.',
    'This action is unauthorized' => 'Cette action n\'est pas autorisée',
    'Access denied.' => 'Accès refusé.',
    'Access denied' => 'Accès refusé',
    'Forbidden' => 'Interdit',
    'Unauthorized' => 'Non autorisé',

    // Erreurs 500 courantes
    'Server Error' => 'Erreur serveur',
    'Internal Server Error' => 'Erreur interne du serveur',
    'Service Unavailable' => 'Service non disponible',
    'Database connection failed' => 'Échec de connexion à la base de données',
    'Connection timeout' => 'Délai de connexion dépassé',
    'Memory limit exceeded' => 'Limite de mémoire dépassée',
    'Fatal error' => 'Erreur fatale',
    'Class not found' => 'Classe non trouvée',
    'Method not found' => 'Méthode non trouvée',
    'Call to undefined function' => 'Appel à une fonction non définie',
    'Maximum execution time exceeded' => 'Temps d\'exécution maximum dépassé',

    // Messages d'erreur de permissions
    'permissions' => [
        'insufficient_privileges' => 'Privilèges insuffisants pour cette action',
        'unauthorized_access' => 'Accès non autorisé à cette fonctionnalité',
        'role_required' => 'Un rôle spécifique est requis pour accéder à cette ressource',
        'company_access_denied' => 'Vous n\'avez pas accès aux données de cette entreprise',
    ],

    // Messages d'erreur génériques
    'generic' => [
        'something_went_wrong' => 'Une erreur inattendue s\'est produite',
        'please_try_again' => 'Veuillez réessayer plus tard',
        'contact_support' => 'Contactez le support si le problème persiste',
    ],
];