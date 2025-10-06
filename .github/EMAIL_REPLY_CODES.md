# Système de Codes de Réponse Email

## Vue d'ensemble

Le système de codes de réponse permet de gérer automatiquement les réponses par email aux tickets de support. Chaque email envoyé contient un code unique qui permet d'identifier le ticket associé lors d'une réponse.

## Architecture

### Service Principal : `EmailReplyCodeService`

Le service centralise toute la logique de gestion des codes de réponse :

- **Génération de codes** : Format `ID.XXXXXXXXX` (ID ticket + point + 9 caractères aléatoires)
- **Extraction des codes** : Analyse des emails entrants pour détecter les codes
- **Nettoyage du contenu** : Suppression du contenu après le code de réponse
- **Validation des codes** : Vérification du format et de la validité

### Format des Codes

```
Format : ### ID.XXXXXXXXX ###
Exemple : ### 15.KD8BSBS35 ###

Où :
- ID = ID du ticket (nombre entier : 1, 15, 142...)
- XXXXXXXXX = 9 caractères aléatoires en majuscules (A-Z, 0-9)
```

## Flux de Fonctionnement

### 1. Création d'un Ticket par Email

```
Email entrant → ImapService → Nouveau ticket → Email de confirmation avec code
```

1. **Reception IMAP** : Email traité par `ImapService`
2. **Vérification** : Pas de code de réponse détecté
3. **Création** : Nouveau ticket créé
4. **Confirmation** : Email avec code de réponse envoyé

### 2. Réponse à un Ticket

```
Email avec code → ImapService → Extraction du code → Ajout au ticket existant
```

1. **Reception** : Email contenant un code `### XXXYYYYY ###`
2. **Extraction** : Code détecté et extrait
3. **Validation** : Ticket correspondant trouvé
4. **Nettoyage** : Contenu nettoyé (suppression après le code)
5. **Ajout** : Message ajouté au ticket existant

## Intégration IMAP

### Méthode `processEmail()` Modifiée

```php
// Vérifier s'il y a un code de réponse
$replyCode = EmailReplyCodeService::extractReplyCode($body);

if ($replyCode) {
    // Réponse à un ticket existant
    $existingTicket = EmailReplyCodeService::getTicketFromReplyCode($replyCode);
    if ($existingTicket) {
        $cleanedBody = EmailReplyCodeService::cleanEmailContent($body);
        return $this->addMessageToTicket($existingTicket, $senderEmail, $cleanedBody);
    }
}

// Nouveau ticket
$ticket = $this->createTicketFromEmail($user, $subject, $body, $messageId);
```

### Gestion des Réponses

La méthode `addMessageToTicket()` :
- Trouve l'utilisateur expéditeur
- Ajoute le contenu nettoyé au ticket
- Met à jour le statut en "in_progress"
- Log les actions pour traçabilité

## Configuration des Emails

### Mail de Confirmation Modifié

`TicketConfirmationMail` intègre automatiquement le code :

```php
$contentWithReplyCode = EmailReplyCodeService::buildEmailWithReplyCode(
    $confirmationContent, 
    $this->ticket
);
```

### Template d'Email avec Code

```
Votre message de confirmation...

────────────────────────────────────────
Pour répondre à ce ticket, répondez directement à cet email.
Code de réponse : ### 15.KD8BSBS35 ###
Ne supprimez pas cette ligne lors de votre réponse.
```

## API du Service

### Méthodes Principales

```php
// Génération de codes
EmailReplyCodeService::generateReplyCode(Ticket $ticket): string
EmailReplyCodeService::formatCodeForEmail(string $code): string

// Traitement des emails
EmailReplyCodeService::extractReplyCode(string $emailContent): ?string
EmailReplyCodeService::cleanEmailContent(string $emailContent): string

// Validation et récupération
EmailReplyCodeService::isValidReplyCode(string $code): bool
EmailReplyCodeService::getTicketFromReplyCode(string $code): ?Ticket

// Construction d'emails
EmailReplyCodeService::buildEmailWithReplyCode(
    string $messageContent, 
    Ticket $ticket, 
    ?string $existingCode = null
): string
```

### Exemples d'Utilisation

```php
// Créer un email avec code
$ticket = Ticket::find(15);
$message = "Votre demande a été traitée...";
$emailWithCode = EmailReplyCodeService::buildEmailWithReplyCode($message, $ticket);

// Traiter une réponse
$incomingEmail = "Ma réponse... ### 15.KD8BSBS35 ### Contenu original...";
$code = EmailReplyCodeService::extractReplyCode($incomingEmail); // "15.KD8BSBS35"
$ticket = EmailReplyCodeService::getTicketFromReplyCode($code); // Ticket #15
$cleanContent = EmailReplyCodeService::cleanEmailContent($incomingEmail); // "Ma réponse..."
```

## Tests et Debug

### Commande de Test

```bash
# Tester avec le dernier ticket
php artisan test:email-reply-code

# Tester avec un ticket spécifique
php artisan test:email-reply-code 15
```

La commande teste :
- ✅ Génération de codes
- ✅ Formatage pour email
- ✅ Construction d'emails complets
- ✅ Extraction de codes
- ✅ Récupération de tickets
- ✅ Nettoyage de contenu
- ✅ Validation des formats

### Logs de Debug

Le service log automatiquement :
- Codes de réponse trouvés
- Tickets associés
- Erreurs de validation
- Messages ajoutés aux tickets

## Sécurité et Robustesse

### Validation des Codes

- **Format strict** : 8 caractères alphanumériques majuscules
- **Vérification d'existence** : Le ticket doit exister en base
- **Nettoyage sécurisé** : Suppression de tout contenu après le code

### Gestion d'Erreurs

- Code invalide → Traitement comme nouveau ticket
- Ticket inexistant → Log d'erreur + nouveau ticket
- Utilisateur introuvable → Log d'avertissement

### Performance

- **Extraction rapide** : Regex optimisée
- **Cache-friendly** : Pas de cache nécessaire
- **Log minimal** : Seulement les événements importants

## Extension Future

### Améliorations Possibles

1. **Expiration des codes** : Ajouter une durée de validité
2. **Chiffrement** : Codes chiffrés pour plus de sécurité
3. **Multi-threading** : Support de plusieurs réponses simultanées
4. **Templates avancés** : Codes dans différents formats d'emails

### Intégrations

- **API REST** : Exposer les fonctionnalités via API
- **Webhooks** : Notifications en temps réel
- **Mobile** : Support des applications mobiles