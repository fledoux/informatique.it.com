# Documentation des Services et Fonctions

## Vue d'ensemble

Ce document référence toutes les fonctions centralisées du projet pour **éviter les duplications** et maintenir un code maintenable.

---

## 🔧 Services

### 1. `AttachmentService` (`app/Services/AttachmentService.php`)

**Responsabilité** : Gestion centralisée de tous les uploads de fichiers vers S3 et création des enregistrements `TicketAttachment`.

**Configuration requise** :
- AWS S3 via SDK direct (pas Flysystem)
- Variables d'environnement : `AWS_BUCKET`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`
- Configuration : `config/attachments.php`

#### Méthodes publiques :

##### `validateFile(UploadedFile $file): void`
Valide un fichier uploadé selon les règles configurées.

**Validations** :
- Taille maximale : `config('attachments.max_size')`
- Extensions bloquées : `config('attachments.blocked_extensions')`

**Exceptions** : Lance une `\Exception` si validation échoue.

**Utilisé par** : Toutes les méthodes d'upload.

---

##### `uploadAttachment(UploadedFile $file, int $ticketId, int $companyId, int $uploadedBy, ?int $messageId = null): TicketAttachment`
Upload un fichier unique depuis un formulaire web.

**Paramètres** :
- `$file` : Instance `UploadedFile` de Laravel
- `$ticketId` : ID du ticket
- `$companyId` : ID de la société
- `$uploadedBy` : ID de l'utilisateur qui upload
- `$messageId` : (optionnel) ID du message si PJ d'un message

**Retour** : Instance `TicketAttachment` créée.

**Processus** :
1. Valide le fichier avec `validateFile()`
2. Génère un nom aléatoire (32 caractères)
3. Upload vers S3 : `ticket/attachments/{ticketId}/{randomName}.{ext}`
4. Crée l'enregistrement en DB
5. Log chaque étape

**Utilisé par** : `uploadMultipleAttachments()`.

---

##### `uploadMultipleAttachments(array $files, int $ticketId, int $companyId, int $uploadedBy, ?int $messageId = null): array`
Upload plusieurs fichiers depuis un formulaire web.

**Paramètres** :
- `$files` : Tableau de `UploadedFile[]`
- Autres : identiques à `uploadAttachment()`

**Retour** : Tableau de `TicketAttachment[]` créés (les fichiers en erreur sont skippés).

**Gestion d'erreurs** :
- Les erreurs d'upload sont loggées mais n'interrompent pas le traitement des autres fichiers
- Capture `S3Exception` et `Exception` séparément

**Utilisé par** :
- `TicketMessageController::store()` - Upload de PJ lors de la création d'un message
- `TicketAttachmentController::store()` - Upload manuel de PJ

---

##### `uploadEmailAttachments(array $attachments, int $ticketId, int $companyId, int $uploadedBy, ?int $messageId = null): array`
Upload plusieurs fichiers extraits d'un email IMAP.

**Paramètres** :
- `$attachments` : Tableau avec structure `['data' => binary, 'filename' => string, 'mime_type' => string, 'size' => int]`
- Autres : identiques aux autres méthodes

**Différences avec `uploadMultipleAttachments()` :
- Format d'entrée : données brutes d'email vs `UploadedFile`
- Utilise `$attachment['data']` directement au lieu de `file_get_contents()`
- Extension extraite avec `pathinfo()` au lieu de `getClientOriginalExtension()`

**Utilisé par** :
- `ImapService::createTicketFromEmail()` - Extraction de PJ depuis emails reçus

---

### 2. `ImapService` (`app/Services/ImapService.php`)

**Responsabilité** : Récupération des emails via IMAP et création automatique de tickets.

**Délégation** : Utilise `AttachmentService::uploadEmailAttachments()` pour les pièces jointes.

#### Méthodes principales :

##### `createTicketFromEmail(...)`
Crée un ticket depuis un email reçu.

**Gestion des PJ** :
```php
$attachments = $this->extractAttachments($emailId, $structure);
$attachmentService = new \App\Services\AttachmentService();
$attachmentService->uploadEmailAttachments($attachments, $ticket->id, $ticket->company_id, $user->id);
```

**⚠️ Ne duplique PLUS le code d'upload** - utilise le service centralisé.

---

### 3. `SmsService` (`app/Services/SmsService.php`)

**Responsabilité** : Envoi de SMS via AWS SNS.

**Méthode** : `send(string $phoneNumber, string $message): bool`

---

## 🎮 Contrôleurs

### `TicketMessageController`

#### `store(TicketMessageStoreRequest $request)`
Crée un message de ticket (réponse ou note interne).

**Gestion des PJ** :
```php
if ($request->hasFile('files')) {
    $attachmentService = new AttachmentService();
    $attachmentService->uploadMultipleAttachments(
        $request->file('files'),
        $ticket->id,
        $ticket->company_id,
        Auth::id(),
        $ticketMessage->id  // ← Lien avec le message
    );
}
```

**⚠️ Ne duplique PLUS le code d'upload** - utilise le service centralisé.

---

### `TicketAttachmentController`

#### `store(TicketAttachmentStoreRequest $request)`
Upload manuel de PJ sur un ticket existant.

**Gestion des PJ** :
```php
$attachmentService = new \App\Services\AttachmentService();
$uploadedAttachments = $attachmentService->uploadMultipleAttachments(
    $request->file('files'),
    $ticket->id,
    $ticket->company_id,
    $user->id,
    null  // ← Pas de message, upload direct sur ticket
);
```

**⚠️ Ne duplique PLUS le code d'upload** - utilise le service centralisé.

---

## 🛠️ Helpers

### `Helper` (`app/Helpers/Helper.php`)

Méthodes statiques utilitaires **réutilisables** dans toute l'application.

#### Méthodes liées aux fichiers :

##### `formatBytes(int $bytes, int $precision = 2): string`
Formate une taille en bytes vers un format lisible (Ko, Mo, Go, To).

**Exemple** :
```php
Helper::formatBytes(20971520);  // → "20 Mo"
```

**Utilisé par** : `TicketAttachment::getFormattedSize()`, affichage dans les vues.

---

##### `getMaxFileSize(): int`
Retourne la taille maximale autorisée pour les uploads (en bytes).

**Source** : `config('attachments.max_size', 20971520)`

**Utilisé par** : Validations, vues.

---

##### `getMaxFileSizeFormatted(): string`
Retourne la taille maximale formatée pour affichage.

**Exemple** :
```php
Helper::getMaxFileSizeFormatted();  // → "20 Mo"
```

**Utilisé par** : Formulaires d'upload (affichage de la limite).

---

##### `getFileIcon(string $filename, ?string $mimeType = null): array`
Retourne l'icône FontAwesome et la couleur selon le type de fichier.

**Paramètres** :
- `$filename` : Nom du fichier (pour extraire l'extension)
- `$mimeType` : (optionnel) Type MIME - prioritaire sur l'extension

**Retour** : Array avec `['icon' => 'fa-file-pdf', 'color' => 'text-danger']`

**Exemple** :
```php
$icon = Helper::getFileIcon('document.pdf');
// → ['icon' => 'fa-file-pdf', 'color' => 'text-danger']

$icon = Helper::getFileIcon('photo.jpg', 'image/jpeg');
// → ['icon' => 'fa-file-image', 'color' => 'text-info']
```

**Types supportés** :
- PDF : icône rouge (`text-danger`)
- Word : icône bleue (`text-primary`)
- Excel : icône verte (`text-success`)
- PowerPoint : icône jaune (`text-warning`)
- Images : icône cyan (`text-info`)
- Archives ZIP : icône noire (`text-dark`)
- Texte : icône par défaut
- Défaut : `fa-file` gris (`text-secondary`)

**Utilisé par** : Vues d'affichage des pièces jointes.

---

#### Autres méthodes :

##### `generateInitials(string $firstname, string $lastname): string`
Génère les initiales depuis prénom + nom.

**Exemple** :
```php
Helper::generateInitials('Jean', 'Dupont');  // → "JD"
```

---

##### `formatPhone(string $phone): string`
Formate un numéro français au format `01 23 45 67 89`.

---

##### `mailTo(string $email, string $name = null, array $attributes = []): string`
Génère un lien `<a href="mailto:...">` style FuelPHP.

---

##### `asLetters(int $number): string`
Convertit un nombre en lettres (1-10) pour l'affichage UI.

**Exemple** :
```php
Helper::asLetters(2);  // → "deux"
```

---

## 📊 Modèles

### `TicketAttachment`

#### Méthodes importantes :

##### `getFormattedSize(): string`
Retourne la taille formatée du fichier.

**Implémentation** :
```php
return \App\Helpers\Helper::formatBytes($this->size_bytes);
```

**⚠️ Utilise Helper** - pas de duplication.

---

##### `deleteWithFile(): bool`
Supprime le fichier de S3 ET l'enregistrement DB.

**Utilisé par** : Controller `destroy()`, cascade delete.

---

### `Ticket`

#### Event `boot()` :
```php
static::deleting(function ($ticket) {
    // Supprimer tous les fichiers S3 du ticket
    $attachments = $ticket->attachments;
    foreach ($attachments as $attachment) {
        $attachment->deleteWithFile();
    }
});
```

**Cascade delete automatique** des PJ lors de la suppression d'un ticket.

---

## 📝 Configuration

### `config/attachments.php`

**Paramètres centralisés** :
- `max_size` : 20971520 (20 Mo)
- `allowed_mime_types` : [] (vide = tout autorisé)
- `blocked_extensions` : ['exe', 'bat', 'sh', 'php', 'js', 'vbs', 'dll', 'jar', 'ps1', 'app', 'deb', 'rpm', 'dmg', 'pkg']
- `temporary_url_minutes` : 5

**Utilisé par** :
- `AttachmentService` (validation)
- Tous les contrôleurs (affichage limites)
- Request validation

---

## 🔐 Règles de développement

### ⚠️ IMPÉRATIF : Respecter le pattern MVC

#### ❌ JAMAIS de requêtes DB dans les vues

**MAUVAIS** :
```blade
@php
    $messageAttachments = $ticket->attachments()
        ->where('message_id', $message->id)
        ->where('status', 'active')
        ->get();
@endphp
@foreach ($messageAttachments as $attachment)
```

**BON** :
```php
// Dans le modèle TicketMessage
public function attachments() {
    return $this->hasMany(TicketAttachment::class, 'message_id')
        ->where('status', 'active');
}
```

```blade
@foreach ($message->attachments as $attachment)
```

**Règle** : Les vues affichent uniquement. La logique métier et les requêtes sont dans les **Modèles** et **Contrôleurs**.

---

### ⚠️ IMPÉRATIF : Éviter les duplications

#### ✅ BON :
```php
// Utiliser le service centralisé
$attachmentService = new AttachmentService();
$attachmentService->uploadMultipleAttachments($files, ...);
```

#### ❌ MAUVAIS :
```php
// Dupliquer la logique d'upload
$s3Client = new S3Client([...]);
foreach ($files as $file) {
    $s3Client->putObject([...]);
    TicketAttachment::create([...]);
}
```

---

### Pattern à suivre :

1. **Service Layer** : Logique métier réutilisable dans `app/Services/`
2. **Helper** : Fonctions utilitaires statiques dans `app/Helpers/`
3. **Controller** : Orchestration uniquement, délègue aux services
4. **Model** : Relations et méthodes spécifiques au modèle

---

## 🗺️ Cartographie des uploads de fichiers

```
┌─────────────────────────────────────────────────────────────┐
│                    Upload de fichiers                        │
└─────────────────────────────────────────────────────────────┘
                              │
                ┌─────────────┴─────────────┐
                │                           │
        [Formulaire Web]              [Email IMAP]
                │                           │
                ↓                           ↓
    TicketMessageController        ImapService
    TicketAttachmentController            │
                │                           │
                └─────────────┬─────────────┘
                              ↓
                    AttachmentService
                              │
                ┌─────────────┴─────────────┐
                │                           │
    uploadMultipleAttachments    uploadEmailAttachments
           (UploadedFile[])         (Array with data)
                │                           │
                └─────────────┬─────────────┘
                              ↓
                    uploadAttachment()
                              │
                    ┌─────────┴─────────┐
                    │                   │
              validateFile()      AWS S3 Upload
                    │                   │
                    └─────────┬─────────┘
                              ↓
                    TicketAttachment::create()
```

---

## 📋 Checklist avant d'ajouter une fonction

Avant de créer une nouvelle fonction, vérifier :

1. ✅ Existe-t-elle déjà dans un `Helper` ?
2. ✅ Existe-t-elle dans un `Service` ?
3. ✅ Peut-elle être mutualisée avec une fonction existante ?
4. ✅ Si oui : **adapter la fonction existante** avec des paramètres optionnels
5. ✅ Si non : créer dans le bon endroit (Service > Helper > Model > Controller)

---

## 🔄 Historique des refactorisations

### 2025-10-08 : Centralisation des uploads

**Avant** :
- Code d'upload dupliqué dans 3 endroits : `TicketMessageController`, `TicketAttachmentController`, `ImapService`
- ~100 lignes dupliquées avec S3Client, validation, logging

**Après** :
- 1 seul `AttachmentService` avec 2 méthodes adaptées
- Réduction de ~300 lignes → ~150 lignes
- Maintenance centralisée

**Bénéfices** :
- ✅ Un seul endroit pour modifier la logique d'upload
- ✅ Validation cohérente partout
- ✅ Logging uniforme
- ✅ Tests plus simples à écrire

---

## 📚 Références

- Architecture : `.github/copilot-instructions.md`
- Data model : `DEV.md`
- Config : `config/attachments.php`
- Ce document : `SERVICES_DOCUMENTATION.md`
