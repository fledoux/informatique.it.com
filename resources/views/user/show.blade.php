@extends('layouts.app')

@section('title', __('user.TitleDetails') . ' ' . ($user->firstname ?? '') . ' ' . ($user->lastname ?? ''))

@section('content')
    <h1 class="h3 mb-3">{!! __('user.TitleDetails') !!} {{ $user->firstname ? $user->firstname : __('user.entity') }}
        {{ $user->lastname ? $user->lastname : '' }}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('user.id') }}</dt>
                <dd class="col-sm-9">{{ $user->id }}</dd>
                <dt class="col-sm-3">{{ __('user.fields.status') }}</dt>
                <dd class="col-sm-9"><span
                        class="badge {{ __('user.statusBadgeColor.' . $user->status) }}">{{ __('user.status.' . $user->status) }}</span>
                </dd>
                <dt class="col-sm-3">{{ __('user.fields.roles') }}</dt>
                <dd class="col-sm-9">
                    @if ($user->getRoleNames()->isNotEmpty())
                        @foreach ($user->getRoleNames() as $role)
                            <span
                                class="badge {{ __('user.badgeRolesColor.' . $role) }}">{{ __('user.roles.' . $role) }}</span>
                        @endforeach
                    @endif
                </dd>
                <dt class="col-sm-3">{{ __('user.fields.name') }}</dt>
                <dd class="col-sm-9">{{ $user->name ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('user.fields.email') }}</dt>
                <dd class="col-sm-9">{{ $user->email ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('user.fields.password') }}</dt>
                <dd class="col-sm-9">••••••••</dd>

                <dt class="col-sm-3">{{ __('user.fields.company_id') }}</dt>
                <dd class="col-sm-9">{{ $user->company_id ? \App\Models\Company::find($user->company_id)?->name : '' }}
                </dd>
                <dt class="col-sm-3">{{ __('user.fields.firstname') }}</dt>
                <dd class="col-sm-9">{{ $user->firstname ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('user.fields.lastname') }}</dt>
                <dd class="col-sm-9">{{ $user->lastname ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('user.fields.initial') }}</dt>
                <dd class="col-sm-9">{{ $user->initial ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('user.fields.phone') }}</dt>
                <dd class="col-sm-9">{{ $user->phone ?? '' }}</dd>
                <dt class="col-sm-3">{{ __('user.fields.last_login') }}</dt>
                <dd class="col-sm-9">
                    {{ $user->last_login ? ($user->last_login instanceof \Carbon\Carbon ? $user->last_login->format('d/m/Y à H:i') : $user->last_login) : '' }}
                </dd>
                <dt class="col-sm-3">{{ __('user.fields.agree_terms') }}</dt>
                <dd class="col-sm-9">{!! __('user.statusAgreeTermsColor.' . $user->agree_terms) !!}</dd>
                <dt class="col-sm-3">{{ __('user.fields.channels') }}</dt>
                <dd class="col-sm-9">
                    @if ($user->channels)
                        @foreach ($user->channels as $key => $channel)
                            <span class="badge bg-info me-1">{{ __('user.channels.' . $key) }}</span>
                        @endforeach
                    @endif
                </dd>
                @hasanyrole(['super-admin'])
                    <dt class="col-sm-3">{{ __('user.fields.note') }}</dt>
                    <dd class="col-sm-9">{{ $user->note ?? '' }}</dd>
                @endhasanyrole
            </dl>

            <div class="btn-group mt-3" role="group" aria-label="Actions">
                <a href="{{ route('user.edit', $user) }}" class="btn btn-primary">{!! __('global.btn.Edit') !!}</a>
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
            </div>
        </div>
    </div>
@endsection
