<?php

namespace App\Http\Controllers;

use \App\Models\Company;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::query()->latest('id')->paginate(15);
        return view('company.index', compact('companies'));
    }

    public function create()
    {
        return view('company.create');
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
        return redirect()->route('company.index')->with('success', __('crud.messages.created'));
    }

    public function show($id)
    {
        try {
            $company = Company::findOrFail($id);
            return view('company.show', compact('company'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('company.index')
                ->with('error', __('crud.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $company = Company::findOrFail($id);
            return view('company.edit', compact('company'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('company.index')
                ->with('error', __('crud.messages.edit_not_found'));
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
            return redirect()->route('company.index')->with('success', __('crud.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('company.index')
                ->with('error', __('crud.messages.update_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();
            return redirect()->route('company.index')->with('success', __('crud.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('company.index')
                ->with('error', __('crud.messages.delete_not_found'));
        }
    }
}