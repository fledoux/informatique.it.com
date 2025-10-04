<div class="row g-3">
        <div class="col-12 col-lg-6">
            <x-forms.relation name="company_id" 
                              :label="__('allowdomain.fields.company_id')" 
                              model="Company" 
                              display-field="name"
                              :value="$allowDomainRegistration->company_id ?? null" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="domain" 
                           :label="__('allowdomain.fields.domain')" 
                           type="text"
                           :value="old('domain', $allowDomainRegistration->domain ?? null)"
                           placeholder=""  />
        </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{{ __('global.Save') }}</button>
<a href="{{ route('allowdomain.index') }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
</div>