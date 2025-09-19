@csrf
<div class="row g-3">
        <div class="col-12 col-lg-4">
            <label for="status" class="form-label">{{ __('company.fields.status') }}</label>
            <select id="status" name="status" class="form-select">
                <option value="active" {{ old('status', $company->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $company->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-8">
            <label for="name" class="form-label">{{ __('company.fields.name') }}</label>
            <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $company->name ?? null) }}">
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="siret" class="form-label">{{ __('company.fields.siret') }}</label>
            <input id="siret" type="text" name="siret" class="form-control" value="{{ old('siret', $company->siret ?? null) }}">
            @error('siret')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="vat_number" class="form-label">{{ __('company.fields.vat_number') }}</label>
            <input id="vat_number" type="text" name="vat_number" class="form-control" value="{{ old('vat_number', $company->vat_number ?? null) }}">
            @error('vat_number')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="email" class="form-label">{{ __('company.fields.email') }}</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $company->email ?? null) }}">
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="phone" class="form-label">{{ __('company.fields.phone') }}</label>
            <input id="phone" type="tel" name="phone" class="form-control" value="{{ old('phone', $company->phone ?? null) }}">
            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="website" class="form-label">{{ __('company.fields.website') }}</label>
            <input id="website" type="url" name="website" class="form-control" value="{{ old('website', $company->website ?? null) }}">
            @error('website')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="address_line1" class="form-label">{{ __('company.fields.address_line1') }}</label>
            <input id="address_line1" type="text" name="address_line1" class="form-control" value="{{ old('address_line1', $company->address_line1 ?? null) }}">
            @error('address_line1')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="address_line2" class="form-label">{{ __('company.fields.address_line2') }}</label>
            <input id="address_line2" type="text" name="address_line2" class="form-control" value="{{ old('address_line2', $company->address_line2 ?? null) }}">
            @error('address_line2')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-4">
            <label for="zip" class="form-label">{{ __('company.fields.zip') }}</label>
            <input id="zip" type="text" name="zip" class="form-control" value="{{ old('zip', $company->zip ?? null) }}">
            @error('zip')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-4">
            <label for="city" class="form-label">{{ __('company.fields.city') }}</label>
            <input id="city" type="text" name="city" class="form-control" value="{{ old('city', $company->city ?? null) }}">
            @error('city')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-4">
            <label for="country" class="form-label">{{ __('company.fields.country') }}</label>
            <input id="country" type="text" name="country" class="form-control" value="{{ old('country', $company->country ?? null) }}" maxlength="2" placeholder="FR">
            @error('country')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label for="notes" class="form-label">{{ __('company.fields.notes') }}</label>
            <textarea id="notes" name="notes" class="form-control" rows="4">{{ old('notes', $company->notes ?? null) }}</textarea>
            @error('notes')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{{ __('crud.Save') }}</button>
<a href="{{ route('company.index') }}" class="btn btn-outline-primary">{{ __('crud.Back') }}</a>
</div>