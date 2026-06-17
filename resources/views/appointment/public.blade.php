@extends('layouts.public')

@section('title', $project->title)

@section('content')
    <section class="py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-secondary-subtle border rounded-3 p-3 d-none d-sm-block" aria-hidden="true">
                        <i class="fa-regular fa-calendar-days fs-3 text-secondary"></i>
                    </div>
                    <div>
                        <h1 class="display-6 fw-semibold mb-2">{{ $project->title }}</h1>
                        @if ($project->subtitle)
                            <div class="lead mb-2">{!! $project->subtitle !!}</div>
                        @endif
                        <p class="text-body-secondary mb-0">
                            Créneaux de {{ $project->slot_duration_minutes }} minutes - Horaires {{ \Carbon\Carbon::parse($project->day_start_time)->format('H\hi') }} à {{ \Carbon\Carbon::parse($project->day_end_time)->format('H\hi') }}
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="POST" action="{{ route('appointment.public.store', ['serial' => $project->serial]) }}" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-12 col-md-3">
                                    <label for="first_name" class="form-label">Prénom</label>
                                    <input
                                        id="first_name"
                                        type="text"
                                        name="first_name"
                                        class="form-control @error('first_name') is-invalid @enderror"
                                        value="{{ old('first_name') }}"
                                        required
                                    >
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="last_name" class="form-label">Nom</label>
                                    <input
                                        id="last_name"
                                        type="text"
                                        name="last_name"
                                        class="form-control @error('last_name') is-invalid @enderror"
                                        value="{{ old('last_name') }}"
                                        required
                                    >
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}"
                                        required
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}"
                                        required
                                    >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <input type="hidden" id="selected_slot" name="selected_slot" value="{{ old('selected_slot') }}">
                                    @error('selected_slot')
                                        <div class="alert alert-danger py-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">Enregistrer mon rendez-vous</button>
                            </div>
                        </form>
                    </div>
                </div>

                <style>
                    .slot-btn {
                        min-height: 60px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        gap: 0.25rem;
                    }
                </style>

                @forelse ($dates as $day)
                    @if ($loop->first)
                        <div class="row g-2">
                            @foreach ($dates as $d)
                                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                    <!-- Titre du jour -->
                                    <div class="fw-bold text-center text-lowercase text-body-secondary small mt-4 mb-2">
                                        {{ $d['date']->locale('fr')->translatedFormat('l d F') }}
                                    </div>
                                    
                                    <!-- Créneaux du jour empilés -->
                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($d['slots'] as $slotForTime)
                                            @php
                                                $checked = old('selected_slot') === $slotForTime['key'];
                                                list($date, $slotTime) = explode('|', $slotForTime['key']);
                                                $slotStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "$date $slotTime");
                                                $isPast = $slotStart->isPast();
                                                $buttonClasses = 'btn btn-sm w-100 slot-btn';
                                                if ($isPast) {
                                                    $buttonClasses .= ' btn-light text-body-tertiary border';
                                                } elseif ($slotForTime['is_break'] ?? false) {
                                                    $buttonClasses .= ' btn-light text-body-tertiary border border-primary';
                                                } elseif ($slotForTime['is_available']) {
                                                    $buttonClasses .= ' btn-outline-success ' . ($checked ? 'btn-success' : '');
                                                } else {
                                                    $buttonClasses .= ' btn-outline-orange';
                                                }
                                            @endphp
                                            <button
                                                type="button"
                                                class="{{ $buttonClasses }}"
                                                data-slot="{{ $slotForTime['key'] }}"
                                                style="opacity: {{ $isPast ? 0.5 : 1 }};"
                                                {{ ($isPast || !$slotForTime['is_available'] || $slotForTime['is_break'] ?? false) ? 'disabled' : '' }}
                                            >
                                                @if (!($slotForTime['is_break'] ?? false))
                                                    <span class="d-block fw-bold">{{ $slotForTime['label'] }}</span>
                                                @endif
                                                @if ($slotForTime['break_text'] ?? false)
                                                    <span class="d-block fw-bold text-primary">{{ $slotForTime['break_text'] }}</span>
                                                @elseif (!$slotForTime['is_available'] && $project->show_booking_names && $slotForTime['booking_name'])
                                                    <span class="d-block small">{{ $slotForTime['booking_name'] }}</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @empty
                    <div class="alert alert-info mb-0">
                        Aucun créneau n'est disponible pour le moment. Merci de revenir ultérieurement.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('javascripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slotInput = document.getElementById('selected_slot');
        const slotButtons = document.querySelectorAll('.slot-btn[data-slot]');

        slotButtons.forEach((button) => {
            button.addEventListener('click', function () {
                const selected = this.getAttribute('data-slot');
                slotInput.value = selected;

                slotButtons.forEach((btn) => {
                    btn.classList.remove('btn-orange');
                    if (!btn.disabled) {
                        btn.classList.add('btn-outline-orange');
                    }
                });

                this.classList.remove('btn-outline-orange');
                this.classList.add('btn-orange');
            });
        });
    });
</script>
@endpush
