@extends('layouts.app')

@section('title', 'Nouveau projet de rendez-vous')

@section('content')
    <h1 class="h3 mb-3">Nouveau projet de rendez-vous</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <form method="POST" action="{{ route('appointment-project.store') }}" novalidate>
                @include('appointment-project._form')
            </form>
        </div>
    </div>
@endsection
