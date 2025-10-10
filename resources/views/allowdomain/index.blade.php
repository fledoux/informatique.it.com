@extends('layouts.app')

@section('title')
    {{ __('allowdomain.List') }}
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
        <h1 class="h3 mb-0">
            {!! __('allowdomain.h1.List') !!}
        </h1>
        @hasanyrole(['super-admin'])
            <div class="d-flex gap-1 gap-sm-2">
                <a href="{{ route('allowdomain.create') }}" class="btn btn-orange">
                    {!! __('allowdomain.btn.New') !!}
                </a>
            </div>
        @endhasanyrole
    </div>

    <div class="table-responsive">
        <table class="table align-middle datatable table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ __('allowdomain.id') }}</th>
                    <th class="text-left">{{ __('allowdomain.fields.company_id') }}</th>
                    <th class="text-left">{{ __('allowdomain.fields.domain') }}</th>
                    <th>{{ __('global.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allowDomainRegistrations as $allowDomainRegistration)
                    <tr>
                        <td class="text-center">{{ $allowDomainRegistration->id }}</td>
                        <td>{{ $allowDomainRegistration->company?->name ?? '' }}</td>
                        <td>{{ $allowDomainRegistration->domain }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('allowdomain.show', $allowDomainRegistration) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.btn.Details') !!}
                            </a>
                            <a href="{{ route('allowdomain.edit', $allowDomainRegistration) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.btn.Edit') !!}
                            </a>
                            @include('allowdomain._delete_form', [
                                'allowDomainRegistration' => $allowDomainRegistration,
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            {!! __('global.No data') !!}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@if ($allowDomainRegistrations->count() > 0)
    @push('javascripts')
        @include('partials._datatable')
    @endpush
@endif
