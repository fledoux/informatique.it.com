@extends('layouts.app')

@section('title')
    @if (auth()->check() && auth()->user()->hasRole('manager'))
        {{ __('contact.List') }}
    @else
        {{ __('contact.YourList') }}
    @endif
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
        <h1 class="h3 mb-0">
            {!! __('contact.h1.List') !!}
        </h1>
        @can('create', App\Models\Contact::class)
            <div class="d-flex gap-1 gap-sm-2">
                <a href="{{ route('contact.create') }}" class="btn btn-orange">
                    {!! __('contact.btn.New') !!}
                </a>
            </div>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table align-middle datatable table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ __('contact.id') }}</th>
                    <th class="text-left">{{ __('contact.fields.name') }}</th>
                    <th class="text-left">{{ __('contact.fields.email') }}</th>
                    <th class="text-left">{{ __('contact.fields.phone') }}</th>
                    <th class="text-left">{{ __('contact.fields.type') }}</th>
                    <th class="text-left">{{ __('contact.fields.need') }}</th>
                    <th>{{ __('global.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                    <tr>
                        <td class="text-center">{{ $contact->id }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->phone }}</td>
                        <td>
                            <span class="badge {{ __('contact.statusBadgeColor.' . $contact->type) }}">{{ __('contact.type.' . $contact->type) }}</span>
                        </td>
                        <td>{{ $contact->need }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('contact.show', $contact) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.btn.Details') !!}
                            </a>
                            <a href="{{ route('contact.edit', $contact) }}"
                                class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.btn.Edit') !!}
                            </a>
                            @include('contact._delete_form', ['contact' => $contact])
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                {!! __('global.No data') !!}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
@endsection

    @if ($contacts->count() > 0)
        @push('javascripts')
            @include('partials._datatable')
        @endpush
    @endif
