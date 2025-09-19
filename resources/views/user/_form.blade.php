@csrf
<div class="row g-3">
        <div class="col-12 col-lg-8">
            <x-forms.input name="name" 
                           :label="__('user.fields.name')" 
                           type="text"
                           :value="old('name', $user->name ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="email" 
                           :label="__('user.fields.email')" 
                           type="email"
                           :value="old('email', $user->email ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="password" 
                           :label="__('user.fields.password')" 
                           type="password" 
                           placeholder="Laissez vide pour conserver le mot de passe actuel" />
        </div>
        <div class="col-12 col-lg-4">
            <x-forms.select name="status" 
                            :label="__('user.fields.status')" 
                            :options="['active' => __('user.enums.status.active'), 'inactive' => __('user.enums.status.inactive')]"
                            :value="$user->status ?? 'active'" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.relation name="company_id" 
                              :label="__('user.fields.company_id')" 
                              model="Company" 
                              display-field="name"
                              :value="$user->company_id ?? null" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="firstname" 
                           :label="__('user.fields.firstname')" 
                           type="text"
                           :value="old('firstname', $user->firstname ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="lastname" 
                           :label="__('user.fields.lastname')" 
                           type="text"
                           :value="old('lastname', $user->lastname ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="phone" 
                           :label="__('user.fields.phone')" 
                           type="tel"
                           :value="old('phone', $user->phone ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="last_login" 
                           :label="__('user.fields.last_login')" 
                           type="datetime-local"
                           :value="old('last_login', $user->last_login ? ($user->last_login instanceof \Carbon\Carbon ? $user->last_login->format('Y-m-d\\TH:i') : $user->last_login) : '')" />
        </div>
        <div class="col-12 col-lg-4">
            <x-forms.select name="agree_terms" 
                            :label="__('user.fields.agree_terms')" 
                            :options="['oui' => __('user.enums.agree_terms.oui'), 'non' => __('user.enums.agree_terms.non')]"
                            :value="$user->agree_terms ?? 'oui'" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.checkbox-group name="channels" 
                                    :label="__('user.fields.channels')" 
                                    :options="['email' => __('user.fields.channels_email'), 'sms' => __('user.fields.channels_sms')]"
                                    :values="old('channels', is_array($user->channels) ? $user->channels : (is_string($user->channels) ? json_decode($user->channels, true) : []))" />
        </div>
        <div class="col-12">
            <x-forms.input name="note" 
                           :label="__('user.fields.note')" 
                           type="textarea" 
                           :rows="4"
                           :value="old('note', $user->note ?? null)" />
        </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{{ __('crud.Save') }}</button>
<a href="{{ route('user.index') }}" class="btn btn-outline-primary">{{ __('crud.Back') }}</a>
</div>