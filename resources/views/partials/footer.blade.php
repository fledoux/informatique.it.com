{{-- Footer --}}
<footer class="mt-5 px-5 bg-white footer mt-auto">
    <div class="container-fluid px-0 py-4 d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-between text-center text-md-start">
        <a href="https://www.yellowcactus.com" target="_blank" class="mb-3 mb-md-0">
            <img src="{{ asset('assets/img/logo/yellowcactus_logo_bw.svg') }}" alt="Yellow Cactus" style="height:24px;" class="ms-2">
        </a>
        <div class="small text-muted mb-3 mb-md-0">©
            {{ date('Y') }},
            <a href="https://www.yellowcactus.com" target="_blank" class="text-orange">Yellow&nbsp;Cactus</a>.
            <a href="/" class="text-orange">informatique.it.com</a>
            est une marque de
            <a href="https://www.yellowcactus.com" target="_blank" class="text-orange">Yellow&nbsp;Cactus</a>. Tous&nbsp;droits&nbsp;réservés.
        </div>
        <div class="small">
            <a href="{{ route('legal') }}" class="me-3 text-orange">Mentions&nbsp;légales</a>
            <a href="{{ route('rgpd') }}" class="me-3 text-orange">RGPD</a>
            <a href="{{ route('cgv') }}" class="text-orange">CGV</a>
        </div>
    </div>
</footer>