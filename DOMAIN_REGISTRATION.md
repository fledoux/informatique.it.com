# Système d'inscription automatique par domaine

## Fonctionnement

### 1. Vérification en temps réel (AJAX)
Lorsqu'un utilisateur saisit son email sur `/register` :
- À chaque frappe (avec debounce de 500ms), un appel API est fait vers `/api/register/check-domain`
- L'API vérifie si le domaine existe dans la table `allow_domain_registrations`
- **Si trouvé** : Les champs société sont masqués (company, address_line1, address_line2, zip, city)
- **Si non trouvé** : Les champs société restent visibles (comportement par défaut)

### 2. Enregistrement de l'utilisateur

#### Cas A : Domaine existant
```
Email: john@acme.com
Domaine: acme.com
```
- Le domaine existe dans `allow_domain_registrations` → company_id = 5
- L'utilisateur est créé avec `company_id = 5`
- Rien n'est créé

#### Cas B : Nouveau domaine
```
Email: marie@newcompany.fr
Domaine: newcompany.fr (non trouvé)
```
- Une nouvelle `Company` est créée avec :
  - name: depuis le champ "company"
  - status: 'active'
  - address_line1, address_line2, zip, city: depuis les champs du formulaire
- Le domaine `newcompany.fr` est ajouté dans `allow_domain_registrations` avec le company_id
- L'utilisateur est créé avec ce company_id

### 3. Fichiers modifiés/créés

**Nouveaux fichiers :**
- `app/Http/Controllers/Api/CheckDomainController.php` - API de vérification
- `routes/api.php` - Route API publique

**Fichiers modifiés :**
- `app/Http/Controllers/Auth/RegisterController.php` - Logique de création Company
- `resources/views/auth/register.blade.php` - AJAX jQuery avec debounce
- `resources/views/layouts/auth.blade.php` - Ajout meta CSRF token

**Fichiers existants utilisés :**
- `app/Models/AllowDomainRegistration.php` - Modèle avec relations
- `database/migrations/2025_09_30_143209_create_allow_domain_registrations_table.php` - Table

### 4. Route API

**Endpoint :** `POST /api/register/check-domain`  
**Accès :** Public (pas de middleware auth)  
**Paramètres :** `email` (requis)

**Réponse si trouvé :**
```json
{
  "found": true,
  "domain": "acme.com",
  "company_id": 5,
  "company_name": "ACME Corporation"
}
```

**Réponse si non trouvé :**
```json
{
  "found": false,
  "domain": "newcompany.fr",
  "company_id": null
}
```

### 5. UX/UI

- **Par défaut** : Tous les champs visibles
- **Domaine reconnu** : Champs société masqués automatiquement + vidés
- **Pas de message** : L'UX est silencieuse, juste le masquage des champs
- **Debounce 500ms** : Évite trop de requêtes API pendant la saisie

### 6. Sécurité

- Validation email côté serveur
- CSRF token pour les appels AJAX
- Validation des champs company obligatoires si domaine non trouvé
- Transactions implicites Laravel pour création Company + AllowDomainRegistration
