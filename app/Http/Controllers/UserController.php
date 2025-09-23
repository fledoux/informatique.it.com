<?php

namespace App\Http\Controllers;

use \App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->with(['company'])->latest('id')->paginate(15);
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        
        // Extraire les rôles des données
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        $user = User::create($data);
        
        // Assigner les rôles si présents
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }
        
        return redirect()->route('user.index')->with('success', __('global.messages.created'));
    }

    public function show($id)
    {
        try {
            $user = User::query()->with(['company'])->findOrFail($id);
            return view('user.show', compact('user'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $user = User::query()->with(['company'])->findOrFail($id);
            return view('user.edit', compact('user'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.edit_not_found'));
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            
            // Extraire les rôles des données
            $roles = $data['roles'] ?? [];
            unset($data['roles']);
            
            $user->update($data);
            
            // Synchroniser les rôles
            $user->syncRoles($roles);
            
            return redirect()->route('user.index')->with('success', __('global.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.update_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Empêcher l'auto-suppression
            if (strtolower('User') === 'user' && Auth::check() && $user->id === Auth::id()) {
                return redirect()->route('user.index')
                    ->with('error', __('global.messages.cannot_delete_self'));
            }
            
            $user->delete();
            return redirect()->route('user.index')->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.delete_not_found'));
        }
    }
}