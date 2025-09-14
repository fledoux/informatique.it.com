User
- user_status: string(20) — Utilisateur activé/désactivé (active|inactive)
- company_id: relation ManyToOne → Company — Société d’appartenance
- first_name: string(120), nullable — Prénom
- last_name: string(120), nullable — Nom
- phone: string(50), nullable — Téléphone
- last_login_at: datetime_immutable, nullable — Dernière connexion
- agree_terms: boolean (default false) — CGU acceptées
- channels: json, nullable — Préférences de communication {“email”:true,“sms”:true}
- note: string(255), nullable — Note interne / rôle dans la société
- created_at: datetime_immutable — Date de création
- updated_at: datetime_immutable — Dernière modification

Company
- company_status: string(20) — Activation/désactivation métier (active|inactive)
- name: string(190) — Nom société
- siret: string(20), nullable — Identifiant SIRET
- vat_number: string(32), nullable — TVA intracommunautaire
- email: string(190), nullable — E-mail générique
- phone: string(50), nullable — Téléphone général
- website: string(190), nullable — Site web
- address_line1: string(190), nullable — Adresse
- address_line2: string(190), nullable — Complément
- postal_code: string(20), nullable — Code postal
- city: string(120), nullable — Ville
- country_code: string(2), nullable — Pays (ISO-2)
- notes: text, nullable — Notes internes
- billing_contact_id: relation ManyToOne → User — Contact facturation (ROLE_BILLING)
- created_at: datetime_immutable — Date de création
- updated_at: datetime_immutable — Dernière modification

Ticket
- ticket_status: string(20) — Statut métier (new|in_progress|waiting|resolved|closed|canceled)
- priority: string(20) — Niveau d’urgence (low|normal|high|urgent)
- company_id: relation ManyToOne → Company — Société concernée
- created_by_id: relation ManyToOne → User — Créateur du ticket (client)
- assigned_to_id: relation ManyToOne → User, nullable — Utilisateur en charge actuellement
- assigned_at: datetime_immutable, nullable — Date/heure de prise en charge
- code_folder: string(64), nullable — Code dossier ou référence interne
- subject: string(190) — Sujet court du ticket
- question: text, nullable — Description initiale
- due_at: datetime_immutable, nullable — Livraison au plus tard
- is_billable: boolean (default true) — Ticket facturable
- created_at: datetime_immutable — Date de création
- updated_at: datetime_immutable — Dernière modification

TicketMessage
- ticket_message_status: string(20) — Message activé/désactivé (active|inactive)
- company_id: relation ManyToOne → Company — Société concernée
- ticket_id: relation ManyToOne → Ticket — Ticket parent
- author_id: relation ManyToOne → User — Auteur du message
- subject: string(190) — Sujet court (utile SMS/e-mail)
- body: text — Contenu du message
- is_internal: boolean (default false) — Note interne non visible côté client
- created_at: datetime_immutable — Date de création
- updated_at: datetime_immutable — Dernière modification

ConversationShare
- conversation_share_status: string(20) — Statut du lien (active|inactive|expired)
- ticket_id: relation ManyToOne → Ticket — Ticket concerné
- company_id: relation ManyToOne → Company — Société concernée
- user_id: relation ManyToOne → User — Utilisateur qui a créé le partage
- uuid: string(36) — Identifiant public unique (UUID v4) pour accès sans login
- created_at: datetime_immutable — Date de création
- updated_at: datetime_immutable — Dernière modification
- expires_at: datetime_immutable — Date d’expiration (par défaut +7 jours)

TicketAttachment
- ticket_attachment_status: string(20) — PJ activée/désactivée (active|inactive)
- company_id: relation ManyToOne → Company — Société concernée
- ticket_id: relation ManyToOne → Ticket — PJ globale
- message_id: relation ManyToOne → TicketMessage, nullable — PJ liée à un message
- uploaded_by: relation ManyToOne → User — Auteur upload
- file_path: string(255) — Chemin stockage
- original_name: string(190) — Nom original
- mime_type: string(120), nullable — Type MIME
- size_bytes: bigint, nullable — Poids du fichier
- created_at: datetime_immutable — Upload
- updated_at: datetime_immutable — Mise à jour

TicketAssignment
- ticket_id: relation ManyToOne → Ticket — Ticket concerné
- assigned_to_id: relation ManyToOne → User — Nouvel utilisateur en charge
- assigned_at: datetime_immutable — Quand l’attribution a eu lieu
- created_at: datetime_immutable — Création
- updated_at: datetime_immutable — Mise à jour

Charge
- company_id: relation ManyToOne → Company — Société concernée
- ticket_id: relation ManyToOne → Ticket — Ticket concerné
- technician_id: relation ManyToOne → User — Technicien qui saisit
- type: string(32) — Nature de la charge (billing|estimate|file_fee|byod|appointment_canceled|appointment_late)
- started_at: datetime_immutable, nullable — Début réel
- ended_at: datetime_immutable, nullable — Fin réelle
- billed_units: decimal(12,3) (default 0.000) — Unités facturables
- note: string(255), nullable — Note du technicien
- created_at: datetime_immutable — Création
- updated_at: datetime_immutable — Mise à jour

Credit
- credit_status: string(20) — Crédit activé/désactivé (active|inactive)
- company_id: relation ManyToOne → Company — Société concernée
- type: string(10) — Type de mouvement (topup|consume|adjust)
- units: decimal(12,3) — Quantité (positif ou négatif)
- reason: string(190), nullable — Raison (optionnel)
- ticket_id: relation ManyToOne → Ticket, nullable — Lien consommation ticket
- charge_id: relation ManyToOne → Charge, nullable — Lien consommation charge
- created_at: datetime_immutable — Création
- updated_at: datetime_immutable — Mise à jour

Approval
- approval_status: string(16) — Statut (pending|approved|declined|expired)
- company_id: relation ManyToOne → Company — Société concernée
- message_id: relation ManyToOne → TicketMessage — Message concerné
- requested_by_id: relation ManyToOne → User — Technicien qui initie la demande
- approver_user_id: relation ManyToOne → User — Approbateur (supérieur, manager, etc.)
- technician_message: text — Message du technicien envoyé à l’approbateur (contexte, consignes)
- channel: string(16) — Canal utilisé (email|sms)
- token: string(64) — Jeton unique pour valider/refuser
- sent_at: datetime_immutable, nullable — Date envoi
- delivered_at: datetime_immutable, nullable — Date distribution (si dispo)
- approval_responded_at: datetime_immutable, nullable — Date réponse approbateur
- response_comment: string(255), nullable — Commentaire optionnel approbateur
- created_at: datetime_immutable — Date de création
- updated_at: datetime_immutable — Dernière modification
