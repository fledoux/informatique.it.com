@extends('layouts.public')

@section('title', $project->title)

@section('content')
    <section class="py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-secondary-subtle border rounded-3 p-3 d-none d-sm-block" aria-hidden="true">
                        <i class="fa-regular fa-calendar fs-3 text-secondary"></i>
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

                <div class="card shadow-sm border-0 mb-4">
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

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button type="submit" class="btn btn-primary">Enregistrer mon rendez-vous</button>
                            </div>
                        </form>
                    </div>
                </div>

                <style>
                    .slots-grid {
                        display: grid;
                        gap: 0.75rem;
                        width: 100%;
                        grid-auto-rows: max-content;
                    }
                    .slot-btn {
                        word-wrap: break-word;
                        word-break: break-word;
                        overflow-wrap: break-word;
                        min-height: 100%;
                    }
                </style>

                @forelse ($dates as $day)
                    @if ($loop->first)
                        @php
                            // Collect all unique time slots across all days
                            $allTimes = collect();
                            foreach ($dates as $d) {
                                foreach ($d['slots'] as $s) {
                                    $allTimes->push($s['label']);
                                }
                            }
                            $uniqueTimes = $allTimes->unique()->sort()->values();
                            $dayCount = count($dates);
                        @endphp
                        <div class="slots-grid" style="grid-template-columns: repeat({{ $dayCount }}, 1fr);">
                            @foreach ($dates as $dayIndex => $d)
                                <div class="fw-semibold text-center text-lowercase text-body-secondary" style="grid-column: {{ $dayIndex + 1 }}; grid-row: 1;">
                                    {{ $d['date']->locale('fr')->translatedFormat('l d F') }}
                                </div>
                            @endforeach

                            @foreach ($uniqueTimes as $timeIndex => $time)
                                @foreach ($dates as $dayIndex => $d)
                                    @php
                                        $slotForTime = collect($d['slots'])->firstWhere('label', $time);
                                    @endphp
                                    @if ($slotForTime)
                                        @php
                                            $checked = old('selected_slot') === $slotForTime['key'];
                                            list($date, $slotTime) = explode('|', $slotForTime['key']);
                                            $slotStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "$date $slotTime");
                                            $isPast = $slotStart->isPast();
                                            $buttonClasses = 'btn btn-sm slot-btn';
                                            if ($isPast) {
                                                $buttonClasses .= ' btn-light text-body-tertiary border';
                                            } elseif ($slotForTime['is_break'] ?? false) {
                                                $buttonClasses .= ' btn-light text-body-tertiary border';
                                            } elseif ($slotForTime['is_available']) {
                                                $buttonClasses .= ' btn-outline-orange ' . ($checked ? 'btn-orange' : '');
                                            } else {
                                                $buttonClasses .= ' btn-light text-body-tertiary border';
                                            }
                                        @endphp
                                        <button
                                            type="button"
                                            class="{{ $buttonClasses }}"
                                            data-slot="{{ $slotForTime['key'] }}"
                                            style="grid-column: {{ $dayIndex + 1 }}; grid-row: {{ $timeIndex + 2 }}; padding: 0.625rem 1.25rem; font-size: 1rem; opacity: {{ $isPast ? 0.5 : 1 }};"
                                            {{ ($isPast || !$slotForTime['is_available'] || $slotForTime['is_break'] ?? false) ? 'disabled' : '' }}
                                        >
                                            @if (!($slotForTime['is_break'] ?? false))
                                                <span class="d-block">{{ $time }}</span>
                                            @endif
                                            @if ($slotForTime['break_text'] ?? false)
                                                <span class="d-block">{{ $slotForTime['break_text'] }}</span>
                                            @elseif (!$slotForTime['is_available'] && $project->show_booking_names && $slotForTime['booking_name'])
                                                <small class="d-block text-nowrap">{{ $slotForTime['booking_name'] }}</small>
                                            @endif
                                        </button>
                                    @else
                                        <div style="grid-column: {{ $dayIndex + 1 }}; grid-row: {{ $timeIndex + 2 }};"></div>
                                    @endif
                                @endforeach
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
