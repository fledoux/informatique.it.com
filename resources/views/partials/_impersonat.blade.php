    {{-- Indicateur d'impersonation --}}
    @if (session('impersonating_from'))
        <div class="alert alert-warning mb-0 text-center border-0 rounded-0 mb-4">
            <i class="fa-regular fa-user-gear me-2"></i>
            Vous êtes connecté en tant que <strong>{{ auth()->user()->name }}</strong>
            <form method="POST" action="{{ route('user.stop-impersonation') }}" class="d-inline ms-3">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-warning">
                    <i class="fa-regular fa-sign-out me-1"></i> Retour admin
                </button>
            </form>
        </div>
    @endif
