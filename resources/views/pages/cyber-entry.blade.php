@extends('layouts.html5')

@section('title', 'Formation Cybersécurité')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-shield-halved fa-4x text-primary mb-3"></i>
                            <h2 class="fw-bold">Formation<br>Cyber-sécurité</h2>
                            <p class="text-muted">Veuillez vous identifier<br>pour obtenir votre cadeau</p>
                        </div>

                        <form method="POST" action="{{ route('cybersecurite.submit') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="firstname" class="form-label fw-bold">
                                    Votre prénom <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('firstname') is-invalid @enderror" 
                                       id="firstname" 
                                       name="firstname" 
                                       placeholder="Entrez votre prénom"
                                       value="{{ old('firstname') }}"
                                       required
                                       autofocus>
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-regular fa-gift me-2"></i>
                                    Continuer
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fa-solid fa-lock me-1"></i>
                                Vos données sont sécurisées
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascripts')
<script>
$(document).ready(function() {
    // Animation du formulaire au chargement
    $('.card').hide().fadeIn(800);
    
    // Animation lors de la soumission
    $('#entry-form').on('submit', function() {
        $(this).find('button[type="submit"]').html('<i class="fa-solid fa-spinner fa-spin me-2"></i>Chargement...');
    });
});
</script>
@endpush
