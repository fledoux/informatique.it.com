<?php

namespace App\Http\Controllers;

use \App\Models\Contact;
use App\Http\Requests\ContactStoreRequest;
use App\Http\Requests\ContactUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
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
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $contact = Contact::create($data);
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