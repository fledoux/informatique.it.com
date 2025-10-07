@csrf
<div class="row g-3">
        <div class="col-12 col-lg-3">
            <x-forms.select name="status" 
                            :label="__('company.fields.status')" 
                            :options="['active' => __('company.enum.status.active'), 'inactive' => __('company.enum.status.inactive')]"
                            :value="$company->status ?? 'active'" />
        </div>
        <div class="col-12 col-lg-9">
            <x-forms.input name="name" 
                           :label="__('company.fields.name')" 
                           type="text"
                           :required=true
                           :value="old('name', $company->name ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-3">
            <x-forms.input name="siret" 
                           :label="__('company.fields.siret')" 
                           type="text"
                           :value="old('siret', $company->siret ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-3">
            <x-forms.input name="vat_number" 
                           :label="__('company.fields.vat_number')" 
                           type="text"
                           :value="old('vat_number', $company->vat_number ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-3">
            <x-forms.input name="email" 
                           :label="__('company.fields.email')" 
                           type="email"
                           :value="old('email', $company->email ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-3">
            <x-forms.input name="phone" 
                           :label="__('company.fields.phone')" 
                           type="tel"
                           :value="old('phone', $company->phone ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-12">
            <x-forms.input name="website" 
                           :label="__('company.fields.website')" 
                           type="url"
                           :value="old('website', $company->website ?? null)"
                           placeholder=""  />
        </div>
</div>
<div class="row g-3">
        <div class="col-12 col-lg-6">
            <x-forms.input name="address_line1" 
                           :label="__('company.fields.address_line1')" 
                           type="text"
                           :required=true
                           :value="old('address_line1', $company->address_line1 ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="address_line2" 
                           :label="__('company.fields.address_line2')" 
                           type="text"
                           :value="old('address_line2', $company->address_line2 ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-3">
            <x-forms.input name="zip" 
                           :label="__('company.fields.zip')" 
                           type="text"
                           :required=true
                           :value="old('zip', $company->zip ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-7">
            <x-forms.input name="city" 
                           :label="__('company.fields.city')" 
                           type="text"
                           :required=true
                           :value="old('city', $company->city ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-2">
            <x-forms.input name="country" 
                           :label="__('company.fields.country')" 
                           type="text"
                           :required=true
                           :value="old('country', $company->country ?? null)"
                           placeholder="FR" maxlength="2" />
        </div>
        <div class="col-12">
            <x-forms.input name="notes" 
                           :label="__('company.fields.notes')" 
                           type="textarea" 
                           :rows="4"
                           :value="old('notes', $company->notes ?? null)" />
        </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{!! __('global.btn.Save') !!}</button>
<a href="{{ route('company.index') }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
</div>