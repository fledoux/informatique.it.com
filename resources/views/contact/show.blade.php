@extends('layouts.app')

@section('title', __('crud.Details') . ' — ' . __('contact.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('crud.Details') }} — {{ __('contact.entity') }}</h1>

    <dl class="row">
        <dt class="col-sm-3">{{ __('contact.id') }}</dt>
        <dd class="col-sm-9">{{ $contact->id }}</dd>
        <dt class="col-sm-3">{{ __('contact.fields.name') }}</dt>
        <dd class="col-sm-9">{{ $contact->name ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('contact.fields.email') }}</dt>
        <dd class="col-sm-9">{{ $contact->email ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('contact.fields.phone') }}</dt>
        <dd class="col-sm-9">{{ $contact->phone ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('contact.fields.type') }}</dt>
        <dd class="col-sm-9">
            @php($badgeColor = 'secondary')
            @switch($contact->type)
                @case('active') @php($badgeColor = 'success') @break
                @case('inactive') @php($badgeColor = 'secondary') @break
            @endswitch
            <span class="badge bg-{{ $badgeColor }}">{{ __('contact.enum.type.' . $contact->type) }}</span>
        </dd>
        <dt class="col-sm-3">{{ __('contact.fields.need') }}</dt>
        <dd class="col-sm-9">{{ $contact->need ?? '—' }}</dd>
    </dl>

    <div class="btn-group mt-3" role="group" aria-label="Actions">
        <a href="{{ route('contact.edit', $contact) }}" class="btn btn-primary">{{ __('crud.Edit') }}</a>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{{ __('crud.Back') }}</a>
    </div>
@endsection