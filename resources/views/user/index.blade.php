@extends('layouts.app-fluid')

@section('title')
    {{ __('user.List') }}
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
        <h1 class="h3 mb-0">
            {!! __('user.h1.List') !!}
        </h1>
        @hasanyrole(['super-admin'])
            @hasanyrole(['super-admin'])
                <div class="d-flex gap-1 gap-sm-2">
                    <a href="{{ route('user.create') }}" class="btn btn-orange">
                        {!! __('user.btn.New') !!}
                    </a>
                </div>
            @endhasanyrole
        @endhasanyrole
    </div>

    <div class="table-responsive">
        <table class="table align-middle datatable table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">{{ __('user.id') }}</th>
                    <th class="text-center">{{ __('user.fields.status') }}</th>
                    <th class="text-left">{{ __('user.fields.name') }}</th>
                    <th class="text-left">{{ __('user.fields.firstname') }}</th>
                    <th class="text-left">{{ __('user.fields.lastname') }}</th>
                    <th class="text-left">{{ __('user.fields.email') }}</th>
                    @hasanyrole(['super-admin'])
                        <th class="text-left">{{ __('user.fields.company_id') }}</th>
                    @endhasanyrole
                    <th class="text-left">{{ __('user.fields.roles') }}</th>
                    @hasanyrole(['super-admin'])
                        <th class="text-left">{{ __('user.fields.initial') }}</th>
                    @endhasanyrole
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
                        <td class="text-center">
                            <span
                                class="badge {{ __('user.statusBadgeColor.' . $user->status) }}">{{ __('user.status.' . $user->status) }}
                            </span>
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->firstname }}</td>
                        <td>{{ $user->lastname }}</td>
                        <td>{{ $user->email }}</td>
                        @hasanyrole(['super-admin'])
                            <td>{{ $user->company?->name ?? '' }}</td>
                        @endhasanyrole
                        <td>
                            @if ($user->getRoleNames()->isNotEmpty())
                                @foreach ($user->getRoleNames() as $role)
                                    <span
                                        class="badge {{ __('user.badgeRolesColor.' . $role) }}">{{ __('user.roles.' . $role) }}</span>
                                @endforeach
                            @endif
                        </td>
                        @hasanyrole(['super-admin'])
                            <td>{{ $user->initial }}</td>
                        @endhasanyrole
                        <td>{{ \App\Helpers\Helper::internationalFormatPhone($user->phone) }}</td>
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
                                {!! __('global.btn.Details') !!}
                            </a>
                            <a href="{{ route('user.edit', $user) }}" class="btn btn-link text-decoration-none p-0 me-2">
                                {!! __('global.btn.Edit') !!}
                            </a>
                            @if ($user->id !== auth()->id())
                                <span class="me-2">
                                    @include('user._delete_form', ['user' => $user])
                                </span>
                            @endif
                            @hasanyrole(['super-admin'])
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('user.impersonate', $user) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-decoration-none p-0 me-2 float-end"
                                            onclick="return confirm('{{ __('nav.Login') }} en tant que {{ $user->name }} ?')">
                                            <i class="fa-regular fa-arrow-right-to-bracket"></i>
                                        </button>
                                    </form>
                                @endif
                            @endhasanyrole
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="16" class="text-center">
                            {!! __('global.No data') !!}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @hasanyrole(['super-admin', 'manager', 'admin'])
        <div class="alert alert-info mt-3">
            <div class="d-flex align-items-start">
                <i class="fa-regular fa-info-circle me-2 mt-1"></i>
                <div>
                    <strong>Pour ajouter un nouvel utilisateur :</strong> Invitez-le à s'inscrire via le
                    <a href="{{ route('register') }}" class="alert-link" target="_blank">formulaire d'inscription</a>.
                    Une fois inscrit et approuvé, l'utilisateur apparaîtra dans cette liste.
                </div>
            </div>
        </div>
        <div class="alert alert-warning mt-3">
            @if (!empty($allowedDomains))
                <div class="d-flex align-items-start">
                    <i class="fa-regular fa-exclamation-triangle me-2 mt-1"></i>
                    <div>
                        Domaines d'emails autorisés pour les inscriptions automatiques :
                        <strong>{{ implode(', ', array_map(fn($domain) => '@' . $domain, $allowedDomains)) }}</strong>
                        <br>
                        Si vous avez besoin d'ajouter d'autres domaines, <a href="{{ route('contact.create') }}">contactez-nous</a>.
                    </div>
                </div>
            @else
                <div class="d-flex align-items-start">
                    <i class="fa-regular fa-exclamation-triangle me-2 mt-1"></i>
                    <div>
                        Aucun domaine d'email autorisé configuré pour votre société. <a href="{{ route('contact.create') }}">Contactez-nous</a> pour en ajouter.
                    </div>
                </div>
            @endif
        </div>
    @endhasanyrole
@endsection

@if ($users->count() > 0)
    @push('javascripts')
        @include('partials._datatable', [
            'datatableOptions' => [
                'order' => [[2, 'asc']], // Tri sur colonne 3 (index 2 = name) en ASC
                'columnDefs' => [
                    ['orderable' => false, 'targets' => -1] // Désactiver tri sur dernière colonne (Actions)
                ]
            ]
        ])
    @endpush
@endif
