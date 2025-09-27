@extends('layouts.app')

@section('title')
    @if (auth()->check() && auth()->user()->hasRole('manager'))
        {{ __('user.List') }}
    @else
        {{ __('user.YourList') }}
    @endif
@endsection

@section('content')
    <h1 class="mb-4">{{ __('user.List') }}</h1>

    <div class="table-responsive-lg">
        <table class="table align-middle table-xs table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ __('user.id') }}</th>
                    <th class="text-left">{{ __('user.fields.name') }}</th>
                    <th class="text-left">{{ __('user.fields.email') }}</th>
                    <th class="text-left">{{ __('user.fields.status') }}</th>
                    @role('super-admin')
                        <th class="text-left">{{ __('user.fields.company_id') }}</th>
                    @endrole
                    <th class="text-left">{{ __('user.fields.roles') }}</th>
                    <th class="text-left">{{ __('user.fields.firstname') }}</th>
                    <th class="text-left">{{ __('user.fields.lastname') }}</th>
                    @role('super-admin')
                        <th class="text-left">{{ __('user.fields.initial') }}</th>
                    @endrole
                    <th class="text-left">{{ __('user.fields.phone') }}</th>
                    @role('super-admin')
                        <th class="text-left">{{ __('user.fields.last_login') }}</th>
                    @endrole
                    @role('super-admin')
                        <th class="text-left">{{ __('user.fields.agree_terms') }}</th>
                    @endrole
                    <th class="text-left">{{ __('user.fields.channels') }}</th>
                    @role('super-admin')
                        <th class="text-left">{{ __('user.fields.note') }}</th>
                    @endrole
                    <th>{{ __('global.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="text-center">{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span
                                class="badge {{ __('user.statusBadgeColor.' . $user->status) }}">{{ __('user.status.' . $user->status) }}</span>
                        </td>
                        @role('super-admin')
                            <td>{{ $user->company?->name ?? '' }}</td>
                        @endrole
                        <td>
                            @if ($user->getRoleNames()->isNotEmpty())
                                @foreach ($user->getRoleNames() as $role)
                                    <span
                                        class="badge {{ __('user.badgeRolesColor.' . $role) }} me-1">{{ __('user.roles.' . $role) }}</span>
                                @endforeach
                            @else
                                <span class="text-secondary"></span>
                            @endif
                        </td>
                        <td>{{ $user->firstname }}</td>
                        <td>{{ $user->lastname }}</td>
                        @role('super-admin')
                            <td>{{ $user->initial }}</td>
                        @endrole
                        <td>{{ $user->phone }}</td>
                        @role('super-admin')
                            <td>{{ $user->last_login ? ($user->last_login instanceof \Carbon\Carbon ? $user->last_login->format('d/m/Y H:i') : $user->last_login) : '' }}
                            </td>
                        @endrole
                        @role('super-admin')
                            <td>
                                @php($badgeColor = 'secondary')
                                @switch($user->agree_terms)
                                    @case('oui')
                                        @php($badgeColor = 'primary')
                                    @break

                                    @case('non')
                                        @php($badgeColor = 'primary')
                                    @break
                                @endswitch
                                <span
                                    class="badge bg-{{ $badgeColor }}">{{ __('user.agree_terms.' . $user->agree_terms) }}</span>
                            </td>
                        @endrole
                        <td>
                            {{ collect(['email', 'sms'])->filter(fn($key) => $user->channels[$key] ?? false)->map(fn($key) => __('user.fields.channels_' . $key))->join(', ') ?:
                                '' }}
                        </td>
                        @role('super-admin')
                            <td>{{ $user->note }}</td>
                        @endrole
                        <td class="text-nowrap">
                            <a href="{{ route('user.show', $user) }}" class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.Details') !!}
                            </a>
                            <a href="{{ route('user.edit', $user) }}" class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.Edit') !!}
                            </a>
                            @role('super-admin')
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('user.impersonate', $user) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-decoration-none p-0 me-2 text-warning"
                                                onclick="return confirm('{{ __('nav.Login') }} en tant que {{ $user->name }} ?')">
                                            <i class="fa-regular fa-user-gear"></i> {{ __('nav.Login') }}
                                        </button>
                                    </form>
                                @endif
                            @endrole
                            @include('user._delete_form', ['user' => $user])
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
