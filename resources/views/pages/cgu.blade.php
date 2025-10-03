@extends('layouts.public')

@section('title', __('global.Terms of Use'))

@section('content')

@php
$soc_nom = 'Yellow Cactus';
$soc_marque = config('app.brand_name');
$soc_email = config('app.company.emails.legal');
@endphp

<section class="py-5">
    <div class="container">
        <h1 class="section-title text-center">Conditions Générales d'Utilisation (CGU)</h1>
        <div class="gradient-bar"></div>
        <p class="text-secondary text-center mt-2">
            Dernière mise à jour : {{ date('01/m/Y') }}
        </p>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">1. Objet et champ d'application</h2>
            <p class="mb-0 text-secondary">
                Les présentes conditions générales d'utilisation (ci-après « CGU ») régissent l'utilisation du site web 
                <strong>{{ $soc_marque }}</strong> et de la plateforme de support informatique éditée par 
                <strong>{{ $soc_nom }}</strong>. L'utilisation du site implique l'acceptation pleine et entière des présentes CGU.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">2. Accès au service</h2>
            <p class="text-secondary mb-2">
                L'accès à la plateforme est réservé aux entreprises clientes de <strong>{{ $soc_nom }}</strong>. 
                Pour accéder aux services, l'utilisateur doit :
            </p>
            <ul class="text-secondary mb-0">
                <li class="mb-1">Disposer d'un compte utilisateur valide créé par un administrateur</li>
                <li class="mb-1">S'authentifier avec ses identifiants personnels</li>
                <li class="mb-1">Respecter les conditions d'utilisation de sa société</li>
                <li class="mb-1">Utiliser le service dans le cadre de son activité professionnelle</li>
            </ul>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">3. Utilisation autorisée</h2>
            <p class="text-secondary mb-2">L'utilisateur s'engage à utiliser le service uniquement pour :</p>
            <ul class="text-secondary mb-0">
                <li class="mb-1">Créer et suivre des demandes de support informatique légitimes</li>
                <li class="mb-1">Échanger avec les techniciens dans le cadre des interventions</li>
                <li class="mb-1">Consulter l'historique des tickets de son entreprise</li>
                <li class="mb-1">Accéder aux ressources mises à disposition par {{ $soc_nom }}</li>
            </ul>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">4. Utilisation interdite</h2>
            <p class="text-secondary mb-2">Il est strictement interdit de :</p>
            <ul class="text-secondary mb-0">
                <li class="mb-1">Utiliser le service à des fins illégales ou frauduleuses</li>
                <li class="mb-1">Tenter d'accéder aux données d'autres entreprises</li>
                <li class="mb-1">Diffuser des contenus offensants, discriminatoires ou illicites</li>
                <li class="mb-1">Perturber le fonctionnement du service ou des serveurs</li>
                <li class="mb-1">Transmettre des virus ou codes malveillants</li>
                <li class="mb-1">Utiliser des robots ou scripts automatisés sans autorisation</li>
            </ul>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">5. Responsabilités de l'utilisateur</h2>
            <p class="text-secondary mb-2">L'utilisateur est responsable de :</p>
            <ul class="text-secondary mb-0">
                <li class="mb-1">La confidentialité de ses identifiants de connexion</li>
                <li class="mb-1">L'exactitude des informations qu'il communique</li>
                <li class="mb-1">Le respect des règles de sécurité informatique</li>
                <li class="mb-1">La sauvegarde de ses propres données</li>
                <li class="mb-1">L'utilisation appropriée des ressources du service</li>
            </ul>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">6. Propriété intellectuelle</h2>
            <p class="mb-0 text-secondary">
                Tous les éléments du site (textes, images, logos, structure, etc.) sont protégés par le droit d'auteur 
                et appartiennent à <strong>{{ $soc_nom }}</strong> ou à ses partenaires. Toute reproduction, 
                même partielle, est interdite sans autorisation écrite préalable.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">7. Données personnelles</h2>
            <p class="mb-0 text-secondary">
                Le traitement des données personnelles collectées dans le cadre de l'utilisation du service 
                est régi par notre <a href="{{ route('rgpd') }}" class="text-orange">Politique de confidentialité (RGPD)</a>. 
                L'utilisateur dispose d'un droit d'accès, de rectification et de suppression de ses données.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">8. Disponibilité du service</h2>
            <p class="mb-0 text-secondary">
                <strong>{{ $soc_nom }}</strong> s'efforce d'assurer une disponibilité optimale du service, 
                mais ne peut garantir un accès ininterrompu. Des interruptions peuvent survenir pour maintenance, 
                mise à jour ou en cas de force majeure. L'utilisateur en sera informé dans la mesure du possible.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">9. Limitation de responsabilité</h2>
            <p class="mb-0 text-secondary">
                <strong>{{ $soc_nom }}</strong> ne pourra être tenue responsable des dommages indirects 
                résultant de l'utilisation du service. La responsabilité est limitée aux dommages directs 
                et ne peut excéder le montant des prestations facturées à l'entreprise cliente.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">10. Suspension et résiliation</h2>
            <p class="mb-0 text-secondary">
                <strong>{{ $soc_nom }}</strong> se réserve le droit de suspendre ou supprimer l'accès 
                d'un utilisateur en cas de non-respect des présentes CGU, sans préavis ni indemnité. 
                L'utilisateur peut demander la suppression de son compte à tout moment.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">11. Évolution des CGU</h2>
            <p class="mb-0 text-secondary">
                Les présentes CGU peuvent être modifiées à tout moment. Les utilisateurs seront informés 
                des modifications par email ou via une notification sur la plateforme. La poursuite de 
                l'utilisation du service vaut acceptation des nouvelles conditions.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">12. Droit applicable et juridiction</h2>
            <p class="mb-0 text-secondary">
                Les présentes CGU sont soumises au droit français. Tout litige sera de la compétence 
                exclusive des tribunaux de Nanterre. En cas de contestation, les parties s'efforceront 
                de trouver une solution amiable avant tout recours judiciaire.
            </p>
        </div>

        <div class="mt-4 p-4 bg-white border rounded-4 shadow-soft">
            <h2 class="h4 fw-bold mb-3">13. Contact</h2>
            <p class="mb-0 text-secondary">
                Pour toute question relative aux présentes CGU, vous pouvez nous contacter à l'adresse : 
                <a href="mailto:{{ $soc_email }}" class="text-orange">{{ $soc_email }}</a>
            </p>
        </div>
    </div>
</section>

@endsection