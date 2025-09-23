# Guide des Traductions

Documentation complète du système de traduction FR/EN de l'application.

## 🎯 État Actuel (22 septembre 2025)

✅ **171 clés de traduction** analysées  
✅ **16 fichiers FR = 16 fichiers EN** (parfaitement équilibré)  
✅ **0 traduction manquante**  
✅ **Scripts de maintenance** automatisés  

## 📁 Structure des Fichiers

### Fichiers Standards Laravel
```
resources/lang/
├── fr/
│   ├── auth.php          # Authentification
│   ├── pagination.php    # Navigation pages
│   ├── passwords.php     # Reset password
│   ├── validation.php    # Messages d'erreur validation
│   └── ...
└── en/
    ├── auth.php
    ├── pagination.php
    ├── passwords.php
    ├── validation.php
    └── ...
```

### Fichiers Métier
```
resources/lang/
├── fr/
│   ├── company.php       # Entité société
│   ├── user.php          # Entité utilisateur
│   ├── ticket.php        # Entité ticket
│   ├── contact.php       # Entité contact
│   ├── dashboard.php     # Tableau de bord
│   ├── nav.php           # Navigation
│   ├── global.php        # Éléments globaux
│   ├── crud.php          # Actions CRUD
│   ├── btn.php           # Boutons
│   ├── home.php          # Page d'accueil
│   ├── login.php         # Connexion
│   └── register.php      # Inscription
```

## 🏗️ Structure Type d'un Fichier

```php
<?php
// Fichier: resources/lang/fr/entity.php

return [
    // Métadonnées
    'entity' => 'Nom de l\'Entité',
    'id' => 'N°',
    
    // Actions CRUD
    'List' => 'Liste',
    'Edit' => 'Modifier',
    'Details' => 'Détails',
    'Actions' => 'Actions',
    'New' => 'Nouveau',
    'Save' => 'Enregistrer',
    'Back' => 'Retour',
    'Delete' => 'Supprimer',
    'Delete?' => 'Supprimer ?',
    'No data' => 'Aucune donnée',
    
    // En-têtes de colonnes (pour dashboard/listes)
    'Id' => 'N°',
    'Status' => 'Statut',
    'Priority' => 'Priorité',
    'Subject' => 'Sujet',
    'Company' => 'Entreprise',
    'AssignedTo' => 'Assigné à',
    'DueAt' => 'Échéance',
    
    // Champs de formulaire
    'fields' => [
        'name' => 'Nom',
        'email' => 'E-mail',
        'phone' => 'Téléphone',
        'status' => 'Statut',
        'company_id' => 'Entreprise',
        'created_at' => 'Créé le',
        'updated_at' => 'Modifié le',
        // ...
    ],
    
    // Énumérations/options
    'enums' => [
        'status' => [
            'active' => 'Actif',
            'inactive' => 'Inactif',
        ],
        'priority' => [
            'low' => 'Faible',
            'normal' => 'Normal',
            'high' => 'Élevée',
            'urgent' => 'Urgent',
        ],
    ],
    
    // Listes spécialisées
    'YourList' => 'Vos entités',
];
```

## 🔧 Scripts de Maintenance

### Script Principal (Recommandé)
```bash
# Aide et état actuel
php dev/translations.php

# Vérifier les traductions manquantes
php dev/translations.php check

# Corriger automatiquement + vérifier
php dev/translations.php fix
```

### Scripts Individuels
```bash
# Vérification seule
php dev/check-translations.php

# Correction seule
php dev/fix-translations.php
```

## 📝 Utilisation dans les Vues

### Clés Simples
```blade
{{ __('user.entity') }}           # "Utilisateur"
{{ __('global.Save') }}             # "Enregistrer"
{{ __('global.Actions') }}        # "Actions"
```

### Clés Imbriquées
```blade
{{ __('user.fields.email') }}     # "E-mail"
{{ __('ticket.enums.status.new') }} # "Nouveau"
{{ __('company.fields.siret') }}  # "Numéro SIRET"
```

### Dans les Titres de Page
```blade
@section('title', __('user.List'))
@section('title', __('global.Page not found'))
```

### Dans les Formulaires x-forms
```blade
<x-forms.input 
    name="email" 
    label="{{ __('user.fields.email') }}" 
    type="email" 
/>

<x-forms.select 
    name="status" 
    label="{{ __('user.fields.status') }}" 
    :options="[
        'active' => __('user.status.active'),
        'inactive' => __('user.status.inactive'),
    ]"
/>
```

## 🚨 Conventions Importantes

### Ponctuation dans les Clés
Les clés peuvent contenir des points finaux qui ne sont PAS des séparateurs :
```php
'We have just sent you a link to complete your registration.' => 'Nous venons...'
```

### Gestion Case Sensitivity
Pour la production Linux :
```bash
git config core.ignorecase false
```

### Nommage des Fichiers
- **Cohérence :** Même nom de fichier en FR et EN
- **Convention :** Nom de l'entité au singulier (`user.php`, pas `users.php`)
- **Alphabétique :** Ranger les clés par ordre logique dans le fichier

## 🔄 Workflow Traductions

### 1. Développement d'une Nouvelle Feature
```bash
# 1. Créer/modifier vues Blade avec __('new.key')
# 2. Vérifier les manques
php dev/translations.php check

# 3. Corriger automatiquement
php dev/translations.php fix

# 4. Compléter manuellement si nécessaire
# 5. Re-vérifier
php dev/translations.php check
```

### 2. Ajout d'une Nouvelle Entité
```bash
# 1. Le script fix-translations.php détecte automatiquement
# 2. Crée la structure de base avec mapping des champs
# 3. Vérifie la cohérence FR/EN
php dev/translations.php fix
```

### 3. Maintenance Régulière
```bash
# Vérification hebdomadaire recommandée
php dev/translations.php check
```

## 🐛 Troubleshooting

### "Traductions manquantes" mais clés présentes
- Vérifier la structure imbriquée (`fields.name` vs `name`)
- Vérifier la ponctuation exacte dans la clé
- Re-lancer le script après correction

### Fichiers FR/EN déséquilibrés
```bash
# Identifier les fichiers manquants
ls resources/lang/fr/ | wc -l
ls resources/lang/en/ | wc -l

# Créer les fichiers Laravel manquants
# pagination.php, passwords.php, validation.php
```

### Erreurs de syntaxe après modification
```bash
# Vérifier syntaxe PHP
php -l resources/lang/fr/fichier.php
```

## 📊 Statistiques Actuelles

- **Pages d'erreur :** Totalement internationalisées (404, 500)
- **Authentification :** Forms login/register avec x-forms
- **Navigation :** Permissions et traductions cohérentes
- **CRUD :** Messages standardisés dans `crud.php`
- **Validation :** 138 règles Laravel traduites

## ⚡ Scripts Techniques

### Extraction des Clés Utilisées
```bash
# Le script analyse automatiquement :
# - Tous les fichiers .blade.php
# - Patterns __('key'), __("key")
# - Gestion des clés imbriquées
# - Clés avec ponctuation finale
```

### Correction Automatique
```bash
# Le script fix-translations.php :
# - Détecte les entités (company, user, ticket, contact)
# - Mappe automatiquement les champs standards
# - Crée les structures enum cohérentes
# - Préserve les traductions existantes
```

---

*Guide créé le 22 septembre 2025*
*Système de traduction 100% opérationnel et automatisé*