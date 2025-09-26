{{-- Meta tags SEO optimisés --}}
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta name="description" content="{{ config('seo.site.description') }}">
<meta name="keywords" content="{{ config('seo.site.keywords') }}">
<meta name="author" content="{{ config('seo.site.author') }}">
<meta name="generator" content="Laravel {{ app()->version() }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ url()->current() }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:title" content="Support Informatique Professionnel - Expert depuis +25 ans">
<meta property="og:description" content="Expert en support informatique, assistance PC/Mac, infogérance et cybersécurité. Intervention rapide, devis gratuit 24h.">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ config('seo.site.name') }}">
<meta property="og:locale" content="fr_FR">
<meta property="og:image" content="{{ asset('favicon.ico') }}">
<meta property="og:image:width" content="64">
<meta property="og:image:height" content="64">
<meta property="og:image:alt" content="{{ config('seo.site.tagline') }} - {{ config('seo.site.name') }}">

{{-- Twitter Cards --}}
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Support Informatique Professionnel - Expert depuis +25 ans">
<meta name="twitter:description" content="Expert en support informatique, assistance PC/Mac, infogérance et cybersécurité. Intervention rapide, devis gratuit 24h.">
<meta name="twitter:image" content="{{ asset('favicon.ico') }}">
<meta name="twitter:site" content="{{ config('seo.social.twitter.handle') }}">
<meta name="twitter:creator" content="{{ config('seo.social.twitter.handle') }}">

{{-- Hreflang pour le multilingue --}}
<link rel="alternate" hreflang="fr" href="{{ url()->current() }}">
<link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
