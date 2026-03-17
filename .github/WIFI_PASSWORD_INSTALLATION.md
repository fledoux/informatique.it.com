# 🚀 Guide d'Installation - Module WiFi Password

## Étapes rapides

### 1️⃣ Exécuter la migration
```bash
php artisan migrate
```

### 2️⃣ Ajouter les variables d'environnement au `.env`

Ajoutez ces lignes à votre fichier `.env`:

```env
# Unifi Controller Configuration
UNIFI_CONTROLLER_URL=https://192.168.111.35:8443
UNIFI_API_KEY=Qq81PN7bfVDfZfykVdFqiFiHKuLuD4uJ
UNIFI_SITE_NAME=default
UNIFI_VERIFY_SSL=false

# LaMetric Configuration
LAMETRIC_API_KEY=Qq81PN7bfVDfZfykVdFqiFiHKuLuD4uJ
LAMETRIC_DEVICE_ID=votre_id_device_lametric
```

⚠️ **SÉCURITÉ**: Remplacez les `API_KEY` par vos vraies clés. Ces valeurs ne doivent JAMAIS être commitées en Git.

### 3️⃣ Tester la connexion Unifi

```bash
php artisan wifi:test-connection
```

**Sortie attendue:**
```
🔍 Test de connexion Unifi en cours...
✓ Connexion Unifi réussie!

📡 Réseaux disponibles:
  ✓ WiFi-SSID
  ✓ WiFi-Guests
```

### 4️⃣ Tester LaMetric

```bash
php artisan wifi:test-lametric
```

Vérifiez que le message s'affiche sur votre appareil LaMetric.

### 5️⃣ Test manuel du changement

```bash
php artisan wifi:change-password --ssid=WiFi-SSID --length=12
```

**Sortie attendue:**
```
Génération d'un nouveau mot de passe Wi-Fi pour: WiFi-SSID
Nouveau mot de passe: aB3cDeF9hI1j
✓ Mot de passe changé sur Unifi
✓ Mot de passe enregistré en base de données
✓ Mot de passe affiché sur LaMetric
✓ Processus terminé avec succès
```

### 6️⃣ Vérifier l'historique

```bash
php artisan wifi:history --limit=10
```

## 📅 Utilisation en production

Le **cron automatique** change le mot de passe chaque jour à **8h du matin**.

Assurez-vous que cette ligne est dans votre crontab:
```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

## 📱 Commandes disponibles

| Commande | Description |
|----------|-------------|
| `php artisan wifi:change-password` | Changer le mot de passe Wi-Fi |
| `php artisan wifi:test-connection` | Tester la connexion Unifi |
| `php artisan wifi:test-lametric` | Tester l'affichage LaMetric |
| `php artisan wifi:history` | Voir l'historique des mots de passe |

## 🐛 Troubleshooting

### Erreur: "Connexion Unifi réussie" mais pas de réseaux
- Vérifiez que `UNIFI_SITE_NAME` est correct (par défaut: `default`)
- Vérifiez les logs: `tail storage/logs/laravel.log`

### Erreur: "Mot de passe non changé sur Unifi"
- Vérifiez que le SSID exact est utilisé (sensible à la casse)
- Testez avec `php artisan wifi:test-connection` pour voir les noms

### Erreur: "LaMetric: Erreur lors de l'affichage"
- Vérifiez que `LAMETRIC_API_KEY` et `LAMETRIC_DEVICE_ID` sont corrects
- Assurez-vous que le dispositif est peu allumé et connecté

### Consulter les logs
```bash
tail -f storage/logs/laravel.log | grep -i unifi
tail -f storage/logs/laravel.log | grep -i lametric
```

## 📝 Structure des fichiers

```
app/
  Console/Commands/
    ├── ChangeWifiPasswordCommand.php       # Cron quotidien
    ├── TestUnifiConnectionCommand.php      # Test Unifi
    ├── TestLaMetricCommand.php            # Test LaMetric
    └── ShowWifiPasswordHistoryCommand.php # Historique
  Models/
    └── WifiPassword.php                   # Model pour les mots de passe (chiffrés)
  Services/
    ├── UnifiService.php                   # API Unifi
    └── LaMetricService.php                # API LaMetric
config/
  └── services.php                         # Configuration (uses .env)
database/
  migrations/
    └── 2026_03_08_create_wifi_passwords_table.php
bootstrap/
  └── app.php                              # Schedule configuré (8h du matin)
```

## 🔐 Sécurité

✅ **Mots de passe chiffrés en BD**: Utilise `Encrypted::cast` de Laravel
✅ **API keys sécurisées**: Stockées dans `.env`, jamais en code source
✅ **HTTPS SSL**: Connexion chiffrée avec Unifi et LaMetric
✅ **Logs détaillés**: Tous les événements sont loggés

## 📚 Documentation complète

Voir [WIFI_PASSWORD_MODULE.md](./WIFI_PASSWORD_MODULE.md) pour plus de détails.
