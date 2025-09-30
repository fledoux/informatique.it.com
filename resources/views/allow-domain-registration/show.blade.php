@extends('layouts.app')

@section('title', __('global.Details') . '  ' . __('allow-domain-registration.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Details') !!}  {{ __('allow-domain-registration.entity') }}</h1>

    <dl class="row">
        <dt class="col-sm-3">{{ __('allow-domain-registration.id') }}</dt>
        <dd class="col-sm-9">{{ $allowDomainRegistration->id }}</dd>
        <dt class="col-sm-3">{{ __('allow-domain-registration.fields.company_id') }}</dt>
        <dd class="col-sm-9">{{ $allowDomainRegistration->company_id ? \App\Models\Company::find($allowDomainRegistration->company_id)?->name : '' }}</dd>
        <dt class="col-sm-3">{{ __('allow-domain-registration.fields.domain') }}</dt>
        <dd class="col-sm-9">{{ $allowDomainRegistration->domain ?? '' }}</dd>
    </dl>

    <div class="btn-group mt-3" role="group" aria-label="Actions">
        <a href="{{ route('allow-domain-registration.edit', $allowDomainRegistration) }}" class="btn btn-primary">{!! __('global.Edit') !!}</a>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
    </div>
@endsection