@extends('layouts.app')

@section('title', __('global.Details') . '  ' . __('allowdomain.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.btn.Details') !!}  {{ __('allowdomain.entity') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('allowdomain.id') }}</dt>
                <dd class="col-sm-9">{{ $allowDomainRegistration->id }}</dd>
                <dt class="col-sm-3">{{ __('allowdomain.fields.company_id') }}</dt>
                <dd class="col-sm-9">{{ $allowDomainRegistration->company_id ? \App\Models\Company::find($allowDomainRegistration->company_id)?->name : '' }}</dd>
                <dt class="col-sm-3">{{ __('allowdomain.fields.domain') }}</dt>
                <dd class="col-sm-9">{{ $allowDomainRegistration->domain ?? '' }}</dd>
            </dl>

            <div class="btn-group mt-3" role="group" aria-label="Actions">
                <a href="{{ route('allowdomain.edit', $allowDomainRegistration) }}" class="btn btn-primary">{!! __('global.btn.Edit') !!}</a>
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
            </div>
        </div>
    </div>
@endsection