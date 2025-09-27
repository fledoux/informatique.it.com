<?php

namespace App\Http\Controllers;

use \App\Models\Ticket;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
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
        
        // Si l'utilisateur est manager ou user, on force certains champs
        if ($user->hasRole(['manager', 'user'])) {
            // Valeurs par défaut pour manager/user
            $data['status'] = 'new';
            $data['company_id'] = $user->company_id;
            $data['author_id'] = $user->id;
            $data['assigned_to'] = null;
            $data['assigned_at'] = null;
            $data['due'] = null;
            $data['billable'] = true;
        }
        
        $ticket = Ticket::create($data);
        Ticket::sendNotification($ticket->id, 'Création de Ticket', 'Message de notification');
        return redirect()->route('ticket.show', $ticket->id)->with('success', __('global.messages.created'));
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::query()->with(['company', 'author'])->findOrFail($id);
            return view('ticket.show', compact('ticket'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $ticket = Ticket::query()->with(['company', 'author'])->findOrFail($id);
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
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            $ticket->update($data);
            return redirect()->route('ticket.show', $ticket->id)->with('success', __('global.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('global.messages.update_not_found'));
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