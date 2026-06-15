@csrf
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <label class="form-label" for="serial">Numéro de série public</label>
        <input id="serial" name="serial" type="text" class="form-control @error('serial') is-invalid @enderror" value="{{ old('serial', $project->serial ?? '') }}" required>
        @error('serial')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-lg-8">
        <label class="form-label" for="title">Titre</label>
        <input id="title" name="title" type="text" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $project->title ?? '') }}" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="subtitle">Sous-titre</label>
        <textarea id="subtitle" name="subtitle" rows="3" class="form-control @error('subtitle') is-invalid @enderror">{{ old('subtitle', $project->subtitle ?? '') }}</textarea>
        @error('subtitle')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="notification_email">Email de notification</label>
        <input id="notification_email" name="notification_email" type="email" class="form-control @error('notification_email') is-invalid @enderror" value="{{ old('notification_email', $project->notification_email ?? '') }}" placeholder="Vide = email support par defaut">
        @error('notification_email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Reçoit un email à chaque nouvelle réservation.</div>
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label" for="slot_duration_minutes">Durée créneau (minutes)</label>
        <select id="slot_duration_minutes" name="slot_duration_minutes" class="form-select @error('slot_duration_minutes') is-invalid @enderror" required>
            @foreach ([15, 30, 45] as $duration)
                <option value="{{ $duration }}" {{ (int) old('slot_duration_minutes', $project->slot_duration_minutes ?? 30) === $duration ? 'selected' : '' }}>
                    {{ $duration }}
                </option>
            @endforeach
        </select>
        @error('slot_duration_minutes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label" for="day_start_time">Horaire debut</label>
        <input id="day_start_time" name="day_start_time" type="time" class="form-control @error('day_start_time') is-invalid @enderror" value="{{ old('day_start_time', isset($project->day_start_time) ? \Carbon\Carbon::parse($project->day_start_time)->format('H:i') : '08:00') }}" required>
        @error('day_start_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label" for="day_end_time">Horaire fin</label>
        <input id="day_end_time" name="day_end_time" type="time" class="form-control @error('day_end_time') is-invalid @enderror" value="{{ old('day_end_time', isset($project->day_end_time) ? \Carbon\Carbon::parse($project->day_end_time)->format('H:i') : '17:00') }}" required>
        @error('day_end_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="booking_horizon_days">Nombre de jours affiches</label>
        <input id="booking_horizon_days" name="booking_horizon_days" type="number" min="1" max="365" class="form-control @error('booking_horizon_days') is-invalid @enderror" value="{{ old('booking_horizon_days', $project->booking_horizon_days) }}" placeholder="Vide = pas de limite stricte">
        @error('booking_horizon_days')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Laisser vide pour un mode ouvert (fenetre etendue).</div>
    </div>

    <div class="col-12 col-md-6 d-flex align-items-end">
        <div class="form-check me-4">
            <input class="form-check-input" type="checkbox" id="single_registration_per_person" name="single_registration_per_person" value="1" {{ old('single_registration_per_person', $project->single_registration_per_person ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="single_registration_per_person">Une seule reservation par personne (email)</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $project->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Projet actif publiquement</label>
        </div>
    </div>
</div>

<div class="btn-group mt-3" role="group" aria-label="Actions formulaire projet rendez-vous">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="{{ route('appointment-project.index') }}" class="btn btn-outline-primary">Retour</a>
</div>
