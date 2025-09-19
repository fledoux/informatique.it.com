@csrf
<div class="row g-3">
        <div class="col-12 col-lg-8">
            <label for="name" class="form-label">{{ __('user.fields.name') }}</label>
            <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? null) }}">
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="email" class="form-label">{{ __('user.fields.email') }}</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? null) }}">
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="password" class="form-label">{{ __('user.fields.password') }}</label>
            <input id="password" type="password" name="password" class="form-control" value="">
            <small class="form-text text-muted">Laissez vide pour conserver le mot de passe actuel</small>
            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-4">
            <label for="status" class="form-label">{{ __('user.fields.status') }}</label>
            <select id="status" name="status" class="form-select">
                <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $user->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="company_id" class="form-label">{{ __('user.fields.company_id') }}</label>
            <select id="company_id" name="company_id" class="form-select">
                <option value="">-- Sélectionner --</option>
                @foreach(\App\Models\Company::orderBy('name')->get() as $option)
                    <option value="{{ $option->id }}" {{ old('company_id', $user->company_id) == $option->id ? 'selected' : '' }}>
                        {{ $option->name }}
                    </option>
                @endforeach
            </select>
            @error('company_id')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="firstname" class="form-label">{{ __('user.fields.firstname') }}</label>
            <input id="firstname" type="text" name="firstname" class="form-control" value="{{ old('firstname', $user->firstname ?? null) }}">
            @error('firstname')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="lastname" class="form-label">{{ __('user.fields.lastname') }}</label>
            <input id="lastname" type="text" name="lastname" class="form-control" value="{{ old('lastname', $user->lastname ?? null) }}">
            @error('lastname')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="phone" class="form-label">{{ __('user.fields.phone') }}</label>
            <input id="phone" type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? null) }}">
            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="last_login" class="form-label">{{ __('user.fields.last_login') }}</label>
            <input id="last_login" type="datetime-local" name="last_login" class="form-control" 
                   value="{{ old('last_login', $user->last_login ? ($user->last_login instanceof \Carbon\Carbon ? $user->last_login->format('Y-m-d\TH:i') : $user->last_login) : '') }}">
            @error('last_login')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-4">
            <label for="agree_terms" class="form-label">{{ __('user.fields.agree_terms') }}</label>
            <select id="agree_terms" name="agree_terms" class="form-select">
                <option value="oui" {{ old('agree_terms', $user->agree_terms ?? 'oui') === 'oui' ? 'selected' : '' }}>Oui</option>
                <option value="non" {{ old('agree_terms', $user->agree_terms ?? 'oui') === 'non' ? 'selected' : '' }}>Non</option>
            </select>
            @error('agree_terms')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label class="form-label">{{ __('user.fields.channels') }}</label>
            <div class="border rounded p-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" 
                       id="channels_email" 
                       name="channels[email]" 
                       value="1"
                       @php($oldData = old('channels', is_array($user->channels) ? $user->channels : (is_string($user->channels) ? json_decode($user->channels, true) : [])))
                       {{ isset($oldData['email']) && $oldData['email'] ? 'checked' : '' }}>
                <label class="form-check-label" for="channels_email">
                    {{ __('user.fields.channels_email') }}
                </label>
            </div>            <div class="form-check">
                <input class="form-check-input" type="checkbox" 
                       id="channels_sms" 
                       name="channels[sms]" 
                       value="1"
                       @php($oldData = old('channels', is_array($user->channels) ? $user->channels : (is_string($user->channels) ? json_decode($user->channels, true) : [])))
                       {{ isset($oldData['sms']) && $oldData['sms'] ? 'checked' : '' }}>
                <label class="form-check-label" for="channels_sms">
                    {{ __('user.fields.channels_sms') }}
                </label>
            </div>
            </div>
            @error('channels')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label for="note" class="form-label">{{ __('user.fields.note') }}</label>
            <textarea id="note" name="note" class="form-control" rows="4">{{ old('note', $user->note ?? null) }}</textarea>
            @error('note')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{{ __('crud.Save') }}</button>
<a href="{{ route('user.index') }}" class="btn btn-outline-primary">{{ __('crud.Back') }}</a>
</div>