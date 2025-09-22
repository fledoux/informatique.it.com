# Guide de Dépannage

Solutions aux problèmes courants rencontrés lors du développement de l'application Laravel helpdesk.

## 🚨 Problèmes Fréquents

### 1. Problèmes de Base de Données

#### "Table doesn't exist"
```bash
# Vérifier l'état des migrations
php artisan migrate:status

# Exécuter les migrations manquantes
php artisan migrate

# Reset complet (ATTENTION: perte de données)
php artisan migrate:fresh --seed
```

#### "Foreign key constraint fails"
```bash
# Ordre des migrations important
# Vérifier que les tables parentes existent avant les FK
php artisan migrate:rollback --step=1
# Corriger la migration
php artisan migrate
```

#### Permissions Spatie manquantes
```bash
# Réinstaller les permissions
php artisan permission:cache-reset
php artisan db:seed --class=PermissionSeeder
```

### 2. Problèmes de Permissions

#### "This action is unauthorized"
```php
// Vérifier si l'utilisateur a la permission
auth()->user()->can('entity.create'); // true/false

// Vérifier les rôles assignés
auth()->user()->roles; // Collection des rôles

// Debug des permissions
dd(auth()->user()->getAllPermissions());
```

#### Cache de permissions
```bash
# Vider le cache des permissions
php artisan permission:cache-reset

# Vider tous les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 3. Problèmes de Traduction

#### Clés de traduction manquantes
```bash
# Lancer la vérification
php dev/translations.php check

# Correction automatique
php dev/translations.php fix

# Si le script n'existe pas
php artisan make:command CheckTranslations
```

#### Traductions non affichées
```php
// Vérifier la locale
app()->getLocale(); // 'fr' ou 'en'

// Forcer une locale pour test
App::setLocale('fr');
```

#### Ponctuation dans les traductions
```json
// ❌ Incorrect - ponctuation dans la clé
{
    "messages.success!": "Succès !"
}

// ✅ Correct - ponctuation dans la valeur
{
    "messages.success": "Succès !"
}
```

### 4. Problèmes de Formulaires

#### Validation échoue silencieusement
```php
// Dans le FormRequest, vérifier authorize()
public function authorize()
{
    return true; // ou la logique appropriée
}

// Debug des erreurs de validation
dd($validator->errors()); // Dans le controller
```

#### Composants x-forms non trouvés
```bash
# Vérifier que les composants existent
ls resources/views/components/forms/

# Publier les composants si nécessaire
php artisan vendor:publish --tag=components
```

#### Vieilles valeurs (old()) persistantes
```bash
# Vider la session
php artisan session:clear

# Ou dans le navigateur
# Supprimer cookies/session storage
```

### 5. Problèmes de Performance

#### Requêtes N+1
```php
// ❌ Problème N+1
$entities = Entity::all();
foreach($entities as $entity) {
    echo $entity->company->name; // Requête à chaque itération
}

// ✅ Solution avec eager loading
$entities = Entity::with('company')->get();
```

#### Debug de requêtes
```php
// Dans AppServiceProvider::boot()
DB::listen(function ($query) {
    Log::info($query->sql, $query->bindings);
});

// Ou utiliser Laravel Debugbar
composer require barryvdh/laravel-debugbar --dev
```

### 6. Problèmes de Développement

#### Serveur ne démarre pas
```bash
# Port déjà utilisé
php artisan serve --port=8001

# Permissions sur storage/
sudo chown -R www-data:www-data storage/
chmod -R 775 storage/
```

#### Queue workers ne traitent pas
```bash
# Vérifier la config queue
php artisan queue:work --verbose

# Redémarrer après changement de code
php artisan queue:restart
```

#### Vite/npm erreurs
```bash
# Nettoyer node_modules
rm -rf node_modules package-lock.json
npm install

# Vérifier la version Node
node --version # >= 16.x recommandé
```

## 🔍 Outils de Debug

### Artisan Commandes Utiles
```bash
# État général
php artisan about

# Routes disponibles
php artisan route:list

# Permissions spatie
php artisan permission:show

# Config actuelle
php artisan config:show

# Variables d'environnement
php artisan env
```

### Debug en Code
```php
// Dump et continue
dump($variable);

// Dump et arrêt
dd($variable);

// Log pour investigation
Log::info('Debug point', ['data' => $variable]);

// Ray (si installé)
ray($variable);
```

### Logs Laravel
```bash
# Suivre les logs en temps réel
tail -f storage/logs/laravel.log

# Avec Pail (plus lisible)
php artisan pail

# Filtrer les logs
php artisan pail --filter="error"
```

## 🛠️ Résolution par Type d'Erreur

### ModelNotFoundException
```php
// ❌ Erreur courante
$entity = Entity::findOrFail($id);

// ✅ Solution avec gestion
try {
    $entity = Entity::findOrFail($id);
} catch (ModelNotFoundException $e) {
    return redirect()->route('entity.index')
        ->with('error', __('crud.messages.not_found'));
}
```

### AuthorizationException
```php
// Vérifier les permissions dans routes
Route::get('/entities', [EntityController::class, 'index'])
    ->middleware('permission:entity.index');

// Ou dans le controller
$this->authorize('entity.index');
```

### QueryException
```php
// Foreign key violation
try {
    $entity->delete();
} catch (QueryException $e) {
    if ($e->getCode() == 23000) { // Integrity constraint
        return back()->with('error', __('crud.messages.delete_constraint'));
    }
    throw $e;
}
```

### ViewException
```bash
# Template Blade invalide
# Vérifier la syntaxe dans resources/views/

# Variables non définies
php artisan view:clear
```

## 🧪 Tests de Diagnostic

### Test de Base de Données
```php
// Vérifier connexion DB
try {
    DB::connection()->getPdo();
    echo "Connexion DB OK\n";
} catch (Exception $e) {
    echo "Erreur DB: " . $e->getMessage() . "\n";
}
```

### Test des Permissions
```php
// Tester les permissions d'un utilisateur
$user = User::find(1);
$permissions = $user->getAllPermissions()->pluck('name');
dd($permissions->toArray());
```

### Test de Configuration
```bash
# Vérifier toute la config
php artisan config:show

# Variable spécifique
php -r "echo env('APP_ENV');"
```

## 🚀 Cas d'Urgence Production

### Application down
```bash
# Mode maintenance
php artisan down --message="Maintenance en cours"

# Corriger le problème...

# Remettre en ligne
php artisan up
```

### Base de données corrompue
```bash
# Backup immédiat
mysqldump -u user -p database > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore depuis backup
mysql -u user -p database < backup_file.sql
```

### Logs trop volumineux
```bash
# Nettoyer les logs
echo "" > storage/logs/laravel.log

# Rotation automatique (dans logrotate)
/path/to/storage/logs/*.log {
    weekly
    rotate 4
    compress
    delaycompress
}
```

## 📞 Support et Resources

### Documentation Laravel
- [Laravel Docs](https://laravel.com/docs)
- [Spatie Permissions](https://spatie.be/docs/laravel-permission)

### Debugging Tools
- [Laravel Telescope](https://laravel.com/docs/telescope) (développement)
- [Laravel Horizon](https://laravel.com/docs/horizon) (queues)
- [Ray](https://spatie.be/products/ray) (debug avancé)

### Community
- [Laracasts](https://laracasts.com/)
- [Laravel Daily](https://laraveldaily.com/)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)

---

*Guide de dépannage basé sur les erreurs couramment rencontrées*  
*Maintenu et testé en situation réelle*