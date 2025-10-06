# Configuration Pushover

## Installation
La librairie Pushover a été installée avec succès :
```bash
composer require laravel-notification-channels/pushover
```

## Configuration

### 1. Variables d'environnement
Ajoutez ces variables dans votre fichier `.env` :
```env
PUSHOVER_APP_TOKEN=your_pushover_app_token_here
PUSHOVER_USER_KEY=your_pushover_user_key_here
```

### 2. Configuration des services
Le fichier `config/services.php` contient déjà la configuration :
```php
'pushover' => [
    'token' => env('PUSHOVER_APP_TOKEN'),
    'user' => env('PUSHOVER_USER_KEY'),
],
```

## Utilisation

### Service simple
```php
use App\Services\PushoverService;

// Notification normale
PushoverService::send('Titre', 'Message de notification');

// Notification avec priorité élevée
PushoverService::send('Urgent', 'Message important', 1);
```

### Notification Laravel complète
```php
use App\Notifications\PushoverNotification;
use Illuminate\Support\Facades\Notification;

$notifiable = new class {
    public function routeNotificationForPushover(): array
    {
        return [
            'token' => config('services.pushover.token'),
            'user' => config('services.pushover.user'),
        ];
    }
};

Notification::send($notifiable, new PushoverNotification('Titre', 'Message', 0));
```

## Priorités Pushover
- `-2` : Priorité la plus basse (silencieux)
- `-1` : Priorité basse
- `0` : Priorité normale (défaut)
- `1` : Priorité élevée
- `2` : Priorité d'urgence (nécessite confirmation)

## Exemple d'utilisation dans l'application
Le service est déjà utilisé dans `PageController@qrCode()` pour notifier lors du scan du QR code.