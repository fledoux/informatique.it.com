@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('company.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('company.h1.Edit') !!} {{ $company->name ?? __('company.entity') }}</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <form method="POST" action="{{ route('company.update', $company) }}" novalidate>
                @csrf @method('PUT')
                @include('company._form')
            </form>
        </div>
    </div>
@endsection