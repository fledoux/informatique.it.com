<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/img/logo/logo-horizontal.svg') }}" alt="informatique.it.com" class="brand-logo d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navMain" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="ms-auto mb-2 btn w-100 {{ str_starts_with($currentRoute, 'ticket.') ? 'btn-orange' : 'btn-outline-secondary' }}" href="{{ route('user.index') }}">
                            <i class="fa-regular fa-message-question"></i>
                            {{ __('Nav.Support') }}
                        </a>
                    </li>
                @endauth
                @auth
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="ms-auto mb-2 btn w-100 {{ str_starts_with($currentRoute, 'user.') ? 'btn-orange' : 'btn-outline-secondary' }}" href="{{ route('user.index') }}">
                            <i class="fa-regular fa-users"></i>
                            {{ __('Nav.Users') }}
                        </a>
                    </li>
                @endauth
                @can('admin.access')
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="ms-auto mb-2 btn w-100 {{ str_starts_with($currentRoute, 'company.') ? 'btn-orange' : 'btn-outline-secondary' }}" href="{{ route('company.index') }}">
                            <i class="fa-regular fa-building"></i>
                            {{ __('Nav.Companies') }}
                        </a>
                    </li>
                @endcan
                @can('admin.access')
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="ms-auto mb-2 btn w-100 {{ str_starts_with($currentRoute, 'contact.') ? 'btn-orange' : 'btn-outline-secondary' }}" href="{{ route('contact.index') }}">
                            <i class="fa-regular fa-address-book"></i>
                            {{ $contactsCount ?? 0 }}
                            @if(($contactsCount ?? 0) <= 1)
                                {{ __('Nav.Contact') }}
                            @else
                                {{ __('Nav.Contacts') }}
                            @endif
                        </a>
                    </li>
                @endcan
                @auth
                    <li class="nav-item ms-lg-2 mb-2">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="fa-regular fa-arrow-right-from-bracket"></i>
                                {{ __('Nav.Logout') }}
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="btn btn-outline-secondary w-100" href="{{ route('register') }}">
                            <i class="fa-regular fa-user-plus"></i>
                            {{ __('Nav.Register') }}
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 mb-2">
                        <a class="btn btn-orange w-100" href="{{ route('login') }}">
                            <i class="fa-regular fa-arrow-right-to-bracket"></i>
                            {{ __('Nav.Login') }}
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>