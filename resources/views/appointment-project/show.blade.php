@extends('layouts.app')

@section('title', 'Detail projet de rendez-vous')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0">Détail projet: {{ $project->title }}</h1>
        <div class="gap-2 d-flex">
            <a href="{{ route('appointment-project.bookings', $project->id) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-calendar-check"></i> Réservations
            </a>
            <a href="{{ route('appointment-project.edit', $project->id) }}" class="btn btn-primary">Modifier</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">Numéro de série</dt>
                <dd class="col-sm-8">{{ $project->serial }}</dd>

                <dt class="col-sm-4">Sous-titre</dt>
                <dd class="col-sm-8">{!! $project->subtitle ?: '-' !!}</dd>

                <dt class="col-sm-4">Email de notification</dt>
                <dd class="col-sm-8">{{ $project->notification_email ?: 'Email support par défaut' }}</dd>

                <dt class="col-sm-4">Durée de créneau</dt>
                <dd class="col-sm-8">{{ $project->slot_duration_minutes }} minutes</dd>

                <dt class="col-sm-4">Horaires</dt>
                <dd class="col-sm-8">{{ \Carbon\Carbon::parse($project->day_start_time)->format('H\hi') }} à {{ \Carbon\Carbon::parse($project->day_end_time)->format('H\hi') }}</dd>

                <dt class="col-sm-4">Nombre de jours affichés</dt>
                <dd class="col-sm-8">{{ $project->booking_horizon_days ?? 'ouvert (pas de limite stricte)' }}</dd>

                <dt class="col-sm-4">Réservation unique</dt>
                <dd class="col-sm-8">{{ $project->single_registration_per_person ? 'active' : 'inactive' }}</dd>

                <dt class="col-sm-4">Actif publiquement</dt>
                <dd class="col-sm-8">{{ $project->is_active ? 'oui' : 'non' }}</dd>

                <dt class="col-sm-4">Lien public</dt>
                <dd class="col-sm-8"><a href="{{ $project->getPublicUrl() }}" target="_blank" rel="noopener">{{ $project->getPublicUrl() }}</a></dd>
            </dl>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h2 class="h5 mb-0">Gestion des créneaux</h2>
        </div>
        <div class="card-body">
            <p class="text-body-secondary">
                Seuls les créneaux enregistrés ci-dessous sont proposés sur la page publique.
                Génère une journée complète ({{ \Carbon\Carbon::parse($project->day_start_time)->format('H\hi') }} à {{ \Carbon\Carbon::parse($project->day_end_time)->format('H\hi') }}, toutes les {{ $project->slot_duration_minutes }} min), puis supprime les créneaux qui ne te conviennent pas. Tu peux aussi ajouter une heure précise.
            </p>

            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="border rounded-3 p-3 h-100">
                        <h3 class="h6">Générer une journée</h3>
                        <form method="POST" action="{{ route('appointment-project.slots.generate', $project->id) }}" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-12 col-sm-5">
                                <label class="form-label" for="generate_date">Jour</label>
                                <input id="generate_date" name="generate_date" type="date" class="form-control @error('generate_date') is-invalid @enderror" value="{{ old('generate_date') }}" required>
                                @error('generate_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label" for="break_time_minutes">Pause entre les RDV</label>
                                <div class="input-group">
                                    <input id="break_time_minutes" name="break_time_minutes" type="number" class="form-control @error('break_time_minutes') is-invalid @enderror" value="{{ old('break_time_minutes', 10) }}" min="0" max="60" required>
                                    <span class="input-group-text">min</span>
                                </div>
                                @error('break_time_minutes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-3">
                                <button type="submit" class="btn btn-primary w-100">Générer la liste</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="border rounded-3 p-3 h-100">
                        <h3 class="h6">Ajouter une heure précise</h3>
                        <form method="POST" action="{{ route('appointment-project.slots.store', $project->id) }}" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-12 col-sm-5">
                                <label class="form-label" for="slot_date">Jour</label>
                                <input id="slot_date" name="slot_date" type="date" class="form-control @error('slot_date') is-invalid @enderror" value="{{ old('slot_date') }}" required>
                                @error('slot_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label" for="slot_time">Heure</label>
                                <input id="slot_time" name="slot_time" type="time" class="form-control @error('slot_time') is-invalid @enderror" value="{{ old('slot_time') }}" required>
                                @error('slot_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-5">
                                <label class="form-label" for="break_text">Texte de pause (optionnel)</label>
                                <input id="break_text" name="break_text" type="text" class="form-control @error('break_text') is-invalid @enderror" value="{{ old('break_text') }}" placeholder="ex: Pause déj, Non disponible">
                                @error('break_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-3">
                                <button type="submit" class="btn btn-outline-primary w-100">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @php($slotsByDay = $manualSlots->groupBy(fn ($slot) => \Carbon\Carbon::parse($slot->slot_date)->toDateString()))

            @if ($slotsByDay->isEmpty())
                <p class="text-body-secondary mb-0">Aucun créneau pour le moment. Génére une journée pour commencer.</p>
            @else
                <div class="row g-3">
                    @foreach ($slotsByDay as $day => $slots)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h3 class="h6 mb-0">{{ \Carbon\Carbon::parse($day)->locale('fr')->translatedFormat('l d F Y') }}</h3>
                                    <form method="POST" action="{{ route('appointment-project.slots.destroyDay', [$project->id, $day]) }}" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer tous les créneaux de cette journée ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-1" title="Supprimer la journée entière">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($slots as $slot)
                                        @php($slotDateTime = \Carbon\Carbon::parse($slot->slot_date . ' ' . $slot->slot_time)->format('Y-m-d H:i'))
                                        @php($isBooked = $bookedSlots->contains($slotDateTime))
                                        @php($buttonClass = $slot->break_text ? 'btn-primary' : ($isBooked ? 'btn-outline-orange' : 'btn-outline-secondary'))
                                        <form method="POST" action="{{ route('appointment-project.slots.destroy', [$project->id, $slot->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm {{ $buttonClass }}" title="Cliquer pour supprimer">
                                                {{ \Carbon\Carbon::parse($slot->slot_time)->format('H:i') }}
                                                @if(!$slot->break_text && !$isBooked)
                                                    <i class="fa-solid fa-xmark ms-1 text-danger"></i>
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
