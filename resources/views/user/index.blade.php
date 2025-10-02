@extends('layouts.app-fluid')

@section('title')
    @if (auth()->check() && auth()->user()->hasRole('manager'))
        {{ __('user.List') }}
    @else
        {{ __('user.YourList') }}
    @endif
@endsection

@section('content')
    <h1 class="mb-4">{{ __('user.List') }}</h1>

    <div class="table-responsive">
        <table class="table align-middle table-xs table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ __('user.id') }}</th>
                    <th class="text-left">{{ __('user.fields.status') }}</th>
                    <th class="text-left">{{ __('user.fields.name') }}</th>
                    <th class="text-left">{{ __('user.fields.firstname') }}</th>
                    <th class="text-left">{{ __('user.fields.lastname') }}</th>
                    <th class="text-left">{{ __('user.fields.email') }}</th>
                    @can('company.show')
                        <th class="text-left">{{ __('user.fields.company_id') }}</th>
                    @endcan
                    <th class="text-left">{{ __('user.fields.roles') }}</th>
                    @can('user.edit')
                        <th class="text-left">{{ __('user.fields.initial') }}</th>
                    @endcan
                    <th class="text-left">{{ __('user.fields.phone') }}</th>
                    @can('user.edit')
                        <th class="text-center">{{ __('user.fields.Conditions') }}</th>
                    @endcan
                    <th class="text-center">{{ __('user.fields.channels') }}</th>
                    <th>{{ __('global.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="text-center">{{ $user->id }}</td>
                        <td>
                            <span
                                class="badge {{ __('user.statusBadgeColor.' . $user->status) }}">{{ __('user.status.' . $user->status) }}
                            </span>
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->firstname }}</td>
                        <td>{{ $user->lastname }}</td>
                        <td>{{ $user->email }}</td>
                        @can('company.show')
                            <td>{{ $user->company?->name ?? '' }}</td>
                        @endcan
                        <td>
                            @if ($user->getRoleNames()->isNotEmpty())
                                @foreach ($user->getRoleNames() as $role)
                                    <span
                                        class="badge {{ __('user.badgeRolesColor.' . $role) }} me-1">{{ __('user.roles.' . $role) }}</span>
                                @endforeach
                            @endif
                        </td>
                        @can('user.edit')
                            <td>{{ $user->initial }}</td>
                        @endcan
                        <td>{{ $user->phone }}</td>
                        @can('user.edit')
                            <td class="text-center">
                                {!! __('user.statusAgreeTermsColor.' . $user->agree_terms) !!}
                            </td>
                        @endcan
                        <td>
                            {{ collect(['email', 'sms'])->filter(fn($key) => $user->channels[$key] ?? false)->map(fn($key) => __('user.fields.channels_' . $key))->join(', ') ?:
                                '' }}
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('user.show', $user) }}" class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.Details') !!}
                            </a>
                            <a href="{{ route('user.edit', $user) }}" class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.Edit') !!}
                            </a>
                            <span class="me-2">
                                @include('user._delete_form', ['user' => $user])
                            </span>
                            @can('user.edit')
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('user.impersonate', $user) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-decoration-none p-0 me-2 float-end"
                                            onclick="return confirm('{{ __('nav.Login') }} en tant que {{ $user->name }} ?')">
                                            <i class="fa-regular fa-arrow-right-to-bracket"></i>
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="16" class="text-center">
                            {{ __('global.No data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('user.create') }}" class="btn btn-orange mt-3">
        <i class="fa-regular fa-square-plus"></i>
        {{ __('global.New') }}
    </a>
@endsection
