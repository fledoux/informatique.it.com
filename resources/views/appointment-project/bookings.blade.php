@extends('layouts.app')

@section('title', 'Réservations - ' . $project->title)

@section('content')
    <div class="container-xl py-4">
        <div class="page-header d-mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title">Réservations</h1>
                    <p class="text-secondary">{{ $project->title }}</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('appointment-project.show', $project->id) }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>

        @if ($bookings->count() > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Date</th>
                                <th>Horaire</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->first_name }}</td>
                                    <td>{{ $booking->last_name }}</td>
                                    <td>
                                        <a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a>
                                    </td>
                                    <td>{{ $booking->phone }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($booking->appointment_date)->locale('fr')->translatedFormat('l d F Y') }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($booking->starts_at)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($booking->ends_at)->format('H:i') }}
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('appointment-project.bookings.destroy', [$project->id, $booking->id]) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer cette réservation">
                                                <i class="fa-solid fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-info mb-0">
                Aucune réservation pour le moment.
            </div>
        @endif
    </div>
@endsection
