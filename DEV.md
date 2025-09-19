Laravel Command

php artisan migrate:fresh
php artisan db:seed --class=PermissionSeeder

User
- status: string(20) — Utilisateur activé/désactivé (active|inactive)
- companyId: relation ManyToOne → Company — Société d’appartenance
- firstname: string(120), nullable — Prénom
- lastname: string(120), nullable — Nom
- phone: string(50), nullable — Téléphone
- lastLogin: datetime_immutable, nullable — Dernière connexion
- agreeTerms: boolean (default false) — CGU acceptées
- channels: json, nullable — Préférences de communication {“email”:true,“sms”:true}
- note: string(255), nullable — Note interne / rôle dans la société

Company
- company_status: string(20) — Activation/désactivation métier (active|inactive)
- name: string(190) — Nom société
- siret: string(20), nullable — Identifiant SIRET
- vatNumber: string(32), nullable — TVA intracommunautaire
- email: string(190), nullable — E-mail générique
- phone: string(50), nullable — Téléphone général
- website: string(190), nullable — Site web
- addressLine1: string(190), nullable — Adresse
- addressLine2: string(190), nullable — Complément
- zip: string(20), nullable — Code postal
- city: string(120), nullable — Ville
- country: string(2), nullable — Pays (ISO-2)
- notes: text, nullable — Notes internes

Ticket
- status: string(20) — Statut métier (new|in_progress|waiting|resolved|closed|canceled)
- priority: string(20) — Niveau d’urgence (low|normal|high|urgent)
- companyId: relation ManyToOne → Company — Société concernée
- authorId: relation ManyToOne → User — Créateur du ticket (client)
- assignedTo: relation ManyToOne → User, nullable — Utilisateur en charge actuellement
- assignedAt: datetime_immutable, nullable — Date/heure de prise en charge
- folderCode: string(64), nullable — Code dossier ou référence interne
- subject: string(190) — Sujet court du ticket
- question: text, nullable — Description initiale
- due: datetime_immutable, nullable — Livraison au plus tard
- billable: boolean (default true) — Ticket facturable

TicketMessage
- status: string(20) — Message activé/désactivé (active|inactive)
- companyId: relation ManyToOne → Company — Société concernée
- ticketId: relation ManyToOne → Ticket — Ticket parent
- authorId: relation ManyToOne → User — Auteur du message
- subject: string(190) — Sujet court (utile SMS/e-mail)
- body: text — Contenu du message
- internal: boolean (default false) — Note interne non visible côté client

ConversationShare
- status: string(20) — Statut du lien (active|inactive|expired)
- ticketId: relation ManyToOne → Ticket — Ticket concerné
- companyId: relation ManyToOne → Company — Société concernée
- userId: relation ManyToOne → User — Utilisateur qui a créé le partage
- uuid: string(36) — Identifiant public unique (UUID v4) pour accès sans login
- expires: datetime_immutable — Date d’expiration (par défaut +7 jours)

TicketAttachment
- status: string(20) — PJ activée/désactivée (active|inactive)
- companyId: relation ManyToOne → Company — Société concernée
- ticketId: relation ManyToOne → Ticket — PJ globale
- messageId: relation ManyToOne → TicketMessage, nullable — PJ liée à un message
- uploadedBy: relation ManyToOne → User — Auteur upload
- filePath: string(255) — Chemin stockage
- original_name: string(190) — Nom original
- mimeType: string(120), nullable — Type MIME
- sizeBytes: bigint, nullable — Poids du fichier

TicketAssignment
- ticketId: relation ManyToOne → Ticket — Ticket concerné
- assignedTo: relation ManyToOne → User — Nouvel utilisateur en charge
- assignedAt: datetime_immutable — Quand l’attribution a eu lieu

Charge
- companyId: relation ManyToOne → Company — Société concernée
- ticketId: relation ManyToOne → Ticket — Ticket concerné
- technician_id: relation ManyToOne → User — Technicien qui saisit
- type: string(32) — Nature de la charge (billing|estimate|file_fee|byod|appointment_canceled|appointment_late)
- started_at: datetime_immutable, nullable — Début réel
- endedAt: datetime_immutable, nullable — Fin réelle
- billedUnits: decimal(12,3) (default 0.000) — Unités facturables
- note: string(255), nullable — Note du technicien

Credit
- status: string(20) — Crédit activé/désactivé (active|inactive)
- companyId: relation ManyToOne → Company — Société concernée
- type: string(10) — Type de mouvement (topup|consume|adjust)
- units: decimal(12,3) — Quantité (positif ou négatif)
- reason: string(190), nullable — Raison (optionnel)
- ticketId: relation ManyToOne → Ticket, nullable — Lien consommation ticket
- chargeId: relation ManyToOne → Charge, nullable — Lien consommation charge

Approval
- status: string(16) — Statut (pending|approved|declined|expired)
- companyId: relation ManyToOne → Company — Société concernée
- message_id: relation ManyToOne → TicketMessage — Message concerné
- requestedBy: relation ManyToOne → User — Technicien qui initie la demande
- approverUserId: relation ManyToOne → User — Approbateur (supérieur, manager, etc.)
- technicianMessage: text — Message du technicien envoyé à l’approbateur (contexte, consignes)
- channel: string(16) — Canal utilisé (email|sms)
- token: string(64) — Jeton unique pour valider/refuser
- sentAt: datetime_immutable, nullable — Date envoi
- delivered_at: datetime_immutable, nullable — Date distribution (si dispo)
- approvedAt: datetime_immutable, nullable — Date réponse approbateur
- responseComment: string(255), nullable — Commentaire optionnel approbateur
