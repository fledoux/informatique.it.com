@if (app()->isLocal())
    {{-- Language Selector --}}
    <li class="nav-item ms-lg-2 mb-2">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="languageDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-regular fa-flag"></i>
                @if (app()->getLocale() == 'fr')
                    FR
                @else
                    EN
                @endif
            </button>
            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                <li>
                    <button class="dropdown-item language-switch" data-locale="fr" type="button">
                        🇫🇷 {{ __('nav.French') }}
                    </button>
                </li>
                <li>
                    <button class="dropdown-item language-switch" data-locale="en" type="button">
                        🇬🇧 {{ __('nav.English') }}
                    </button>
                </li>
            </ul>
        </div>
    </li>

    {{-- Language Switcher Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            {{-- Gérer le changement de langue --}}
            document.querySelectorAll('.language-switch').forEach(button => {
                button.addEventListener('click', function() {
                    const locale = this.dataset.locale;
                    const currentLocale = '{{ app()->getLocale() }}';

                    {{-- Ne rien faire si c'est déjà la langue courante --}}
                    if (locale === currentLocale) {
                        return;
                    }

                    {{-- Afficher un indicateur de chargement --}}
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ' + originalText
                        .replace(/🇫🇷|🇬🇧/, '');
                    this.disabled = true;

                    {{-- Envoyer la requête --}}
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
                                {{-- Recharger la page pour appliquer la nouvelle langue --}}
                                window.location.reload();
                            } else {
                                console.error('Erreur lors du changement de langue:', data
                                    .message);
                                {{-- Restaurer le texte original --}}
                                this.innerHTML = originalText;
                                this.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Erreur réseau:', error);
                            {{-- Restaurer le texte original --}}
                            this.innerHTML = originalText;
                            this.disabled = false;
                        });
                });
            });
        });
    </script>
@endif
