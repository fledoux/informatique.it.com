@extends('layouts.app')

@section('title')
    @hasanyrole(['super-admin'])
        {{ __('company.YourList') }}
    @else
        {{ __('company.List') }}
    @endhasanyrole
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
        <h1 class="h3 mb-0">
            <i class="fa-regular fa-building me-2"></i>
            @hasanyrole(['super-admin'])
                {{ __('company.YourList') }}
            @else
                {{ __('company.List') }}
            @endhasanyrole
        </h1>
        @can('create', App\Models\Company::class)
            <div class="d-flex gap-1 gap-sm-2">
                <a href="{{ route('company.create') }}" class="btn btn-orange">
                    {!! __('global.btn.New') !!}
                </a>
            </div>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table align-middle datatable table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ __('company.id') }}</th>
                    <th class="text-left">{{ __('company.fields.status') }}</th>
                    <th class="text-left">{{ __('company.fields.name') }}</th>
                    <th class="text-left">{{ __('company.fields.siret') }}</th>
                    <th class="text-left">{{ __('company.fields.vat_number') }}</th>
                    <th class="text-left">{{ __('company.fields.email') }}</th>
                    <th class="text-left">{{ __('company.fields.phone') }}</th>
                    <th class="text-left">{{ __('company.fields.website') }}</th>
                    <th class="text-left">{{ __('company.fields.address_line1') }}</th>
                    <th class="text-left">{{ __('company.fields.address_line2') }}</th>
                    <th class="text-left">{{ __('company.fields.zip') }}</th>
                    <th class="text-left">{{ __('company.fields.city') }}</th>
                    <th class="text-left">{{ __('company.fields.country') }}</th>
                    <th>{{ __('global.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td class="text-center">{{ $company->id }}</td>
                        <td>
                            <span
                                class="badge {{ __('company.statusBadgeColor.' . $company->status) }}">{{ __('company.status.' . $company->status) }}</span>
                        </td>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->siret }}</td>
                        <td>{{ $company->vat_number }}</td>
                        <td>{{ $company->email }}</td>
                        <td>{{ $company->phone }}</td>
                        <td>{{ $company->website }}</td>
                        <td>{{ $company->address_line1 }}</td>
                        <td>{{ $company->address_line2 }}</td>
                        <td>{{ $company->zip }}</td>
                        <td>{{ $company->city }}</td>
                        <td>{{ $company->country }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('company.show', $company) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.btn.Details') !!}
                            </a>
                            <a href="{{ route('company.edit', $company) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.btn.Edit') !!}
                            </a>
                            @include('company._delete_form', ['company' => $company])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" class="text-center">
                            {!! __('global.No data') !!}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@if ($companies->count() > 0)
    @push('javascripts')
        @include('partials._datatable')
    @endpush
@endif
