@extends('layouts.html5')

@section('title', __('home.Welcome'))

@section('content')
    <div class="mx-2 mt-2">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <h1 class="text-center fw-bold text-danger"><i class="fa-solid fa-biohazard"></i> VIRUS ! <i
                            class="fa-solid fa-biohazard"></i></h1>
                    @if (request()->has('firstname'))
                        <h5 class="text-center mb-0"><strong>{{ ucfirst(request('firstname')) }}</strong>,<br>votre VIRUS
                            est&nbsp;bien&nbsp;installé sur&nbsp;votre&nbsp;téléphone !</h5>
                    @else
                        <h5 class="text-center mb-0">VIRUS installé sur&nbsp;votre&nbsp;téléphone !</h5>
                    @endif
                </div>
            </div>
        </div>

        <div class="row" id="contact-info" style="display: none;">
            <div class="col-12">
                <p><strong>Attention</strong>, les QR codes sont aussi des sources de virus et d'attaques.</p>
                <p><strong>{{ ucfirst(request('firstname')) }}</strong>, ne scannez que les QR&nbsp;codes provenant
                    de sources fiables.</p>
                <p>Bien évidemment, le message ci-dessus n'est pas réel, mais <u>soyez très vigilant(e)</u> !</p>
                <p>Merci à vous, d'avoir participé à cette démonstration de cybersécurité.</p>
                <div class="row g-2 mt-4">
                    <div class="col-3">
                        <img src="{{ asset('assets/img/cybersecurite/fledoux.jpg') }}" alt="{{ config('app.brand_name') }}"
                            class="img-fluid rounded">
                    </div>
                    <div class="col-9">
                        <img src="{{ asset('assets/img/logo/yellowcactus_logo_bw.svg') }}" alt="Yellow Cactus" class="img-fluid me-5 mb-3">
                        <div>Frédéric LEDOUX</div>
                        <div class="fw-bold">Expert en&nbsp;cybersécurité</div>
                        <div>Tél. : <a href="tel:+33149662177">01&nbsp;49&nbsp;66&nbsp;21&nbsp;77</a></div>
                        <div>Port. : <a href="tel:+33661478068">06&nbsp;61&nbsp;47&nbsp;80&nbsp;68</a></div>
                    </div>
                    <div class="col-12">
                        <ul>
                            <li><a href="mailto:fledoux@yellowcactus.com">fledoux@yellowcactus.com</a></li>
                            <li><a href="https://www.linkedin.com/in/fledoux/">LinkedIn</a></li>
                            <li><a href="https://github.com/fledoux">GitHub</a></li>
                            <li><a href="https://yellowcactus.com">yellowcactus.com</a></li>
                            <li><a href="https://extranet.yellowcactus.com/home/share/create">Envoyer un secret</a></li>
                            <li><a href="https://cyber.gouv.fr">cyber.gouv.fr</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row g-2 mb-5">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h4 class="fw-bold text-primary">GOES.EVENTS</h4>
                            <h6 class="fw-bold">NOUVELLE VERSION<br>v3.1 avec l'IA</h5>
                                <p class="mb-0">Solutions digitales au service de vos événements : <a
                                        href="https://goes.events">https://goes.events</a></p>
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
            // Afficher les informations de contact après 2 secondes avec un effet fadeIn
            setTimeout(function() {
                $('#contact-info').fadeIn(1000);
            }, 4000);
        });
    </script>
@endpush
