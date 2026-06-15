@extends('layouts.app')

@section('title', 'Projets de rendez-vous')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Projets de rendez-vous</h1>
        <a href="{{ route('appointment-project.create') }}" class="btn btn-primary">Nouveau projet</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Serie</th>
                    <th>Titre</th>
                    <th>Duree</th>
                    <th>Horaires</th>
                    <th>Jours</th>
                    <th>Unique</th>
                    <th>Actif</th>
                    <th>Afficher noms</th>
                    <th>Lien public</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>{{ $project->id }}</td>
                        <td>{{ $project->serial }}</td>
                        <td>{{ $project->title }}</td>
                        <td>{{ $project->slot_duration_minutes }} min</td>
                        <td>{{ \Carbon\Carbon::parse($project->day_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($project->day_end_time)->format('H:i') }}</td>
                        <td>{{ $project->booking_horizon_days ?? 'ouvert' }}</td>
                        <td>{{ $project->single_registration_per_person ? 'oui' : 'non' }}</td>
                        <td>{{ $project->is_active ? 'oui' : 'non' }}</td>
                        <td class="text-center">
                            <form action="{{ route('appointment-project.toggle-booking-names', $project->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-link fs-4 p-0" title="Basculer l'affichage des noms de réservation">
                                    <i class="fa-solid {{ $project->show_booking_names ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger' }}"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ $project->getPublicUrl() }}" target="_blank" rel="noopener">Ouvrir</a>
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('appointment-project.show', $project->id) }}" class="btn btn-link p-0 me-2">Voir</a>
                            <a href="{{ route('appointment-project.edit', $project->id) }}" class="btn btn-link p-0 me-2">Editer</a>
                            <form action="{{ route('appointment-project.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger text-decoration-none p-0">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">Aucun projet de rendez-vous.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $projects->links() }}
@endsection
