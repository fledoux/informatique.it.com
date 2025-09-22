# Standards de Codage

## Conventions de Nommage de Fichiers

### Fichiers de Traduction
- **TOUJOURS en minuscules** : `auth.php`, `register.php`, `login.php`
- **Jamais de majuscules** : ~~`Auth.php`~~, ~~`Register.php`~~, ~~`Login.php`~~

### Fichiers PHP
- **Controllers** : PascalCase `UserController.php`
- **Models** : PascalCase `User.php`
- **Requests** : PascalCase `UserStoreRequest.php`
- **Vues Blade** : snake_case `user_profile.blade.php`
- **Traductions** : snake_case `user.php`, `auth.php`

### Raison
Les serveurs Linux (production) sont sensibles à la casse contrairement à macOS/Windows (développement).
Un fichier `Register.php` en développement peut causer une erreur 404 en production si le code appelle `register.php`.

## Vérification avant Commit
```bash
# Vérifier les noms de fichiers
git ls-files | grep -E '[A-Z].*\.php$' | grep -v 'app/'
```