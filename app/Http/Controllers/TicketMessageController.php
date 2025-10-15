<?php

namespace App\Http\Controllers;

use App\Helpers\TicketSecurityHelper;
use App\Http\Requests\TicketMessageStoreRequest;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\AttachmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class TicketMessageController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        // Permissions standard pour les opérations CRUD
        $this->middleware('permission:ticketmessage.create')->only(['create', 'store']);
        $this->middleware('permission:ticketmessage.show')->only('show');
        $this->middleware('permission:ticketmessage.edit')->only(['edit', 'update']);
        $this->middleware('permission:ticketmessage.delete')->only('destroy');
        
        // S'assurer que l'utilisateur est authentifié
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($ticketId, $internal = null)
    {
        // Récupérer le ticket
        $ticket = \App\Models\Ticket::findOrFail($ticketId);
        
        // Déterminer si c'est une note interne
        $isInternal = ($internal === '1' || $internal === 'internal');
        
        // Vérifier que seuls les super-admin peuvent créer des notes internes
        if ($isInternal && !Auth::user()->hasRole('super-admin')) {
            return redirect()->route('ticket.show', $ticket->id)
                ->with('error', 'Seuls les super-administrateurs peuvent créer des notes internes.');
        }
        
        // Vérifications de sécurité par rôle
        $user = Auth::user();
        
        if ($user->hasRole('super-admin')) {
            // Super-admin : accès à tous les tickets
        } elseif ($user->hasAnyRole(['admin', 'manager'])) {
            // Admin/Manager : seulement les tickets de leur société
            if ($ticket->company_id !== $user->company_id) {
                return redirect()->route('ticket.show', $ticket->id)
                    ->with('error', 'Vous ne pouvez pas accéder aux tickets d\'une autre société.');
            }
        } else {
            // Utilisateur normal : seulement ses propres tickets ou ceux de sa société
            if ($ticket->company_id !== $user->company_id) {
                return redirect()->route('ticket.show', $ticket->id)
                    ->with('error', 'Vous ne pouvez pas accéder aux tickets d\'une autre société.');
            }
            
            // Pour les notes internes, on a déjà vérifié plus haut
            // Pour les réponses publiques, vérifier que c'est son ticket ou sa société
            if (!$isInternal && $ticket->author_id !== $user->id && $ticket->company_id !== $user->company_id) {
                return redirect()->route('ticket.show', $ticket->id)
                    ->with('error', 'Vous ne pouvez répondre qu\'aux tickets de votre société.');
            }
        }
        
        return view('ticketmessage.create', compact('ticket', 'isInternal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketMessageStoreRequest $request)
    {
        $validated = $request->validated();
        
        // Récupérer le ticket et vérifier les permissions
        $ticket = Ticket::findOrFail($validated['ticket_id']);
        
        // Vérifications de sécurité par rôle
        $user = Auth::user();
        
        if ($user->hasRole('super-admin')) {
            // Super-admin : accès à tous les tickets
        } elseif ($user->hasAnyRole(['admin', 'manager'])) {
            // Admin/Manager : seulement les tickets de leur société
            if ($ticket->company_id !== $user->company_id) {
                return redirect()->route('ticket.show', $ticket->id)
                    ->with('error', 'Vous ne pouvez pas accéder aux tickets d\'une autre société.');
            }
        } else {
            // Utilisateur normal : seulement ses propres tickets ou ceux de sa société
            if ($ticket->company_id !== $user->company_id) {
                return redirect()->route('ticket.show', $ticket->id)
                    ->with('error', 'Vous ne pouvez pas accéder aux tickets d\'une autre société.');
            }
            
            // Pour les réponses publiques, vérifier que c'est son ticket ou sa société
            if ($validated['status'] === 'active' && $ticket->author_id !== $user->id && $ticket->company_id !== $user->company_id) {
                return redirect()->route('ticket.show', $ticket->id)
                    ->with('error', 'Vous ne pouvez répondre qu\'aux tickets de votre société.');
            }
        }
        
        // Vérifier que seuls les super-admin peuvent créer des notes internes
        if ($validated['status'] === 'internal' && !$user->hasRole('super-admin')) {
            return redirect()->route('ticket.show', $ticket->id)
                ->with('error', 'Seuls les super-administrateurs peuvent créer des notes internes.')
                ->withInput();
        }
        
        // Si le ticket est clôturé (resolved, closed, canceled), créer un nouveau ticket
        if (in_array($ticket->status, ['resolved', 'closed', 'canceled']) && $validated['status'] === 'active') {
            $newTicket = Ticket::create([
                'status' => 'new',
                'priority' => $ticket->priority,
                'company_id' => $ticket->company_id,
                'author_id' => Auth::id(),
                'assigned_to' => $ticket->assigned_to,
                'folder_code' => $ticket->folder_code,
                'subject' => 'Re: ' . $ticket->subject,
                'question' => "Suite à la demande #" . $ticket->id . " :\n\n" . ($ticket->question ?? ''),
                'billable' => $ticket->billable,
                'source' => 'web',
            ]);
            
            // Créer le message dans le nouveau ticket
            $validated['ticket_id'] = $newTicket->id;
            $validated['author_id'] = Auth::id();
            $validated['company_id'] = $ticket->company_id;
            
            $ticketMessage = TicketMessage::create($validated);
            
            // Gérer les pièces jointes si présentes
            if ($request->hasFile('files')) {
                $attachmentService = new AttachmentService();
                $attachmentService->uploadMultipleAttachments(
                    $request->file('files'),
                    $newTicket->id,
                    $newTicket->company_id,
                    Auth::id(),
                    $ticketMessage->id
                );
            }
            
            // Envoyer email de confirmation au client
            try {
                \Illuminate\Support\Facades\Mail::to($newTicket->author->email)
                    ->send(new \App\Mail\TicketConfirmationMail($newTicket));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send confirmation email: " . $e->getMessage());
            }
            
            // Récupérer les utilisateurs de la société de support (company_id = 1) avec notifications activées
            $supportUsers = \App\Models\User::where('company_id', 1)
                ->where('status', 'active')
                ->get()
                ->filter(function ($user) {
                    $channels = $user->channels ?? [];
                    return !empty($channels['email']);
                });
            
            // Envoyer notification email avec l'historique complet
            foreach ($supportUsers as $supportUser) {
                try {
                    \Illuminate\Support\Facades\Mail::to($supportUser->email)
                        ->send(new \App\Mail\TicketReplyNotificationMail($newTicket, $ticketMessage, $supportUser->email));
                    \Illuminate\Support\Facades\Log::info("Notification email sent to {$supportUser->email} for ticket #{$newTicket->id}");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send notification email to {$supportUser->email}: " . $e->getMessage());
                }
            }
            
            // Récupérer les utilisateurs avec Pushover activé
            $pushoverUsers = \App\Models\User::where('company_id', 1)
                ->where('status', 'active')
                ->get()
                ->filter(function ($user) {
                    $channels = $user->channels ?? [];
                    return !empty($channels['sms']);
                });
            
            // Envoyer notification Pushover si au moins un utilisateur l'a activée
            if ($pushoverUsers->count() > 0) {
                $message = "🔔 Nouveau ticket créé #" . $newTicket->id . "\n";
                $message .= "Client : {$newTicket->author->name}\n";
                $message .= "Sujet : {$newTicket->subject}\n";
                $message .= "Voir : " . route('ticket.show', $newTicket->id);
                
                \App\Helpers\Helper::sendPushoverNotification('Nouveau ticket', $message);
                \Illuminate\Support\Facades\Log::info("Pushover notification sent for ticket #{$newTicket->id} to {$pushoverUsers->count()} user(s)");
            }
            
            return redirect()
                ->route('ticket.show', $newTicket->id)
                ->with('success', 'Le ticket était clôturé. Une nouvelle demande #' . $newTicket->id . ' a été créée.');
        }
        
        // Ajouter l'auteur et la société du ticket
        $validated['author_id'] = Auth::id();
        $validated['company_id'] = $ticket->company_id;

        $ticketMessage = TicketMessage::create($validated);

        // Gérer les pièces jointes si présentes
        if ($request->hasFile('files')) {
            $attachmentService = new AttachmentService();
            $attachmentService->uploadMultipleAttachments(
                $request->file('files'),
                $ticket->id,
                $ticket->company_id,
                Auth::id(),
                $ticketMessage->id
            );
        }

        // Mettre à jour le statut du ticket si c'est une réponse publique
        if ($validated['status'] === 'active') {
            $ticket->update(['status' => 'in_progress']);
            
            // Récupérer les utilisateurs de la société de support (company_id = 1) avec notifications activées
            $supportUsers = \App\Models\User::where('company_id', 1)
                ->where('status', 'active')
                ->get()
                ->filter(function ($user) {
                    $channels = $user->channels ?? [];
                    return !empty($channels['email']);
                });
            
            // Envoyer notification email avec l'historique complet
            foreach ($supportUsers as $supportUser) {
                try {
                    \Illuminate\Support\Facades\Mail::to($supportUser->email)
                        ->send(new \App\Mail\TicketReplyNotificationMail($ticket, $ticketMessage, $supportUser->email));
                    \Illuminate\Support\Facades\Log::info("Notification email sent to {$supportUser->email} for ticket #{$ticket->id}");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send notification email to {$supportUser->email}: " . $e->getMessage());
                }
            }
            
            // Récupérer les utilisateurs avec Pushover activé
            $pushoverUsers = \App\Models\User::where('company_id', 1)
                ->where('status', 'active')
                ->get()
                ->filter(function ($user) {
                    $channels = $user->channels ?? [];
                    return !empty($channels['sms']);
                });
            
            // Envoyer notification Pushover si au moins un utilisateur l'a activée
            if ($pushoverUsers->count() > 0) {
                $message = "🔔 Nouvelle réponse sur Support #{$ticket->id}\n";
                $message .= "Client : {$ticket->author->name}\n";
                $message .= "Sujet : {$ticket->subject}\n";
                $message .= "Voir : " . route('ticket.show', $ticket->id);
                
                \App\Helpers\Helper::sendPushoverNotification('Nouvelle réponse client', $message);
                \Illuminate\Support\Facades\Log::info("Pushover notification sent for ticket #{$ticket->id} to {$pushoverUsers->count()} user(s)");
            }
            
            // Envoyer email récapitulatif au client
            $shouldSendToClient = false;
            
            // Cas 1 : Le client répond lui-même → toujours envoyer
            if (Auth::id() === $ticket->author_id) {
                $shouldSendToClient = true;
            }
            // Cas 2 : Super-admin répond et coche la case
            elseif (Auth::user()->hasRole('super-admin') && isset($validated['send_email_to_client']) && $validated['send_email_to_client']) {
                $shouldSendToClient = true;
            }
            
            if ($shouldSendToClient) {
                try {
                    \Illuminate\Support\Facades\Mail::to($ticket->author->email)
                        ->send(new \App\Mail\TicketClientRecapMail($ticket, $ticketMessage));
                    \Illuminate\Support\Facades\Log::info("Client recap email sent to {$ticket->author->email} for ticket #{$ticket->id}");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send client recap email: " . $e->getMessage());
                }
            }
        }

        $messageType = $validated['status'] === 'internal' ? 'Note interne' : 'Réponse';
        
        return redirect()
            ->route('ticket.show', $ticket->id)
            ->with('success', $messageType . ' ajoutée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketMessage $ticketMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($ticketmessage)
    {
        try {
            // Récupération manuelle du TicketMessage
            $ticketMessage = TicketMessage::findOrFail($ticketmessage);

            // Vérification de sécurité
            if (!TicketSecurityHelper::canAccessTicketById(Auth::user(), $ticketMessage->ticket_id)) {
                return redirect()->route('ticket.show', $ticketMessage->ticket_id)
                    ->with('error', __('global.messages.not_found'));
            }

            return view('ticketmessage.edit', compact('ticketMessage'));
            
        } catch (ModelNotFoundException $e) {
            // Essayer de récupérer le ticket_id depuis l'URL de référence ou utiliser un ID par défaut
            $ticketId = request()->get('ticket_id') ?? 1;
            return redirect()->route('ticket.show', $ticketId)
                ->with('error', __('global.messages.not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $ticketmessage)
    {
        try {
            // Récupération manuelle du TicketMessage
            $ticketMessage = TicketMessage::findOrFail($ticketmessage);
            
            // Vérification de sécurité
            if (!TicketSecurityHelper::canAccessTicketById(Auth::user(), $ticketMessage->ticket_id)) {
                return redirect()->route('ticket.show', $ticketMessage->ticket_id)
                    ->with('error', __('global.messages.not_found'));
            }

            // Validation des champs éditables seulement
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'body' => 'required|string',
                'status' => 'required|in:active,inactive,internal',
            ]);

            // Mise à jour
            $ticketMessage->update($validated);

            return redirect()
                ->route('ticket.show', $ticketMessage->ticket_id)
                ->with('success', 'Message mis à jour avec succès');
                
        } catch (ModelNotFoundException $e) {
            // Essayer de récupérer le ticket_id depuis l'URL de référence ou utiliser un ID par défaut
            $ticketId = request()->get('ticket_id') ?? 1;
            return redirect()->route('ticket.show', $ticketId)
                ->with('error', __('global.messages.not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $ticketMessage = TicketMessage::findOrFail($id);
            
            // Sauvegarder l'ID du ticket pour la redirection
            $ticketId = $ticketMessage->ticket_id;
            
            // Vérifier les permissions (sécurité)
            if (!TicketSecurityHelper::canManageTicketMessage(Auth::user(), $ticketMessage)) {
                return redirect()->route('ticket.show', $ticketId)
                    ->with('error', 'Accès refusé pour supprimer ce message.');
            }
            
            $ticketMessage->delete();
            return redirect()->route('ticket.show', $ticketId)
                ->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            // Si on ne peut pas trouver le message, rediriger vers un ticket par défaut
            $ticketId = request()->get('ticket_id') ?? 1;
            return redirect()->route('ticket.show', $ticketId)
                ->with('error', __('global.messages.delete_not_found'));
        }
    }
}
