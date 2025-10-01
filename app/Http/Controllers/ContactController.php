<?php

namespace App\Http\Controllers;

use \App\Models\Contact;
use App\Http\Requests\ContactStoreRequest;
use App\Http\Requests\ContactUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

class ContactController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        $this->middleware('permission:contact.index')->only('index');
        $this->middleware('permission:contact.show')->only('show');
        $this->middleware('permission:contact.edit')->only(['edit', 'update']);
        $this->middleware('permission:contact.delete')->only('destroy');
        // Note: contact.create est commenté dans les routes, donc pas de middleware ici
    }

    public function index()
    {
        $contacts = Contact::query()->latest('id')->paginate(15);
        return view('contact.index', compact('contacts'));
    }

    public function create()
    {
        return view('contact.create');
    }

    public function store(ContactStoreRequest $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', 'in:particulier,entreprise,association,autre'],
            'need' => ['required', 'string', 'max:5000'],
        ]);

        // Create the contact record
        Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'type' => $validated['type'],
            'need' => $validated['need'],
        ]);

        $title = 'Nouveau Contact';
        $message = $validated['name'] . "\n" . $validated['email'] . "\n" . $validated['phone'] . "\n" . $validated['type'] . "\n" . $validated['need'];
        Helper::sendPushoverNotification($title, $message);

        return redirect()->route('home')->with('success', __('global.messages.created'));
    }

    public function show($id)
    {
        try {
            $contact = Contact::query()->findOrFail($id);
            return view('contact.show', compact('contact'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('contact.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $contact = Contact::query()->findOrFail($id);
            return view('contact.edit', compact('contact'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('contact.index')
                ->with('error', __('global.messages.edit_not_found'));
        }
    }

    public function update(ContactUpdateRequest $request, $id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            $contact->update($data);
            return redirect()->route('contact.index')->with('success', __('global.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('contact.index')
                ->with('error', __('global.messages.update_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            
            // Empêcher l'auto-suppression
            if (strtolower('Contact') === 'user' && Auth::check() && $contact->id === Auth::id()) {
                return redirect()->route('contact.index')
                    ->with('error', __('global.messages.cannot_delete_self'));
            }
            
            $contact->delete();
            return redirect()->route('contact.index')->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('contact.index')
                ->with('error', __('global.messages.delete_not_found'));
        }
    }
}