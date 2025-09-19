@extends('layouts.app')

@section('title')
@if(auth()->check() && auth()->user()->hasRole('admin'))
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
<th class="text-left">{{ __('user.fields.password') }}</th>
<th class="text-left">{{ __('user.fields.status') }}</th>
<th class="text-left">{{ __('user.fields.company_id') }}</th>
<th class="text-left">{{ __('user.fields.firstname') }}</th>
<th class="text-left">{{ __('user.fields.lastname') }}</th>
<th class="text-left">{{ __('user.fields.phone') }}</th>
<th class="text-left">{{ __('user.fields.last_login') }}</th>
<th class="text-left">{{ __('user.fields.agree_terms') }}</th>
<th class="text-left">{{ __('user.fields.channels') }}</th>
<th class="text-left">{{ __('user.fields.note') }}</th>
<th>{{ __('crud.Actions') }}</th>
</tr>
</thead>
<tbody>
@forelse($users as $user)
<tr>
<td class="text-center">{{ $user->id }}</td>
<td>{{ $user->name }}</td>
<td>{{ $user->email }}</td>
<td>••••••••</td>
<td>
@php($badgeColor = 'secondary')
@switch($user->status)
    @case('active') @php($badgeColor = 'success') @break
    @case('inactive') @php($badgeColor = 'secondary') @break
@endswitch
<span class="badge bg-{{ $badgeColor }}">{{ __('user.enum.status.' . $user->status) }}</span>
</td>
<td>{{ $user->company_id ? \App\Models\Company::find($user->company_id)?->name : '—' }}</td>
<td>{{ $user->firstname }}</td>
<td>{{ $user->lastname }}</td>
<td>{{ $user->phone }}</td>
<td>{{ $user->last_login ? ($user->last_login instanceof \Carbon\Carbon ? $user->last_login->format('d/m/Y H:i') : $user->last_login) : '—' }}</td>
<td>
@php($badgeColor = 'secondary')
@switch($user->agree_terms)
    @case('oui') @php($badgeColor = 'primary') @break
    @case('non') @php($badgeColor = 'primary') @break
@endswitch
<span class="badge bg-{{ $badgeColor }}">{{ __('user.enum.agree_terms.' . $user->agree_terms) }}</span>
</td>
<td>@php($selected = collect(["email","sms"])->filter(fn($key) => $user->channels[$key] ?? false)->map(fn($key) => __('user.fields.channels_' . $key))->join(', ')){{ $selected ?: '—' }}</td>
<td>{{ $user->note }}</td>
<td class="text-nowrap">
<a href="{{ route('user.show', $user) }}" class="btn btn-link text-decoration-none p-0 me-2">
{{ __('crud.Details') }}
</a>
<a href="{{ route('user.edit', $user) }}" class="btn btn-link text-decoration-none p-0 me-2">
{{ __('crud.Edit') }}
</a>
@include('user._delete_form', ['user' => $user])
</td>
</tr>
@empty
<tr>
<td colspan="14" class="text-center">
{{ __('crud.No data') }}
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<a href="{{ route('user.create') }}" class="btn btn-orange mt-3">
{{ __('crud.New') }}
</a>
@endsection