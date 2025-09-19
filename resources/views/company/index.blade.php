@extends('layouts.app')

@section('title')
@if(auth()->check() && auth()->user()->hasRole('admin'))
{{ __('company.List') }}
@else  
{{ __('company.YourList') }}
@endif
@endsection

@section('content')
<h1 class="mb-4">{{ __('company.List') }}</h1>

<div class="table-responsive-lg">
<table class="table align-middle table-xs table-bordered table-hover">
<thead>
<tr>
<th class="text-center">{{ __('company.id') }}</th>
<th class="text-left">{{ __('company.fields.status') }}</th>
<th class="text-left">{{ __('company.fields.name') }}</th>
<th class="text-left">{{ __('company.fields.siret') }}</th>
<th class="text-left">{{ __('company.fields.vat_number') }}</th>
<th class="text-left">{{ __('company.fields.email') }}</th>
<th class="text-left">{{ __('company.fields.phone') }}</th>
<th class="text-left">{{ __('company.fields.website') }}</th>
<th class="text-left">{{ __('company.fields.address_line1') }}</th>
<th class="text-left">{{ __('company.fields.address_line2') }}</th>
<th class="text-left">{{ __('company.fields.zip') }}</th>
<th class="text-left">{{ __('company.fields.city') }}</th>
<th class="text-left">{{ __('company.fields.country') }}</th>
<th class="text-left">{{ __('company.fields.notes') }}</th>
<th>{{ __('crud.Actions') }}</th>
</tr>
</thead>
<tbody>
@forelse($companies as $company)
<tr>
<td class="text-center">{{ $company->id }}</td>
<td>
@php($badgeColor = 'secondary')
@switch($company->status)
    @case('active') @php($badgeColor = 'success') @break
    @case('inactive') @php($badgeColor = 'secondary') @break
@endswitch
<span class="badge bg-{{ $badgeColor }}">{{ __('company.enum.status.' . $company->status) }}</span>
</td>
<td>{{ $company->name }}</td>
<td>{{ $company->siret }}</td>
<td>{{ $company->vat_number }}</td>
<td>{{ $company->email }}</td>
<td>{{ $company->phone }}</td>
<td>{{ $company->website }}</td>
<td>{{ $company->address_line1 }}</td>
<td>{{ $company->address_line2 }}</td>
<td>{{ $company->zip }}</td>
<td>{{ $company->city }}</td>
<td>{{ $company->country }}</td>
<td>{{ $company->notes }}</td>
<td class="text-nowrap">
<a href="{{ route('company.show', $company) }}" class="btn btn-link text-decoration-none p-0 me-2">
{{ __('crud.Details') }}
</a>
<a href="{{ route('company.edit', $company) }}" class="btn btn-link text-decoration-none p-0 me-2">
{{ __('crud.Edit') }}
</a>
@include('company._delete_form', ['company' => $company])
</td>
</tr>
@empty
<tr>
<td colspan="15" class="text-center">
{{ __('crud.No data') }}
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<a href="{{ route('company.create') }}" class="btn btn-orange mt-3">
{{ __('crud.New') }}
</a>
@endsection