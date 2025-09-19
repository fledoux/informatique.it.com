@csrf
<div class="row g-3">
        <div class="col-12 col-lg-8">
            <label for="name" class="form-label">{{ __('contact.fields.name') }}</label>
            <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $contact->name ?? null) }}">
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="email" class="form-label">{{ __('contact.fields.email') }}</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $contact->email ?? null) }}">
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="phone" class="form-label">{{ __('contact.fields.phone') }}</label>
            <input id="phone" type="tel" name="phone" class="form-control" value="{{ old('phone', $contact->phone ?? null) }}">
            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="type" class="form-label">{{ __('contact.fields.type') }}</label>
            <input id="type" type="text" name="type" class="form-control" value="{{ old('type', $contact->type ?? null) }}">
            @error('type')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-lg-6">
            <label for="need" class="form-label">{{ __('contact.fields.need') }}</label>
            <input id="need" type="text" name="need" class="form-control" value="{{ old('need', $contact->need ?? null) }}">
            @error('need')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{{ __('crud.Save') }}</button>
<a href="{{ route('contact.index') }}" class="btn btn-outline-primary">{{ __('crud.Back') }}</a>
</div>