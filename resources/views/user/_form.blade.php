@csrf
<div class="row g-3">
    <div class="col-12 col-lg-12">
        <div class="row">
            <div class="col-12 col-lg-4">
                <x-forms.select name="status" :label="__('user.fields.status')" :options="['active' => __('user.status.active'), 'inactive' => __('user.status.inactive')]" :required="true" :value="$user->status ?? 'active'" />
            </div>
            @hasanyrole(['super-admin'])
                <div class="col-12 col-lg-4">
                    <x-forms.select name="agree_terms" :label="__('user.fields.agree_terms')" :options="['oui' => __('user.agree_terms.oui'), 'non' => __('user.agree_terms.non')]" :value="$user->agree_terms ?? 'oui'" />
                </div>
            @endhasanyrole
            @hasanyrole(['super-admin'])
                <div class="col-12 col-lg-4">
                    <x-forms.relation name="company_id" :label="__('user.fields.company_id')" model="Company" display-field="name"
                        :value="$user->company_id ?? null" />
                </div>
            @endhasanyrole
        </div>
    </div>
    <div class="col-12 col-lg-12">
        <x-forms.input name="name" :label="__('user.fields.name')" :required="true" type="text" :value="old('name', $user->name ?? null)"
            placeholder="" />
    </div>
    <div class="col-12 col-lg-4">
        <x-forms.input name="firstname" :label="__('user.fields.firstname')" :required="true" type="text" :value="old('firstname', $user->firstname ?? null)"
            placeholder="" />
    </div>
    <div class="col-12 col-lg-4">
        <x-forms.input name="lastname" :label="__('user.fields.lastname')" :required="true" type="text" :value="old('lastname', $user->lastname ?? null)"
            placeholder="" />
    </div>
    @hasanyrole(['super-admin'])
        <div class="col-12 col-lg-4">
            <x-forms.input name="email" :label="__('user.fields.email')" :required="true" type="email" :value="old('email', $user->email ?? null)"
                placeholder="" />
        </div>
    @endhasanyrole
    <div class="col-12 col-lg-4">
        <x-forms.input name="password" :label="__('user.fields.password')" type="password" :required="true"
            placeholder="Laissez vide pour conserver le mot de passe actuel" />
    </div>
    
</div>

<div class="row g-3 mt-1">
    @hasanyrole(['super-admin'])
        <div class="col-4">
            <x-forms.roles-radio name="roles" :label="__('user.fields.roles')" :required="true" :options="collect(App\Models\User::getAvailableRoles())
                ->mapWithKeys(function ($role, $key) {
                    return [$key => __('user.roles.' . $key)];
                })
                ->toArray()"
                :values="old('roles', isset($user) ? $user->getRoleNames()->toArray() : [])" />
        </div>
    @endhasanyrole
    <div class="col-12 col-lg-4">
        <x-forms.checkbox-group name="channels" :label="__('user.fields.channels')" :options="['email' => __('user.fields.channels_email'), 'sms' => __('user.fields.channels_sms')]" :values="old(
            'channels',
            is_array($user->channels)
                ? $user->channels
                : (is_string($user->channels)
                    ? json_decode($user->channels, true)
                    : []),
        )" />
    </div>
    <div class="col-12 col-lg-4">
        <x-forms.input name="phone" :label="__('user.fields.phone')" type="tel" :value="old('phone', $user->phone ?? null)" placeholder="" />
    </div>
    @hasanyrole(['super-admin'])
        <div class="col-12 col-lg-12">
            <x-forms.textarea name="note" :label="__('user.fields.note')" :value="old('note', $user->note ?? null)" placeholder="" />
        </div>
    @endhasanyrole
</div>

<div class="btn-group mt-3" role="group" aria-label="Basic example">
    <button type="submit" class="btn btn-primary">{!! __('global.btn.Save') !!}</button>
    <a href="{{ route('user.index') }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
</div>
