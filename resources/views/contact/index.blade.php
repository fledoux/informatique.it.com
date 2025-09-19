@extends('layouts.app')

@section('title')
@if(auth()->check() && auth()->user()->hasRole('admin'))
{{ __('contact.List') }}
@else  
{{ __('contact.YourList') }}
@endif
@endsection

@section('content')
<h1 class="mb-4">{{ __('contact.List') }}</h1>

<div class="table-responsive-lg">
<table class="table align-middle table-xs table-bordered table-hover">
<thead>
<tr>
<th class="text-center">{{ __('contact.id') }}</th>
<th class="text-left">{{ __('contact.fields.name') }}</th>
<th class="text-left">{{ __('contact.fields.email') }}</th>
<th class="text-left">{{ __('contact.fields.phone') }}</th>
<th class="text-left">{{ __('contact.fields.type') }}</th>
<th class="text-left">{{ __('contact.fields.need') }}</th>
<th>{{ __('crud.Actions') }}</th>
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
@php($badgeColor = 'secondary')
@switch($contact->type)
    @case('active') @php($badgeColor = 'success') @break
    @case('inactive') @php($badgeColor = 'secondary') @break
@endswitch
<span class="badge bg-{{ $badgeColor }}">{{ __('contact.enum.type.' . $contact->type) }}</span>
</td>
<td>{{ $contact->need }}</td>
<td class="text-nowrap">
<a href="{{ route('contact.show', $contact) }}" class="btn btn-link text-decoration-none p-0 me-2">
{{ __('crud.Details') }}
</a>
<a href="{{ route('contact.edit', $contact) }}" class="btn btn-link text-decoration-none p-0 me-2">
{{ __('crud.Edit') }}
</a>
@include('contact._delete_form', ['contact' => $contact])
</td>
</tr>
@empty
<tr>
<td colspan="7" class="text-center">
{{ __('crud.No data') }}
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<a href="{{ route('contact.create') }}" class="btn btn-orange mt-3">
{{ __('crud.New') }}
</a>
@endsection