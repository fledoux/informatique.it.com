@extends('layouts.app')

@section('title')
    @if (auth()->check() && auth()->user()->hasRole('manager'))
        {{ __('allow-domain-registration.List') }}
    @else
        {{ __('allow-domain-registration.YourList') }}
    @endif
@endsection

@section('content')
    <h1 class="mb-4">{{ __('allow-domain-registration.List') }}</h1>

    <div class="table-responsive">
        <table class="table align-middle table-xs table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ __('allow-domain-registration.id') }}</th>
                    <th class="text-left">{{ __('allow-domain-registration.fields.company_id') }}</th>
                    <th class="text-left">{{ __('allow-domain-registration.fields.domain') }}</th>
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
                            <a href="{{ route('allow-domain-registration.show', $allowDomainRegistration) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.Details') !!}
                            </a>
                            <a href="{{ route('allow-domain-registration.edit', $allowDomainRegistration) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.Edit') !!}
                            </a>
                            @include('allow-domain-registration._delete_form', [
                                'allowDomainRegistration' => $allowDomainRegistration,
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            {{ __('global.No data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('allow-domain-registration.create') }}" class="btn btn-orange mt-3">
        <i class="fa-regular fa-square-plus"></i>
        {{ __('global.New') }}
    </a>
@endsection
