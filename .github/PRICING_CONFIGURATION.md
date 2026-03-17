# Documentation Tarification

## Configuration Centralisée des Prix et Validités

Tous les prix et validités des tickets sont centralisés pour éviter les erreurs lors des modifications.

### 📍 Fichiers de Configuration

#### 1. Variables d'environnement (`.env`)
```env
# Prix unitaires HT par catégorie (en euros)
TICKET_PRICE_UNIT=69
TICKET_PRICE_P10=59
TICKET_PRICE_P50=55
TICKET_PRICE_P100=49
TICKET_PRICE_P400=45

# Validité des tickets (en mois)
TICKET_VALIDITY_UNIT=6
TICKET_VALIDITY_P10=6
TICKET_VALIDITY_P50=12
TICKET_VALIDITY_P100=12
TICKET_VALIDITY_P400=12
```

#### 2. Fichier de configuration (`config/pricing.php`)
Lit les valeurs depuis `.env` et les rend disponibles dans l'application.

#### 3. Helpers (`app/Helpers/Helper.php`)
Méthodes statiques pour accéder facilement aux prix et validités :
- `Helper::getTicketPrice('unit')` → Prix HT d'un ticket unitaire
- `Helper::getAllTicketPrices()` → Tous les prix
- `Helper::getTicketValidity('p10')` → Validité en mois du pack 10
- `Helper::getAllTicketValidities()` → Toutes les validités
- `Helper::calculateTTC(69)` → Calcule le TTC (69 * 1.20 = 82.80)
- `Helper::getValidityLabel('p50')` → "Validité 12 mois"

### 🔄 Flux de Données

```
.env 
  ↓
config/pricing.php 
  ↓
app/Helpers/Helper.php 
  ↓
┌─────────────────────┬─────────────────────┐
│  Views (Blade)      │  JavaScript         │
│  _tarif.blade.php   │  window.ticketConfig│
│  _simulateur.blade  │  script-home.blade  │
└─────────────────────┴─────────────────────┘
```

### ✏️ Comment Modifier les Tarifs

#### Changement Simple (prix ou validité)
1. Modifier le fichier `.env`
2. Rafraîchir la page (aucun cache à vider)

#### Ajouter une Nouvelle Catégorie
1. Ajouter dans `.env` :
   ```env
   TICKET_PRICE_P200=45
   TICKET_VALIDITY_P200=18
   ```
2. Ajouter dans `config/pricing.php` :
   ```php
   'prices' => [
       // ... existing
       'p200' => (float) env('TICKET_PRICE_P200', 45),
   ],
   'validity' => [
       // ... existing
       'p200' => (int) env('TICKET_VALIDITY_P200', 18),
   ],
   ```
3. Ajouter dans les vues si nécessaire

### 🎯 Utilisation dans les Vues Blade

```blade
{{-- Prix HT --}}
{{ \App\Helpers\Helper::getTicketPrice('unit') }}

{{-- Prix total pour un pack --}}
{{ \App\Helpers\Helper::getTicketPrice('p10') * 10 }}

{{-- Label de validité formaté --}}
{{ \App\Helpers\Helper::getValidityLabel('p50') }}

{{-- Prix TTC calculé --}}
{{ \App\Helpers\Helper::calculateTTC(\App\Helpers\Helper::getTicketPrice('unit')) }}
```

### 🔧 Utilisation en JavaScript

Les prix et validités sont injectés via `_pricing_config.blade.php` :

```javascript
// Accès aux prix
window.ticketConfig.prices.unit    // 69
window.ticketConfig.prices.p10     // 59

// Accès aux validités
window.ticketConfig.validities.unit  // 6
window.ticketConfig.validities.p50   // 12

// Taux de TVA
window.ticketConfig.tvaRate          // 0.20

// Calcul TTC
const priceTTC = priceHT * (1 + window.ticketConfig.tvaRate);
```

### ⚠️ Points d'Attention

1. **Toujours modifier `.env` en premier** - C'est la source de vérité unique
2. **Ne jamais mettre de prix en dur** dans les vues ou le JS
3. **Vider le cache** si les changements ne s'appliquent pas :
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
4. **Tester sur tous les écrans** :
   - Page d'accueil (section tarifs)
   - Simulateur (calculs dynamiques)
   - Responsive (mobile/tablette/desktop)

### 📊 Exemple Complet de Modification

**Scénario** : Augmenter le prix du ticket unitaire de 69€ à 75€

1. Éditer `.env` :
   ```env
   TICKET_PRICE_UNIT=75
   ```

2. Rafraîchir la page → Les changements apparaissent :
   - ✅ Section tarifs : "75€"
   - ✅ Simulateur : recalcul automatique
   - ✅ Prix TTC : 90€ (75 * 1.20)

### 🧪 Vérification

Après modification, vérifier :
- [ ] Page d'accueil → Section "Tarifs transparents"
- [ ] Simulateur → Tableau des résultats
- [ ] Switch HT/TTC → Calculs corrects
- [ ] Colonnes masquées/affichées selon le mode
- [ ] Validités affichées correctement

### 💡 Avantages du Système

✅ **Une seule source de vérité** (principe DRY)  
✅ **Moins d'erreurs** (pas de duplication)  
✅ **Modification rapide** (un seul fichier)  
✅ **Cohérence garantie** (partout dans l'app)  
✅ **Maintenabilité** (code centralisé)

---

**Note** : Ce système respecte le principe fondamental du projet : **SIMPLICITÉ TOUJOURS !**
