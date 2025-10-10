@extends('layouts.app')

@section('title', __('global.Details') . ' ' . __('company.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.btn.Details') !!} de {{ $company->name ?? __('company.entity') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('company.id') }}</dt>
                <dd class="col-sm-9">{{ $company->id }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.status') }}</dt>
                <dd class="col-sm-9">
                    <span
                        class="badge {{ __('company.statusBadgeColor.' . $company->status) }}">{{ __('company.status.' . $company->status) }}</span>
                </dd>
                <dt class="col-sm-3">{{ __('company.fields.name') }}</dt>
                <dd class="col-sm-9">{{ $company->name ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.siret') }}</dt>
                <dd class="col-sm-9">{{ $company->siret ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.vat_number') }}</dt>
                <dd class="col-sm-9">{{ $company->vat_number ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.email') }}</dt>
                <dd class="col-sm-9">{{ $company->email ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.phone') }}</dt>
                <dd class="col-sm-9">{{ $company->phone ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.website') }}</dt>
                <dd class="col-sm-9">{{ $company->website ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.address_line1') }}</dt>
                <dd class="col-sm-9">{{ $company->address_line1 ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.address_line2') }}</dt>
                <dd class="col-sm-9">{{ $company->address_line2 ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.zip') }}</dt>
                <dd class="col-sm-9">{{ $company->zip ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.city') }}</dt>
                <dd class="col-sm-9">{{ $company->city ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.country') }}</dt>
                <dd class="col-sm-9">{{ $company->country ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('company.fields.notes') }}</dt>
                <dd class="col-sm-9">{{ $company->notes ?? '' }}</dd>
            </dl>

            <div class="btn-group mt-3" role="group" aria-label="Actions">
                <a href="{{ route('company.edit', $company) }}" class="btn btn-primary">{!! __('global.btn.Edit') !!}</a>
                @hasanyrole(['super-admin'])
                    <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
                @endhasanyrole
            </div>
        </div>
    </div>
@endsection
