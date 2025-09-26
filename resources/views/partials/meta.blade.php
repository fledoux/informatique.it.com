{{-- Meta tags SEO optimisés --}}
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta name="description" content="Expert en support informatique, dépannage PC/Mac, infogérance et cybersécurité pour entreprises à France. Intervention rapide, devis gratuit 24h, maintenance proactive. +25 ans d'expérience, 9999+ interventions réussies.">
<meta name="keywords" content="support informatique, dépannage informatique, infogérance, cybersécurité, maintenance informatique, assistance PC Mac, expert informatique France, dépannage rapide, support technique">
<meta name="author" content="Informatique.it.com">
<meta name="generator" content="Laravel {{ app()->version() }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ url()->current() }}">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="@yield('og:type', 'website')">
<meta property="og:title" content="@yield('og:title', __('home.Welcome') . ' - Expert depuis +25 ans')">
<meta property="og:description" content="@yield('og:description', 'Expert en support informatique, dépannage PC/Mac, infogérance et cybersécurité. Intervention rapide, devis gratuit 24h. +25 ans d\'expérience, 9999+ interventions réussies.')">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Informatique.it.com">
<meta property="og:locale" content="fr_FR">
<meta property="og:image" content="@yield('og:image', asset('favicon.ico'))">
<meta property="og:image:width" content="64">
<meta property="og:image:height" content="64">
<meta property="og:image:alt" content="Support informatique professionnel - Informatique.it.com">

{{-- Twitter Cards --}}
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="@yield('twitter:title', __('home.Welcome') . ' - Expert depuis +25 ans')">
<meta name="twitter:description" content="@yield('twitter:description', 'Expert en support informatique, dépannage PC/Mac, infogérance et cybersécurité. Intervention rapide, devis gratuit 24h.')">
<meta name="twitter:image" content="@yield('twitter:image', asset('favicon.ico'))">
<meta name="twitter:site" content="{{ config('seo.social.twitter.handle') }}">
<meta name="twitter:creator" content="{{ config('seo.social.twitter.handle') }}">

{{-- Hreflang pour le multilingue --}}
<link rel="alternate" hreflang="fr" href="{{ url()->current() }}">
<link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

{{-- Schema.org JSON-LD --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Informatique.it.com",
  "description": "Expert en support informatique, dépannage PC/Mac, infogérance et cybersécurité pour entreprises. Intervention rapide, maintenance proactive.",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('favicon.ico') }}",
  "image": "{{ asset('favicon.ico') }}",
  "telephone": "+33-X-XX-XX-XX-XX",
  "email": "contact@informatique.it.com",
  "address": {
    "@type": "PostalAddress",
    "addressCountry": "FR",
    "addressLocality": "France"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "46.603354",
    "longitude": "1.888334"
  },
  "openingHours": "Mo-Fr 09:00-18:00",
  "priceRange": "€€",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "reviewCount": "370",
    "bestRating": "5",
    "worstRating": "1"
  },
  "serviceArea": {
    "@type": "Country",
    "name": "France"
  },
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Services informatiques",
    "itemListElement": [
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "name": "Support informatique",
          "description": "Dépannage et assistance informatique pour PC et Mac"
        }
      },
      {
        "@type": "Offer", 
        "itemOffered": {
          "@type": "Service",
          "name": "Infogérance",
          "description": "Gestion et maintenance complète de votre infrastructure informatique"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service", 
          "name": "Cybersécurité",
          "description": "Protection et sécurisation de vos données et systèmes informatiques"
        }
      }
    ]
  },
  "sameAs": [
    "{{ config('seo.social.twitter.url') }}",
    "{{ config('seo.social.linkedin.url') }}"
  ],
  "potentialAction": {
    "@type": "SearchAction",
    "target": "{{ url('/') }}?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>