# Scripts de Développement

Ce dossier contient tous les scripts et outils de développement qui ne doivent **jamais être déployés en production**.

## 🔧 Scripts de Traduction

### `translations.php` ⭐ (Recommandé)
**Script de convenance tout-en-un**

```bash
# Aide et état actuel
php dev/translations.php

# Vérifier les traductions
php dev/translations.php check

# Corriger + vérifier automatiquement
php dev/translations.php fix
```

### `check-translations.php`
**Script de vérification des traductions manquantes**

```bash
php dev/check-translations.php
```

**Fonctionnalités :**
- Analyse tous les fichiers Blade pour extraire les clés `__('...')`
- Vérifie la présence des traductions dans les fichiers FR et EN
- Gère les clés imbriquées (`company.fields.status`)
- Gère les clés avec ponctuation (`register.We have just sent...`)
- Statistiques complètes : nombre de clés, fichiers analysés, traductions manquantes

**Sortie :**
- ✅ Message de succès si toutes les traductions sont présentes
- ❌ Liste détaillée des traductions manquantes avec les fichiers où elles sont utilisées
- 📊 Statistiques complètes

### `fix-translations.php`
**Script de correction automatique des traductions**

```bash
php dev/fix-translations.php
```

**Fonctionnalités :**
- Correction automatique des fichiers de traduction incomplets
- Mapping automatique des champs selon les conventions Laravel
- Création des structures d'enum cohérentes
- Ajout des traductions de base pour les entités (company, user, ticket, contact)

**Utilisation :**
Exécuter après avoir ajouté de nouvelles entités ou modifié les formulaires Blade.

## 📝 Conventions

### Structure des traductions
```php
// Fichier: resources/lang/fr/entity.php
return [
    'entity' => 'Nom de l\'entité',
    'fields' => [
        'field_name' => 'Nom du champ',
        // ...
    ],
    'enums' => [
        'status' => [
            'active' => 'Actif',
            'inactive' => 'Inactif',
        ],
    ],
];
```

### Workflow de traduction
1. Créer/modifier les vues Blade avec `__('key')`
2. Exécuter `php dev/check-translations.php` pour identifier les manques
3. Exécuter `php dev/fix-translations.php` pour les corrections automatiques
4. Compléter manuellement les traductions spécifiques si nécessaire
5. Re-vérifier avec `check-translations.php`

## 🚫 Production

**⚠️ IMPORTANT :** Ce dossier ne doit jamais être déployé en production !

Ajouter dans `.gitignore` ou dans le script de déploiement :
```bash
# Exclure du déploiement
dev/
```

## 📊 Statistiques actuelles

- ✅ **171 clés de traduction** vérifiées
- ✅ **16 fichiers FR = 16 fichiers EN** (parfaitement équilibré)
- ✅ **0 traduction manquante**
- ✅ Scripts de maintenance automatisés

---

*Scripts créés le 22 septembre 2025*
*Dernière mise à jour : 22 septembre 2025*