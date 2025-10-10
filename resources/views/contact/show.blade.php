@extends('layouts.app')

@section('title', __('global.Details') . '  ' . __('contact.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.btn.Details') !!}  {{ __('contact.entity') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('contact.id') }}</dt>
                <dd class="col-sm-9">{{ $contact->id }}</dd>
                <dt class="col-sm-3">{{ __('contact.fields.name') }}</dt>
                <dd class="col-sm-9">{{ $contact->name ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('contact.fields.email') }}</dt>
                <dd class="col-sm-9">{{ $contact->email ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('contact.fields.phone') }}</dt>
                <dd class="col-sm-9">{{ $contact->phone ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('contact.fields.type') }}</dt>
                <dd class="col-sm-9">
                    <span class="badge {{ __('contact.statusBadgeColor.' . $contact->type) }}">{{ __('contact.type.' . $contact->type) }}</span>
                </dd>
                <dt class="col-sm-3">{{ __('contact.fields.need') }}</dt>
                <dd class="col-sm-9">{{ $contact->need ?? '' }}</dd>
            </dl>

            <div class="btn-group mt-3" role="group" aria-label="Actions">
                <a href="{{ route('contact.edit', $contact) }}" class="btn btn-primary">{!! __('global.btn.Edit') !!}</a>
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
            </div>
        </div>
    </div>
@endsection