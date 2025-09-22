<?php

namespace App\Http\Controllers;

use \App\Models\Ticket;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::query()->with(['company', 'author'])->latest('id')->paginate(15);
        return view('ticket.index', compact('tickets'));
    }

    public function create()
    {
        return view('ticket.create');
    }

    public function store(TicketStoreRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $ticket = Ticket::create($data);
        return redirect()->route('ticket.index')->with('success', __('crud.messages.created'));
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::query()->with(['company', 'author'])->findOrFail($id);
            return view('ticket.show', compact('ticket'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('crud.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $ticket = Ticket::query()->with(['company', 'author'])->findOrFail($id);
            return view('ticket.edit', compact('ticket'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('crud.messages.edit_not_found'));
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
            return redirect()->route('ticket.index')->with('success', __('crud.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('crud.messages.update_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            
            // Empêcher l'auto-suppression
            if (strtolower('Ticket') === 'user' && auth()->check() && $ticket->id === auth()->id()) {
                return redirect()->route('ticket.index')
                    ->with('error', __('crud.messages.cannot_delete_self'));
            }
            
            $ticket->delete();
            return redirect()->route('ticket.index')->with('success', __('crud.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('ticket.index')
                ->with('error', __('crud.messages.delete_not_found'));
        }
    }
}