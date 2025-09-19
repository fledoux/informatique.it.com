@extends('layouts.public')

@section('title', __('Home.Welcome'))

@section('content')

@php
$soc_nom = 'Yellow Cactus';
$soc_marque = 'informatique.it.com';
$soc_forme = 'SARL';
$soc_capital = '8 000 €';
$soc_siren = '430 411 173';
$soc_rcs = 'RCS Nanterre';
$soc_tva = 'FR86 430411173';
$soc_adresse = '10 Bis rue du Bel Air, 92310 Sèvres, France';
$soc_email = 'legal@informatique.it.com';
$soc_tel = '+33 1 49 66 21 77';
$dir_publication = 'Frédéric Ledoux';
$hebergeur_nom = 'Amazon AWS';
$hebergeur_ad = 'Tour Carpe Diem, 31 Place des Corolles, 92400 Courbevoie, France';
$hebergeur_site = 'https://aws.amazon.com';
@endphp

<section class="py-5">
    <div class="container">
        <h1 class="section-title text-center">Mentions légales</h1>
        <div class="gradient-bar"></div>
        <p class="text-secondary text-center mt-2">
            Dernière mise à jour : {{ date('01/m/Y') }}
        </p>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">1. Éditeur du site</h2>
            <p class="mb-0 text-secondary">
                Le site <strong>{{ $soc_marque }}</strong> est édité par <strong>{{ $soc_nom }}</strong>,
                {{ $soc_forme }} au capital de {{ $soc_capital }}, immatriculée au {{ $soc_rcs }}
                sous le n° <strong>{{ $soc_siren }}</strong>, n° de TVA intracommunautaire
                <strong>{{ $soc_tva }}</strong>.<br>
                Siège social : {{ $soc_adresse }}<br>
                Contact : <a href="mailto:{{ $soc_email }}" class="text-orange">{{ $soc_email }}</a> — {{ $soc_tel }}<br>
                Directeur·rice de la publication : <strong>{{ $dir_publication }}</strong>.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">2. Hébergement</h2>
            <p class="mb-0 text-secondary">
                Hébergeur : <strong>{{ $hebergeur_nom }}</strong> — {{ $hebergeur_ad }} —
                <a href="{{ $hebergeur_site }}" class="text-orange" target="_blank" rel="noopener">Site web</a>.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">3. Propriété intellectuelle</h2>
            <p class="mb-0 text-secondary">
                L'ensemble des contenus (textes, visuels, logos, icônes, mises en page) présents sur {{ $soc_marque }}
                est protégé par le droit d'auteur et les lois en vigueur. Toute reproduction, représentation ou diffusion, 
                totale ou partielle, sans autorisation écrite préalable est interdite.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">4. Responsabilité</h2>
            <p class="mb-0 text-secondary">
                Nous mettons tout en œuvre pour assurer l'exactitude et l'actualité des informations publiées. 
                Toutefois, nous ne saurions être tenus pour responsables des erreurs, omissions ou indisponibilités 
                temporaires du service. Les liens externes n'engagent pas notre responsabilité.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">5. Données personnelles</h2>
            <p class="mb-0 text-secondary">
                Pour toute information concernant le traitement de vos données et l'exercice de vos droits, 
                veuillez consulter notre <a href="{{ route('rgpd') }}" class="text-orange">Politique de confidentialité (RGPD)</a>.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">6. Cookies</h2>
            <p class="mb-0 text-secondary">
                Le site peut utiliser des cookies techniques nécessaires à son fonctionnement. 
                Les réglages éventuels de mesure d'audience ou de services tiers sont soumis à votre consentement (voir RGPD).
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">7. Médiation et droit applicable</h2>
            <p class="mb-0 text-secondary">
                En cas de litige, vous pouvez adresser une réclamation à <a href="mailto:{{ $soc_email }}" class="text-orange">{{ $soc_email }}</a>. 
                À défaut d'accord amiable, et si vous êtes consommateur, vous pouvez recourir à un médiateur de la consommation compétent. 
                Le droit applicable est le droit français, les tribunaux compétents sont ceux du siège de {{ $soc_nom }}, 
                sous réserve de dispositions légales impératives.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">8. Crédits</h2>
            <p class="mb-0 text-secondary">
                Conception & développement : {{ $soc_nom }}. Logos et marques cités restent la propriété de leurs titulaires. 
                Visuels sous licence et/ou créations internes.
            </p>
        </div>
    </div>
</section>

@endsection