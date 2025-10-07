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
        $this->middleware('permission:ticket.edit')->only(['edit', 'update']);
        $this->middleware('permission:ticket.delete')->only('destroy');
    }

    public function index()
    {
        $tickets = Ticket::getAllForUser();
        return view('ticket.index', compact('tickets'));
    }

    public function create()
    {
        return view('ticket.create');
    }

    public function store(TicketStoreRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        // Pour tous les utilisateurs sauf super-admin, on détermine le company_id en fonction de l'utilisateur sélectionné
        if (!$user->hasRole('super-admin')) {
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
            $ticket = Ticket::query()->with(['company', 'author'])->findOrFail($id);
            
            // Vérifications de sécurité
            if (!TicketSecurityHelper::canAccessTicket(Auth::user(), $ticket)) {
                return redirect()->route('ticket.index')
                    ->with('error', TicketSecurityHelper::getAccessDeniedMessage());
            }

            $user = \App\Models\User::find($ticket->author_id);
            $company = \App\Models\Company::find($ticket->company_id);

            return view('ticket.show', compact('ticket', 'user', 'company'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $ticket = Ticket::query()->with(['company', 'author'])->findOrFail($id);
            
            // Vérifications de sécurité par rôle
            $user = Auth::user();
            
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
}
