<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketMessageStoreRequest;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketMessageController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        // Permissions standard pour les opérations CRUD
        $this->middleware('permission:ticketmessage.index')->only('index');
        $this->middleware('permission:ticketmessage.create')->only(['create', 'store']);
        $this->middleware('permission:ticketmessage.show')->only('show');
        $this->middleware('permission:ticketmessage.edit')->only(['edit', 'update']);
        $this->middleware('permission:ticketmessage.delete')->only('destroy');
        
        // S'assurer que l'utilisateur est authentifié
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $ticket = null;
        $isInternal = $request->boolean('internal', false);
        
        // Vérifier que seuls les super-admin peuvent créer des notes internes
        if ($isInternal && !Auth::user()->hasRole('super-admin')) {
            return redirect()->back()
                ->with('error', 'Seuls les super-administrateurs peuvent créer des notes internes.');
        }
        
        // Si ticket_id est fourni, récupérer le ticket
        if ($request->has('ticket_id')) {
            $ticket = \App\Models\Ticket::findOrFail($request->get('ticket_id'));
        }
        
        return view('ticket_message.create', compact('ticket', 'isInternal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketMessageStoreRequest $request)
    {
        $validated = $request->validated();
        
        // Récupérer le ticket et vérifier les permissions
        $ticket = Ticket::findOrFail($validated['ticket_id']);
        
        // Vérifier que seuls les super-admin peuvent créer des notes internes
        if ($validated['status'] === 'internal' && !Auth::user()->hasRole('super-admin')) {
            return redirect()->back()
                ->with('error', 'Seuls les super-administrateurs peuvent créer des notes internes.')
                ->withInput();
        }
        
        // Ajouter l'auteur et la société du ticket
        $validated['author_id'] = Auth::id();
        $validated['company_id'] = $ticket->company_id;

        $ticketMessage = TicketMessage::create($validated);

        // Mettre à jour le statut du ticket si c'est une réponse publique
        if ($validated['status'] === 'active') {
            $ticket->update(['status' => 'in_progress']);
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
    public function edit(TicketMessage $ticketMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketMessage $ticketMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketMessage $ticketMessage)
    {
        //
    }
}
