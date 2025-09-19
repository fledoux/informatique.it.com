<?php

namespace App\Http\Controllers;

use \App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->latest('id')->paginate(15);
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
        $user = User::create($data);
        return redirect()->route('user.index')->with('success', __('crud.messages.created'));
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('user.show', compact('user'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('crud.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('user.edit', compact('user'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('crud.messages.edit_not_found'));
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
            $user->update($data);
            return redirect()->route('user.index')->with('success', __('crud.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('crud.messages.update_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Empêcher l'auto-suppression
            if (strtolower('User') === 'user' && auth()->check() && $user->id === auth()->id()) {
                return redirect()->route('user.index')
                    ->with('error', __('crud.messages.cannot_delete_self'));
            }
            
            $user->delete();
            return redirect()->route('user.index')->with('success', __('crud.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('crud.messages.delete_not_found'));
        }
    }
}