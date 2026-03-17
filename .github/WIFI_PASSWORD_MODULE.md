# Module WiFi Password Management

Ce module gère automatiquement le changement du mot de passe Wi-Fi Unifi et affiche les nouveaux mots de passe sur un affichage LaMetric.

## 📋 Configuration

### Variables d'environnement à ajouter au `.env`

```env
# Unifi Controller Configuration
UNIFI_CONTROLLER_URL=https://192.168.111.35:8443
UNIFI_API_KEY=votre_cle_api_unifi
UNIFI_SITE_NAME=default

# LaMetric Configuration
LAMETRIC_API_KEY=votre_cle_api_lametric
LAMETRIC_DEVICE_ID=votre_id_device_lametric
```

### Obtenir les credentials

#### Unifi API Key
1. Accédez au contrôleur Unifi (https://votre-ip-unifi:8443)
2. Allez dans **Paramètres** > **API Access**
3. Créez une nouvelle clé API ou utilisez une existante
4. Copiez la clé dans `UNIFI_API_KEY`

⚠️ **Format du contrôleur**: `https://IP:PORT` (ex: `https://192.168.111.35:8443`)

#### LaMetric API Key et Device ID
1. Connectez-vous à [developer.lametric.com](https://developer.lametric.com)
2. Accédez à votre profil et obtenez l'API key
3. Obtenez votre Device ID depuis l'application LaMetric

## 🚀 Installation

### 1. Exécuter la migration
```bash
php artisan migrate
```

Cela créera la table `wifi_passwords` pour stocker les mots de passe chiffrés.

### 2. Configurer les variables d'environnement
Editez votre `.env` avec les credentials collectés ci-dessus.

### 3. Configurer le scheduler (cron)
Le cron est **automatiquement configuré** pour s'exécuter chaque jour à **8h du matin**.

Pour développement local, vous pouvez tester avec:
```bash
# Exécuter le cron manuellement
php artisan schedule:run
```

Pour production, assurez-vous que le cron système est configuré:
```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

## 📱 Utilisation

### Changer le mot de passe manuellement
```bash
php artisan wifi:change-password --ssid=default --length=12
```

### Options disponibles
- `--ssid=NAME`: Nom du réseau WiFi (défaut: `default`)
- `--length=N`: Longueur du mot de passe généré (défaut: `12`)

### Exemple de sortie
```
Génération d'un nouveau mot de passe Wi-Fi pour: default
Nouveau mot de passe: aB3cDeF9hI1j
✓ Mot de passe changé sur Unifi
✓ Mot de passe enregistré en base de données
✓ Mot de passe affiché sur LaMetric
✓ Processus terminé avec succès
```

## 💾 Accès aux mots de passe stockés

### Récupérer le dernier mot de passe
```php
use App\Models\WifiPassword;

$latestPassword = WifiPassword::getLatest();
echo $latestPassword->password; // Automatiquement déchiffré
```

### Voir l'historique complet
```php
$history = WifiPassword::orderBy('changed_at', 'desc')->get();
foreach ($history as $record) {
    echo $record->password . ' - ' . $record->changed_at;
}
```

## 🔐 Sécurité

- ✅ Les mots de passe sont **chiffrés en base de données** automatiquement
- ✅ Utilisation de Laravel's `Encrypted::cast`
- ✅ Clés chiffrées stockées dans `APP_KEY` du `.env`
- ✅ Les API keys n'apparaissent jamais dans le code source

⚠️ **IMPORTANT**: Ne commitez JAMAIS le `.env` en version contrôle. Utilisez uniquement `.env.example`.

## 🐛 Troubleshooting

### "Unifi: Réseau não trouvé"
- Vérifiez que le SSID est correct (sensible à la casse)
- Vérifiez que le réseau existe dans votre contrôleur Unifi

### "LaMetric: Erreur lors de l'affichage"
- Vérifiez que l'API key LaMetric est valide
- Vérifiez que le device ID est correct
- Assurez-vous que le dispositif est connecté à Internet

### Mot de passe non changé sur Unifi
- Vérifiez l'URL du contrôleur (format: `https://IP:PORT`)
- Vérifiez que l'API key Unifi a les bonnes permissions
- Consultez les logs: `tail storage/logs/laravel.log`

## 📊 Exemple de workflow quotidien

**8h du matin:**
1. ✅ Cron exécute `wifi:change-password`
2. ✅ Génération d'un mot de passe aléatoire (ex: `k7Lm9NpQrS2t`)
3. ✅ Changement du mot de passe sur le contrôleur Unifi
4. ✅ Enregistrement chiffré en base de données
5. ✅ Affichage sur le LaMetric: `WiFi: k7Lm9NpQrS2`

## 📝 Logs

Tous les événements sont enregistrés dans `storage/logs/laravel.log`:
- ✓ Changement réussi
- ✗ Erreurs Unifi
- ✗ Erreurs LaMetric
- ℹ Information générale
