@extends('layouts.app')

@section('title', __('user.TitleDetails') . '  ' . ($user->firstname ?? '') . ' ' . ($user->lastname ?? ''))

@section('content')
    <h1 class="h3 mb-3">{!! __('user.TitleDetails') !!}  {{ __('user.entity') }}</h1>

    <dl class="row">
        <dt class="col-sm-3">{{ __('user.id') }}</dt>
        <dd class="col-sm-9">{{ $user->id }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.name') }}</dt>
        <dd class="col-sm-9">{{ $user->name ?? '' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.email') }}</dt>
        <dd class="col-sm-9">{{ $user->email ?? '' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.password') }}</dt>
        <dd class="col-sm-9">••••••••</dd>
        <dt class="col-sm-3">{{ __('user.fields.status') }}</dt>
        <dd class="col-sm-9">
            @php($badgeColor = 'secondary')
            @switch($user->status)
                @case('active')
                    @php($badgeColor = 'success')
                @break
                @case('inactive')
                    @php($badgeColor = 'secondary')
                @break
            @endswitch
            <span class="badge bg-{{ $badgeColor }}">{{ __('user.status.' . $user->status) }}</span>
        </dd>
        <dt class="col-sm-3">{{ __('user.fields.company_id') }}</dt>
        <dd class="col-sm-9">{{ $user->company_id ? \App\Models\Company::find($user->company_id)?->name : '' }}</dd>
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
        <dd class="col-sm-9">
            {!! __('user.statusAgreeTermsColor.' . $user->agree_terms) !!}
        </dd>
        <dt class="col-sm-3">{{ __('user.fields.channels') }}</dt>
        <dd class="col-sm-9">
            @if ($user->getRoleNames()->isNotEmpty())
                @foreach ($user->getRoleNames() as $role)
                    <span class="badge bg-info me-1">{{ __('user.roles.' . $role) }}</span>
                @endforeach
            @else
                <span class="text-secondary"></span>
            @endif
        </dd>
        <dt class="col-sm-3">{{ __('user.fields.note') }}</dt>
        <dd class="col-sm-9">{{ $user->note ?? '' }}</dd>
    </dl>

    <div class="btn-group mt-3" role="group" aria-label="Actions">
        <a href="{{ route('user.edit', $user) }}" class="btn btn-primary">{!! __('global.Edit') !!}</a>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
    </div>
@endsection
