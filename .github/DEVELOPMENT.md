# Guide de Développement - informatique.it.com

Ce guide contient toutes les informations essentielles pour maintenir et développer l'application Laravel helpdesk.

## 📋 Architecture du Projet

### Structure Multi-tenant
- **Tenant racine :** `Company` - toutes les données sont liées à une entreprise
- **Utilisateurs :** `User` appartient à `Company` via `company_id`
- **Permissions :** Spatie Laravel-Permission avec convention `{resource}.{action}`
- **Rôles :** super-admin, admin, manager avec granularité différente

### Modèles de Données Principaux
- **Company :** Tenant racine avec informations complètes (SIRET, TVA, adresse)
- **User :** Utilisateur lié à Company avec rôles/permissions
- **Ticket :** Système complet (statuts, priorités, assignation, facturation)
- **Contact :** Prospects/clients avec types et besoins
- **Attachments :** Fichiers par ticket et par message
- **Time tracking :** Charges décimales pour facturation

## 🌐 Système de Traductions

### Structure Standard
```php
// Fichier: resources/lang/{fr|en}/entity.php
return [
    'entity' => 'Nom de l\'entité',
    'fields' => [
        'field_name' => 'Nom du champ',
    ],
    'enums' => [
        'status' => [
            'active' => 'Actif',
            'inactive' => 'Inactif',
        ],
    ],
    'List' => 'Liste',
    'YourList' => 'Vos entités',
];
```

### Scripts de Maintenance
```bash
# Vérifier les traductions manquantes
php dev/translations.php check

# Corriger automatiquement + vérifier
php dev/translations.php fix

# Scripts individuels
php dev/check-translations.php
php dev/fix-translations.php
```

### Conventions Importantes
- **Clés imbriquées :** `company.fields.status` pour les champs de formulaire
- **Enums :** Structure `entity.enums.field.value`
- **Ponctuation :** Les clés peuvent contenir des points finaux (`register.We have...`)
- **Cohérence :** Même nombre de fichiers FR/EN (actuellement 16 chaque)

## 🎨 Composants Blade

### x-forms (Bootstrap 5 Floating Labels)
```blade
<x-forms.input 
    name="email" 
    label="E-mail" 
    type="email" 
    :value="old('email')" 
    required 
/>

<x-forms.select 
    name="status" 
    label="Statut" 
    :options="['active' => 'Actif', 'inactive' => 'Inactif']"
    :value="old('status')" 
/>
```

### Navigation avec Permissions
```blade
@can('user.index')
    <a href="{{ route('user.index') }}" class="nav-link">
        {{ __('nav.Users') }}
    </a>
@endcan
```

## 🗄️ Base de Données

### Standards
- **Status fields :** String enums (pas d'integers)
- **Décimaux billing :** `decimal(12,3)` pour précision
- **UUID publics :** `string(36)` pour partage
- **Foreign keys :** Toujours `->index()` et `->cascadeOnDelete()`
- **Pays :** Codes ISO-2 (`string(2)`)

### Migrations Pattern
```php
$table->string('status')->default('active');
$table->decimal('amount', 12, 3)->default(0);
$table->foreignId('company_id')->constrained()->cascadeOnDelete();
```

## 🚀 Workflows de Développement

### Ajout d'une Nouvelle Entité
1. **Migration :** Créer avec contraintes et index appropriés
2. **Modèle :** Relations, attributs, factory
3. **Controller :** Pattern avec Form Requests et try-catch
4. **Form Requests :** Séparés Store/Update avec validations
5. **Routes :** Middleware permissions granulaires
6. **Vues :** x-forms components, traductions `__('key')`
7. **Traductions :** `php dev/translations.php fix`
8. **Tests :** Factories et tests feature

### Modification de Formulaires
1. Modifier les vues Blade avec nouvelles clés `__('...')`
2. `php dev/translations.php check` pour identifier manques
3. `php dev/translations.php fix` pour corrections auto
4. Compléter traductions spécifiques manuellement
5. Re-vérifier avec `php dev/translations.php check`

### Déploiement Production
- Dossier `dev/` exclu automatiquement (.gitignore)
- Vérifier traductions avant déploiement
- Tests de permissions sur environnement staging
- Configuration Linux case-sensitive (git config core.ignorecase false)

## 🔐 Permissions Pattern

### Convention de Nommage
- Format: `{resource}.{action}`
- Exemples: `user.create`, `company.edit`, `ticket.delete`

### Rôles Standard
```php
'super-admin' => ['*'], // Toutes permissions
'admin' => ['*.create', '*.read', '*.update', '*.delete', 'admin.*', 'reports.*'],
'manager' => ['*.create', '*.read', '*.update'], // Pas de delete
```

### Dans les Controllers
```php
try {
    $company = Company::findOrFail($id);
} catch (ModelNotFoundException $e) {
    return redirect()->route('company.index')
        ->with('error', __('crud.messages.not_found'));
}
```

## 📂 Structure des Fichiers

### Dossiers Critiques
- `dev/` - Scripts de développement (NE PAS déployer)
- `resources/lang/` - Traductions FR/EN synchronisées
- `app/Http/Requests/` - Form Requests séparés
- `resources/views/components/forms/` - x-forms components

### Fichiers de Configuration
- `config/permission.php` - Spatie permissions
- `database/seeders/PermissionSeeder.php` - Rôles et permissions
- `.env` - Configuration environnement
- `composer.json` - Scripts de développement

## 🐛 Debugging et Maintenance

### Scripts Utiles
```bash
# Développement complet (serveur, queue, logs, vite)
composer run dev

# Tests
composer run test

# Traductions
php dev/translations.php

# Vérifier syntaxe PHP
php -l fichier.php
```

### Logs et Debugging
- `storage/logs/laravel.log` - Logs application
- `php artisan pail` - Logs en temps réel (inclus dans composer run dev)
- Queue worker actif pour jobs asynchrones

## ⚠️ Points d'Attention

### Sécurité
- Toujours try-catch sur `findOrFail()`
- Validation stricte des Form Requests
- Permissions granulaires sur toutes les routes
- CSRF protection actif

### Performance
- Index sur toutes les foreign keys
- Pagination sur les listes longues
- Eager loading pour relations fréquentes

### Maintenance
- Scripts de traduction automatisés
- Structure cohérente FR/EN
- Documentation à jour
- Tests de régression

---

*Documentation créée le 22 septembre 2025*
*Dernière mise à jour : 22 septembre 2025*