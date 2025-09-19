@extends('layouts.app')

@section('title', __('crud.Details') . ' — ' . __('user.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('crud.Details') }} — {{ __('user.entity') }}</h1>

    <dl class="row">
        <dt class="col-sm-3">{{ __('user.id') }}</dt>
        <dd class="col-sm-9">{{ $user->id }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.name') }}</dt>
        <dd class="col-sm-9">{{ $user->name ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.email') }}</dt>
        <dd class="col-sm-9">{{ $user->email ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.password') }}</dt>
        <dd class="col-sm-9">••••••••</dd>
        <dt class="col-sm-3">{{ __('user.fields.status') }}</dt>
        <dd class="col-sm-9">
            @php($badgeColor = 'secondary')
            @switch($user->status)
                @case('active') @php($badgeColor = 'success') @break
                @case('inactive') @php($badgeColor = 'secondary') @break
            @endswitch
            <span class="badge bg-{{ $badgeColor }}">{{ __('user.enum.status.' . $user->status) }}</span>
        </dd>
        <dt class="col-sm-3">{{ __('user.fields.company_id') }}</dt>
        <dd class="col-sm-9">{{ $user->company_id ? \App\Models\Company::find($user->company_id)?->name : '—' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.firstname') }}</dt>
        <dd class="col-sm-9">{{ $user->firstname ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.lastname') }}</dt>
        <dd class="col-sm-9">{{ $user->lastname ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.phone') }}</dt>
        <dd class="col-sm-9">{{ $user->phone ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.last_login') }}</dt>
        <dd class="col-sm-9">{{ $user->last_login ? ($user->last_login instanceof \Carbon\Carbon ? $user->last_login->format('d/m/Y à H:i') : $user->last_login) : '—' }}</dd>
        <dt class="col-sm-3">{{ __('user.fields.agree_terms') }}</dt>
        <dd class="col-sm-9">
            @php($badgeColor = 'secondary')
            @switch($user->agree_terms)
                @case('oui') @php($badgeColor = 'primary') @break
                @case('non') @php($badgeColor = 'primary') @break
            @endswitch
            <span class="badge bg-{{ $badgeColor }}">{{ __('user.enum.agree_terms.' . $user->agree_terms) }}</span>
        </dd>
        <dt class="col-sm-3">{{ __('user.fields.channels') }}</dt>
        <dd class="col-sm-9"><pre>{{ is_array($user->channels) ? json_encode($user->channels, JSON_PRETTY_PRINT) : ($user->channels ?? '—') }}</pre></dd>
        <dt class="col-sm-3">{{ __('user.fields.note') }}</dt>
        <dd class="col-sm-9">{{ $user->note ?? '—' }}</dd>
    </dl>

    <div class="btn-group mt-3" role="group" aria-label="Actions">
        <a href="{{ route('user.edit', $user) }}" class="btn btn-primary">{{ __('crud.Edit') }}</a>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{{ __('crud.Back') }}</a>
    </div>
@endsection