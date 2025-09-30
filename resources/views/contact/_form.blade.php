@csrf
<div class="row g-3">
    <div class="col-12 col-lg-8">
        <x-forms.input name="name" :label="__('contact.fields.name')" :required="true" type="text" :value="old('name', $contact->name ?? null)" placeholder="" />
    </div>
    <div class="col-12 col-lg-6">
        <x-forms.input name="email" :label="__('contact.fields.email')" :required="true" type="email" :value="old('email', $contact->email ?? null)" placeholder="" />
    </div>
    <div class="col-12 col-lg-6">
        <x-forms.input name="phone" :label="__('contact.fields.phone')" :required="true" type="tel" :value="old('phone', $contact->phone ?? null)" placeholder="" />
    </div>
    <div class="col-12 col-lg-4">
        <x-forms.select name="type" :label="__('contact.fields.type')" :required="true" placeholder="-- Choisissez --"
            :options="__('contact.type')" />
    </div>
    <div class="col-12">
        <x-forms.input name="need" :label="__('contact.fields.need')" :required="true" type="textarea" :rows="4" :value="old('need', $contact->need ?? null)" />
    </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
    <button type="submit" class="btn btn-primary">{{ __('global.Save') }}</button>
    <a href="{{ route('contact.index') }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
</div>
