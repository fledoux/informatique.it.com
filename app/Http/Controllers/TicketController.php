<?php

namespace App\Http\Controllers;

use \App\Models\Ticket;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use App\Helpers\TicketSecurityHelper;

class TicketController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        $this->middleware('permission:ticket.index')->only('index');
        $this->middleware('permission:ticket.create')->only(['create', 'store']);
        $this->middleware('permission:ticket.show')->only('show');
        $this->middleware('permission:ticket.edit')->only(['edit', 'update', 'mergeForm', 'merge']);
        $this->middleware('permission:ticket.delete')->only('destroy');
    }

    public function index()
    {
        $tickets = Ticket::getAllForUser();
        return view('ticket.index', compact('tickets'));
    }

    public function create()
    {
        $ticket = new Ticket();
        return view('ticket.create', compact('ticket'));
    }

    public function store(TicketStoreRequest $request)
    {
        $data = $request->validated();
        
        // Les checkbox non cochées ne sont pas envoyées, on met false par défaut
        $data['billable'] = $data['billable'] ?? false;
        
        $user = Auth::user();

        // Déterminer company_id et author_id selon le rôle
        if ($user->hasRole('super-admin')) {
            // Super-admin : doit avoir author_id, on récupère le company_id de cet auteur
            if (isset($data['author_id'])) {
                $author = \App\Models\User::find($data['author_id']);
                if ($author) {
                    $data['company_id'] = $author->company_id;
                } else {
                    // Fallback si l'auteur n'existe pas
                    $data['company_id'] = $user->company_id;
                    $data['author_id'] = $user->id;
                }
            } else {
                // Si pas d'author_id fourni, utiliser le super-admin lui-même
                $data['company_id'] = $user->company_id;
                $data['author_id'] = $user->id;
            }
        } else {
            // Pour tous les autres utilisateurs (admin, manager, user)
            if (isset($data['author_id'])) {
                $author = \App\Models\User::find($data['author_id']);
                if ($author) {
                    $data['company_id'] = $author->company_id;
                } else {
                    $data['company_id'] = $user->company_id;
                    $data['author_id'] = $user->id;
                }
            } else {
                $data['company_id'] = $user->company_id;
                $data['author_id'] = $user->id;
            }
        }

        // Si l'utilisateur est manager ou user, on force certains champs supplémentaires
        if ($user->hasRole(['manager', 'user'])) {
            // Valeurs par défaut pour manager/user
            $data['status'] = 'new';
            $data['assigned_to'] = null;
            $data['assigned_at'] = null;
            $data['due'] = null;
            $data['billable'] = true;
        }

        $ticket = Ticket::create($data);

        // Envoyer la notification Pushover
        $title = '#' . $ticket->id . ' Question';
        $message = 'Création de Ticket';
        Helper::sendPushoverNotification($title, $message);

        // Envoyer l'email de confirmation
        try {
            \Illuminate\Support\Facades\Mail::to($ticket->author->email)->send(new \App\Mail\TicketConfirmationMail($ticket));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send ticket confirmation email: ' . $e->getMessage());
        }

        return redirect()->route('ticket.show', $ticket->id)->with('success', __('global.messages.created'));
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::query()
                ->with([
                    'company',
                    'author',
                    'assignedTo',
                    'messages' => function ($query) {
                        $query->with(['author', 'attachments' => function ($q) {
                            $q->where('status', 'active');
                        }])->orderBy('created_at', 'desc');
                    },
                    'attachments' => function ($query) {
                        $query->where('status', 'active')->with('uploader');
                    },
                    'initialAttachments'
                ])
                ->findOrFail($id);
            
            // Vérifier l'autorisation avec la policy
            $this->authorize('view', $ticket);

            $user = $ticket->author;
            $company = $ticket->company;
            
            // Récupérer les pièces jointes actives (déjà chargées via eager loading)
            $attachments = $ticket->attachments->where('status', 'active')->sortByDesc('id');
            $attachmentsCount = $attachments->count();
            
            // Pré-charger les managers pour éviter la requête dans la vue
            $managers = $ticket->getManagers();

            return view('ticket.show', compact('ticket', 'user', 'company', 'attachments', 'attachmentsCount', 'managers'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $ticket = Ticket::query()->with(['company', 'author'])->findOrFail($id);
            
            // Vérifier l'autorisation avec la policy
            $this->authorize('update', $ticket);
            
            return view('ticket.edit', compact('ticket'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.edit_not_found'));
        }
    }

    public function update(TicketUpdateRequest $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $data = $request->validated();
            
            // Les checkbox non cochées ne sont pas envoyées, on met false par défaut
            $data['billable'] = $data['billable'] ?? false;
            
            $user = Auth::user();
            
            // Vérifications de sécurité par rôle
            if ($user->hasRole('super-admin')) {
                // Super-admin : accès à tous les tickets
            } elseif ($user->hasAnyRole(['admin', 'manager'])) {
                // Admin/Manager : seulement les tickets de leur société
                if ($ticket->company_id !== $user->company_id) {
                    return redirect()->route('ticket.index')
                        ->with('error', 'Vous ne pouvez pas modifier les tickets d\'une autre société.');
                }
            } else {
                // Utilisateur normal : seulement les tickets de sa société
                if ($ticket->company_id !== $user->company_id) {
                    return redirect()->route('ticket.index')
                        ->with('error', 'Vous ne pouvez pas modifier les tickets d\'une autre société.');
                }
            }

            // Déterminer le company_id en fonction de l'utilisateur sélectionné (pour tous les utilisateurs)
            if (isset($data['author_id'])) {
                $author = \App\Models\User::find($data['author_id']);
                if ($author) {
                    $data['company_id'] = $author->company_id;
                }
            } else {
                // Si pas d'author_id spécifié, garder le company_id existant du ticket
                // ou utiliser celui de l'utilisateur connecté comme fallback
                if (!$ticket->company_id) {
                    $data['company_id'] = $user->company_id;
                }
            }

            $ticket->update($data);
            return redirect()->route('ticket.show', $ticket->id)->with('success', __('global.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.update_not_found'));
        }
    }

    /**
     * Renvoyer l'email de confirmation
     */
    public function resendConfirmation($id)
    {
        die('function fermée pour le moment');
        try {
            $ticket = Ticket::with(['author', 'company'])->findOrFail($id);
            
            // Envoyer l'email de confirmation
            \Illuminate\Support\Facades\Mail::to($ticket->author->email)->send(new \App\Mail\TicketConfirmationMail($ticket));
            
            return back()->with('success', 'Email de confirmation renvoyé à ' . $ticket->author->email);
        } catch (ModelNotFoundException $e) {
            return back()->with('error', __('global.messages.not_found'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            // Empêcher l'auto-suppression
            if (strtolower('Ticket') === 'user' && Auth::check() && $ticket->id === Auth::id()) {
                return redirect()->route('ticket.index')
                    ->with('error', __('global.messages.cannot_delete_self'));
            }

            $ticket->delete();
            return redirect()->route('ticket.index')->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.delete_not_found'));
        }
    }

    /**
     * Afficher le formulaire de fusion de tickets
     */
    public function mergeForm($id)
    {
        try {
            $ticket = Ticket::findForMerge($id);
            
            if (!$ticket) {
                throw new ModelNotFoundException();
            }
            
            $availableTickets = $ticket->getAvailableTicketsForMerge();
            
            return view('ticket.merge', compact('ticket', 'availableTickets'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    /**
     * Fusionner deux tickets
     */
    public function merge($id)
    {
        try {
            $sourceTicketId = request()->input('source_ticket_id');
            
            if (!$sourceTicketId) {
                return back()->with('error', 'Veuillez sélectionner un ticket à fusionner.');
            }

            $targetTicket = Ticket::findOrFail($id); // Ticket à conserver
            $sourceTicket = Ticket::findOrFail($sourceTicketId); // Ticket à supprimer
            
            // Vérifier que les tickets appartiennent à la même entreprise
            if ($targetTicket->company_id !== $sourceTicket->company_id) {
                return back()->with('error', 'Les tickets doivent appartenir à la même entreprise.');
            }

            \Illuminate\Support\Facades\DB::beginTransaction();
            
            try {
                // 1. Migrer les messages du ticket source vers le ticket cible
                \App\Models\TicketMessage::where('ticket_id', $sourceTicket->id)
                    ->update(['ticket_id' => $targetTicket->id]);
                
                // 2. Migrer les pièces jointes du ticket source vers le ticket cible
                // Récupérer toutes les pièces jointes à déplacer
                $attachments = \App\Models\TicketAttachment::where('ticket_id', $sourceTicket->id)->get();
                
                $movedCount = 0;
                $failedCount = 0;
                
                foreach ($attachments as $attachment) {
                    if ($attachment->moveToTicket($targetTicket->id)) {
                        $movedCount++;
                    } else {
                        $failedCount++;
                        \Illuminate\Support\Facades\Log::warning(
                            "Failed to move attachment {$attachment->id} from ticket {$sourceTicket->id} to {$targetTicket->id}"
                        );
                    }
                }
                
                // Si des pièces jointes n'ont pas pu être déplacées, logger l'info mais continuer
                if ($failedCount > 0) {
                    \Illuminate\Support\Facades\Log::warning(
                        "Ticket merge: {$movedCount} attachments moved successfully, {$failedCount} failed"
                    );
                }
                
                // 3. Créer un message système pour indiquer la fusion
                $messagesCount = \App\Models\TicketMessage::where('ticket_id', $targetTicket->id)->count();
                
                $mergeDetails = "Le ticket #{$sourceTicket->id} (créé le " . $sourceTicket->created_at->format('d/m/Y à H:i') . ") a été fusionné avec ce ticket.\n\n";
                $mergeDetails .= "Sujet du ticket fusionné : {$sourceTicket->subject}\n";
                $mergeDetails .= "Contenu transféré : Question initiale + {$messagesCount} message(s)\n";
                $mergeDetails .= "Pièces jointes déplacées : {$movedCount}";
                
                if ($failedCount > 0) {
                    $mergeDetails .= " ({$failedCount} échec(s))";
                }
                
                \App\Models\TicketMessage::create([
                    'ticket_id' => $targetTicket->id,
                    'company_id' => $targetTicket->company_id,
                    'author_id' => Auth::id(),
                    'status' => 'internal',
                    'subject' => 'Fusion de tickets',
                    'body' => $mergeDetails,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // 4. Créer un message avec le contenu initial du ticket source (question de départ)
                // On le crée APRÈS le message système pour qu'il apparaisse juste au-dessus (ordre DESC)
                if ($sourceTicket->question) {
                    \App\Models\TicketMessage::create([
                        'ticket_id' => $targetTicket->id,
                        'company_id' => $sourceTicket->company_id,
                        'author_id' => $sourceTicket->author_id,
                        'status' => 'active',
                        'subject' => $sourceTicket->subject,
                        'body' => $sourceTicket->question, // Le champ 'question' du ticket → 'body' du message
                        'created_at' => now(), // Date actuelle pour l'ordre d'affichage
                        'updated_at' => now(),
                    ]);
                }
                
                // 5. Supprimer l'ancien ticket
                $sourceTicket->delete();
                
                \Illuminate\Support\Facades\DB::commit();
                
                $successMessage = "Le ticket #{$sourceTicket->id} a été fusionné avec succès.";
                if ($failedCount > 0) {
                    $successMessage .= " Attention : {$failedCount} pièce(s) jointe(s) n'ont pas pu être déplacées.";
                }
                
                return redirect()->route('ticket.show', $targetTicket->id)
                    ->with('success', $successMessage);
                    
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                throw $e;
            }
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.not_found'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la fusion : ' . $e->getMessage());
        }
    }
}
