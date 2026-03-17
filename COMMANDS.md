# 📋 Commandes disponibles

## Scripts Composer

```bash
composer run dev        # Lance: serveur + queue worker + logs + Vite en parallèle
composer run test       # Lance les tests unitaires avec PHPUnit
```

---

## Commandes Artisan personnalisées

### Wi-Fi & Unifi
```bash
php artisan wifi:change-password --ssid=socrate_guest --length=8
# Changer le mot de passe Wi-Fi Unifi

php artisan wifi:push-password {--ssid=socrate_guest}
# Envoyer le mot de passe du jour sur LaMetric

php artisan wifi:history {--limit=10}
# Afficher l'historique des mots de passe Wi-Fi

php artisan wifi:test-lametric
# Tester l'affichage sur LaMetric

php artisan wifi:test-connection
# Tester la connexion à l'API Unifi
```

### Génération d'outils
```bash
php artisan generate:secure-keys {--lines=132} {--length=32}
# Générer des clés aléatoires sécurisées (32 caractères)

php artisan generate:passwords {--lines=132} {--length=32} {--export=}
# Générer des mots de passe très sécurisés

php artisan make:crud-bootstrap {model} {--table=} {--force}
# Générer des vues Blade Bootstrap 5 pour un modèle
# Exemple: php artisan make:crud-bootstrap Company
```

### Tests & Emails
```bash
php artisan email:fetch {--test} {--dry-run}
# Récupérer les emails IMAP et les convertir en tickets

php artisan test:email-reply-code {ticket_id?}
# Test le système de codes de réponse email

php artisan ticket:test-confirmation {ticket_id}
# Teste l'envoi d'un email de confirmation de ticket
```

### Monitoring
```bash
php artisan sentry:test
# Test Sentry error reporting
```

---

## Commandes Laravel natives (courantes)

### Base de données
```bash
php artisan migrate              # Exécuter les migrations
php artisan migrate:rollback     # Annuler la dernière migration
php artisan db:seed             # Lancer les seeders
php artisan tinker              # Console REPL interactive
```

### Cache & Configuration
```bash
php artisan config:clear        # Vider le cache de configuration
php artisan cache:clear         # Vider tous les caches
php artisan view:clear          # Vider le cache des vues
```

### Files & Storage
```bash
php artisan storage:link        # Créer le symlink storage/app/public
```

### Queue
```bash
php artisan queue:work          # Lancer le worker de queue
php artisan queue:listen        # Écouter les jobs (avec rechargement)
php artisan queue:failed        # Lister les jobs échoués
```

### Debug
```bash
php artisan pail                # Afficher les logs en temps réel
php artisan pail --timeout=0    # Afficher les logs indéfiniment
```

---

## 💡 Conseil
Pour le développement, utilisez `composer run dev` qui lance tout en parallèle (serveur, queue, logs et Vite).
