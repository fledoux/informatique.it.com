<?php

namespace App\Http\Controllers;

use \App\Models\AllowDomainRegistration;
use App\Http\Requests\AllowDomainRegistrationStoreRequest;
use App\Http\Requests\AllowDomainRegistrationUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class AllowDomainRegistrationController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        $this->middleware('permission:allowdomain.index')->only(['index']);
        $this->middleware('permission:allowdomain.create')->only(['create', 'store']);
        $this->middleware('permission:allowdomain.show')->only(['show']);
        $this->middleware('permission:allowdomain.edit')->only(['edit', 'update']);
        $this->middleware('permission:allowdomain.delete')->only(['destroy']);
    }

    public function index()
    {
        $allowDomainRegistrations = AllowDomainRegistration::query()->with(['company'])->latest('id')->paginate(15);
        return view('allowdomain.index', compact('allowDomainRegistrations'));
    }

    public function create()
    {
        return view('allowdomain.create');
    }

    public function store(AllowDomainRegistrationStoreRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $allowDomainRegistration = AllowDomainRegistration::create($data);
        return redirect()->route('allowdomain.index')->with('success', __('global.messages.created'));
    }

    public function show($id)
    {
        try {
            $allowDomainRegistration = AllowDomainRegistration::query()->with(['company'])->findOrFail($id);
            return view('allowdomain.show', compact('allowDomainRegistration'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('allowdomain.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $allowDomainRegistration = AllowDomainRegistration::query()->with(['company'])->findOrFail($id);
            return view('allowdomain.edit', compact('allowDomainRegistration'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('allowdomain.index')
                ->with('error', __('global.messages.edit_not_found'));
        }
    }

    public function update(AllowDomainRegistrationUpdateRequest $request, $id)
    {
        try {
            $allowDomainRegistration = AllowDomainRegistration::findOrFail($id);
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            $allowDomainRegistration->update($data);
            return redirect()->route('allowdomain.index')->with('success', __('global.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('allowdomain.index')
                ->with('error', __('global.messages.update_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            $allowDomainRegistration = AllowDomainRegistration::findOrFail($id);
            
            // Empêcher l'auto-suppression
            if (strtolower('AllowDomainRegistration') === 'user' && Auth::check() && $allowDomainRegistration->id === Auth::id()) {
                return redirect()->route('allowdomain.index')
                    ->with('error', __('global.messages.cannot_delete_self'));
            }
            
            $allowDomainRegistration->delete();
            return redirect()->route('allowdomain.index')->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('allowdomain.index')
                ->with('error', __('global.messages.delete_not_found'));
        }
    }
}