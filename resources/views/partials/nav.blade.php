{{-- NAVBAR --}}
<nav class="navbar border-top border-5 border-orange navbar-expand-lg bg-white sticky-top shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/img/logo/logo-horizontal.svg') }}" alt="informatique.it.com"
                class="brand-logo d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navMain" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @can('ticket.index')
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="ms-auto mb-2 btn w-100 {{ $currentRoute && str_starts_with($currentRoute, 'ticket.') ? 'btn-orange' : 'btn-outline-secondary' }}"
                            href="{{ route('ticket.index') }}">
                            <i class="fa-regular fa-message-question"></i>
                            {{ __('nav.Support') }}
                        </a>
                    </li>
                @endcan
                @can('user.index')
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="ms-auto mb-2 btn w-100 {{ $currentRoute && str_starts_with($currentRoute, 'user.') ? 'btn-orange' : 'btn-outline-secondary' }}"
                            href="{{ route('user.index') }}">
                            <i class="fa-regular fa-users"></i>
                            {{ __('nav.Users') }}
                        </a>
                    </li>
                @endcan
                @can('company.index')
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="ms-auto mb-2 btn w-100 {{ $currentRoute && str_starts_with($currentRoute, 'company.') ? 'btn-orange' : 'btn-outline-secondary' }}"
                            href="{{ route('company.index') }}">
                            <i class="fa-regular fa-building"></i>
                            {{ __('nav.Companies') }}
                        </a>
                    </li>
                @endcan
                @can('contact.index')
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="ms-auto mb-2 btn w-100 {{ $currentRoute && str_starts_with($currentRoute, 'contact.') ? 'btn-orange' : 'btn-outline-secondary' }}"
                            href="{{ route('contact.index') }}">
                            <i class="fa-regular fa-address-book"></i>
                            {{ $contactsCount ?? 0 }}
                            @if (($contactsCount ?? 0) <= 1)
                                {{ __('nav.Contact') }}
                            @else
                                {{ __('nav.Contacts') }}
                            @endif
                        </a>
                    </li>
                @endcan
                @include('partials._lang')
                @auth
                    <li class="nav-item ms-lg-2 mb-2">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="fa-regular fa-arrow-right-from-bracket"></i>
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="btn btn-outline-secondary w-100" href="{{ route('register') }}">
                            <i class="fa-regular fa-user-plus"></i>
                            {{ __('nav.Register') }}
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="btn btn-orange w-100" href="{{ route('login') }}">
                            <i class="fa-regular fa-arrow-right-to-bracket"></i>
                            {{ __('nav.Login') }}
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Language Switcher Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gérer le changement de langue
        document.querySelectorAll('.language-switch').forEach(button => {
            button.addEventListener('click', function() {
                const locale = this.dataset.locale;
                const currentLocale = '{{ app()->getLocale() }}';

                // Ne rien faire si c'est déjà la langue courante
                if (locale === currentLocale) {
                    return;
                }

                // Afficher un indicateur de chargement
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ' + originalText
                    .replace(/🇫🇷|🇬🇧/, '');
                this.disabled = true;

                // Envoyer la requête
                fetch('{{ route('locale.change') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.getAttribute('content') ||
                                '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            locale: locale
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Recharger la page pour appliquer la nouvelle langue
                            window.location.reload();
                        } else {
                            console.error('Erreur lors du changement de langue:', data
                                .message);
                            // Restaurer le texte original
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur réseau:', error);
                        // Restaurer le texte original
                        this.innerHTML = originalText;
                        this.disabled = false;
                    });
            });
        });
    });
</script>
