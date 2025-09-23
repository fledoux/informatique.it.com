@extends('layouts.app')

@section('title', 'Scannez-moi - QR Code')
@section('meta_description', 'Scannez ce QR code pour accéder facilement à informatique.it.com depuis votre téléphone')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fa-solid fa-qrcode text-orange me-3"></i>
                    Scannez-moi
                </h1>
                <p class="lead text-muted">
                    Scannez ce QR code pour <strong class="text-orange">nous retrouver sur votre portable</strong>
                </p>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-body p-5 text-center">
                    <!-- QR Code généré côté serveur -->
                    @if(!empty($dataUri))
                        <img src="{{ $dataUri }}" alt="QR Code informatique.it.com" class="mb-4" style="width:256px;height:256px;" />
                    @else
                        <div class="alert alert-danger">Impossible de générer le QR code</div>
                    @endif
                    
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Comment scanner ?</h5>
                        <div class="row g-3 text-start">
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon me-3 mt-1">
                                        <i class="fa-brands fa-apple"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold">iPhone/iPad</h6>
                                        <p class="small text-muted mb-0">
                                            Ouvrez l'appareil photo et pointez vers le QR code
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon me-3 mt-1">
                                        <i class="fa-brands fa-android"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold">Android</h6>
                                        <p class="small text-muted mb-0">
                                            Google Lens ou appareil photo natif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <i class="fa-regular fa-lightbulb me-2"></i>
                        <strong>Astuce :</strong> Ajoutez cette page à vos favoris !
                    </div>

                    <div class="text-center">
                        <a href="{{ route('home') }}" class="btn btn-orange">
                            <i class="fa-regular fa-house me-2"></i>
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="small text-muted">
                    <i class="fa-regular fa-mobile-screen-button me-1"></i>
                    Optimisé pour tous les appareils mobiles
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Génère le QR code avec l'URL actuelle du site
    const currentUrl = window.location.origin;
    
    QRCode.toCanvas(document.getElementById('qrcode'), currentUrl, {
        width: 256,
        height: 256,
        colorDark: '#ff6600', // Couleur orange de votre thème
        colorLight: '#ffffff',
        margin: 2,
        errorCorrectionLevel: 'M'
    }, function (error) {
        if (error) {
            console.error('Erreur lors de la génération du QR code:', error);
            document.getElementById('qrcode').innerHTML = 
                '<div class="alert alert-danger">Impossible de générer le QR code</div>';
        }
    });
});
</script>
@endpush