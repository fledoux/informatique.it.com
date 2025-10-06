# Configuration Sentry.io

## Installation et Configuration

### 1. Installation du package Sentry
```bash
composer require sentry/sentry-laravel
php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```

### 2. Configuration du DSN
1. Créer un compte sur [Sentry.io](https://sentry.io)
2. Créer un nouveau projet Laravel
3. Récupérer le DSN du projet
4. Ajouter le DSN dans le fichier `.env` :

```bash
SENTRY_LARAVEL_DSN=https://YOUR-DSN-KEY@sentry.io/YOUR-PROJECT-ID
SENTRY_ENVIRONMENT=production
SENTRY_TRACES_SAMPLE_RATE=1.0
```

### 3. Configuration des canaux de logs
Le fichier `config/logging.php` a été configuré avec un canal Sentry :

```php
'sentry' => [
    'driver' => 'sentry',
]
```

### 4. Middleware de contexte
Le middleware `SentryContext` capture automatiquement :
- Informations utilisateur (ID, nom, email, rôles)
- Informations de requête (IP, User-Agent, route)
- Tags personnalisés pour filtrage

### 5. Tests de fonctionnement

#### Via le dashboard (développement uniquement)
- Accéder au dashboard en tant que super-admin
- Utiliser les boutons de test Sentry
- Vérifier les erreurs dans l'interface Sentry.io

#### Via les commandes
```bash
# Test officiel Sentry
php artisan sentry:test

# Test d'exception web (développement uniquement)
# GET /test-sentry
```

### 6. Intégration dans le code

#### Log manuel d'erreurs
```php
use Sentry\Laravel\Integration;

// Log d'une erreur
\Sentry\captureException(new \Exception('Message d\'erreur'));

// Log avec contexte
\Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
    $scope->setTag('ticket_id', $ticket->id);
    $scope->setUser(['id' => auth()->id()]);
});
```

#### Gestion d'exceptions personnalisées
Le fichier `bootstrap/app.php` configure la capture automatique via :
```php
->withExceptions(function (Exceptions $exceptions) {
    Integration::handles($exceptions);
})
```

### 7. Configuration de production

#### Variables d'environnement recommandées
```bash
SENTRY_LARAVEL_DSN=https://YOUR-DSN-KEY@sentry.io/YOUR-PROJECT-ID
SENTRY_ENVIRONMENT=production
SENTRY_RELEASE=v1.0.0  # Version de l'application
SENTRY_TRACES_SAMPLE_RATE=0.1  # 10% des traces en production
SENTRY_SEND_DEFAULT_PII=false  # Protection de la vie privée
SENTRY_ENABLE_LOGS=true  # Capture des logs Laravel
```

#### Filtrage des erreurs
Le fichier `config/sentry.php` ignore automatiquement :
- Les requêtes vers `/up` (health check)
- Ajoutez d'autres routes à ignorer si nécessaire

### 8. Monitoring et alertes
- Configurer les notifications Sentry pour les erreurs critiques
- Utiliser les releases pour tracker les déploiements
- Configurer les alertes par email/Slack pour l'équipe

### 9. Informations capturées
Le middleware `SentryContext` envoie automatiquement :
- **Utilisateur** : ID, nom, email, rôles Spatie
- **Requête** : IP, User-Agent, méthode HTTP
- **Contexte** : Route Laravel, contrôleur, action
- **Tags** : Environnement, version, rôle utilisateur

### 10. Troubleshooting

#### Erreur "Could not discover DSN"
- Vérifier que `SENTRY_LARAVEL_DSN` est défini dans `.env`
- Vérifier la syntaxe du DSN (doit commencer par `https://`)
- Redémarrer le serveur après modification du `.env`

#### Les erreurs ne remontent pas
- Vérifier que `APP_DEBUG=false` en production
- Vérifier les logs dans `storage/logs/laravel.log`
- Tester avec `php artisan sentry:test`

#### Contexte utilisateur manquant
- Vérifier que le middleware `SentryContext` est bien appliqué
- Vérifier l'authentification Laravel
- Contrôler les permissions Spatie