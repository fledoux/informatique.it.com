<?php

namespace App\Http\Controllers;

use \App\Models\Company;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        $this->middleware('permission:company.index')->only('index');
        $this->middleware('permission:company.create')->only(['create', 'store']);
        $this->middleware('permission:company.show')->only('show');
        $this->middleware('permission:company.edit')->only(['edit', 'update']);
        $this->middleware('permission:company.delete')->only('destroy');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Super-admin voit toutes les sociétés
        if ($user->hasRole('super-admin')) {
            $companies = Company::all()->sortBy('name');
        } else {
            // Admin/Manager ne voient que leur propre société
            $companies = Company::where('id', $user->company_id)->get();
        }
        
        return view('company.index', compact('companies'));
    }

    public function create()
    {
        $company = new \App\Models\Company();
        return view('company.create', compact('company'));
    }

    public function store(CompanyStoreRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $company = Company::create($data);
        
        // Redirection conditionnelle selon le rôle
        $user = Auth::user();
        if ($user->hasRole('super-admin')) {
            return redirect()->route('company.index')->with('success', __('global.messages.created'));
        } else {
            return redirect()->route('company.show', $company)->with('success', __('global.messages.created'));
        }
    }

    public function show($id)
    {
        try {
            $company = Company::query()->findOrFail($id);
            
            // Vérifier l'autorisation avec la policy
            $this->authorize('view', $company);
            
            return view('company.show', compact('company'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('company.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $company = Company::query()->findOrFail($id);
            
            // Vérifier l'autorisation avec la policy
            $this->authorize('update', $company);
            
            return view('company.edit', compact('company'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('company.index')
                ->with('error', __('global.messages.edit_not_found'));
        }
    }

    public function update(CompanyUpdateRequest $request, $id)
    {
        try {
            $company = Company::findOrFail($id);
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            $company->update($data);
            
            // Redirection conditionnelle selon le rôle
            $user = Auth::user();
            if ($user->hasRole('super-admin')) {
                return redirect()->route('company.index')->with('success', __('global.messages.updated'));
            } else {
                return redirect()->route('company.show', $company)->with('success', __('global.messages.updated'));
            }
        } catch (ModelNotFoundException $e) {
            return redirect()->route('company.index')
                ->with('error', __('global.messages.update_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            
            // Empêcher l'auto-suppression
            if (strtolower('Company') === 'user' && Auth::check() && $company->id === Auth::id()) {
                return redirect()->route('company.index')
                    ->with('error', __('global.messages.cannot_delete_self'));
            }
            
            $company->delete();
            return redirect()->route('company.index')->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('company.index')
                ->with('error', __('global.messages.delete_not_found'));
        }
    }
}