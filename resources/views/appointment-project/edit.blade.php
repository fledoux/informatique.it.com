@extends('layouts.app')

@section('title', 'Modifier projet de rendez-vous')

@section('content')
    <h1 class="h3 mb-3">Modifier projet de rendez-vous</h1>

    <div class="card shadow-sm">
        <div class="card-body p-2 p-sm-3">
            <form method="POST" action="{{ route('appointment-project.update', $project->id) }}" novalidate>
                @csrf
                @method('PUT')
                @include('appointment-project._form')
            </form>
        </div>
    </div>
@endsection
