# Standards UI du projet

> **Note pour les développeurs** : Ces standards s'appliquent à toutes les vues, y compris celles générées avec `php artisan make:crud-bootstrap`. Le générateur CRUD référence ce fichier dans sa documentation interne.

## Alertes avec icônes

Pour maintenir une cohérence visuelle dans tout le projet, toutes les alertes avec icônes doivent suivre cette structure :

### Structure recommandée

```blade
<div class="alert alert-info">
    <div class="d-flex align-items-start">
        <i class="fa-regular fa-info-circle me-2 mt-1"></i>
        <div>
            Votre texte ici, qui peut s'étendre
            sur plusieurs lignes sans problème.
        </div>
    </div>
</div>
```

### Pour les paragraphes courts

```blade
<p class="text-muted d-flex align-items-start">
    <i class="fa-regular fa-info-circle me-2 mt-1"></i>
    <span>Votre texte court ici.</span>
</p>
```

### Principes clés

1. **`d-flex align-items-start`** : Conteneur flex avec alignement en haut
2. **`me-2`** sur l'icône : Marge droite pour espacer l'icône du texte
3. **`mt-1`** sur l'icône : Petite marge en haut pour aligner visuellement avec la première ligne de texte
4. **`<div>` ou `<span>`** pour le texte : Permet au texte multi-lignes de ne pas passer sous l'icône

### Icônes courantes

- Info : `fa-regular fa-info-circle`
- Warning : `fa-regular fa-exclamation-triangle`
- Success : `fa-regular fa-check-circle`
- Error : `fa-regular fa-times-circle`

### Fichiers mis à jour

Les fichiers suivants ont été standardisés :
- `resources/views/user/index.blade.php`
- `resources/views/ticket/show.blade.php`

## DataTables

### Colonne triée en gras

La colonne actuellement triée est automatiquement mise en gras via CSS :

```css
/* public/assets/css/app.css */
.datatable thead th.sorting_asc,
.datatable thead th.sorting_desc,
.datatable tbody td.sorting_1 {
  font-weight: 600;
}
```

### Options de tri personnalisées

```blade
@push('javascripts')
    @include('partials._datatable', [
        'datatableOptions' => [
            'order' => [[2, 'asc']], // Tri par colonne 3 (index 2) croissant
            'columnDefs' => [
                ['orderable' => false, 'targets' => -1] // Désactiver tri sur dernière colonne
            ]
        ]
    ])
@endpush
```

## SMS Service (AWS SNS)

### Configuration

- **Région** : `eu-west-3` (Paris)
- **Type de message** : `Transactional` (priorité élevée)
- **Sender ID** : Géré automatiquement par AWS (36650)

### Utilisation

```php
\App\Services\SmsService::send('+33612345678', 'Votre message');
```

Le numéro doit être au format international (+33 pour la France).

## Sentry

Le monitoring d'erreurs Sentry est activé **uniquement en production** :

```php
// config/sentry.php
'dsn' => env('APP_ENV') === 'production' ? env('SENTRY_LARAVEL_DSN') : null,
```

En développement, aucune requête n'est envoyée à Sentry.
