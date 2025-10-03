<div class="dropdown w-100">
    <button class="btn btn-orange dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-regular fa-bars me-2"></i>Actions
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('ticket.edit', $ticket) }}">
                {!! __('ticket.Answer') !!}
            </a></li>
        @can('ticket.edit')
            <li><a class="dropdown-item" href="{{ route('ticket.edit', $ticket) }}">
                    {!! __('global.Edit') !!}
                </a></li>
            @role('super-admin')
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="{{ route('ticket.resend-confirmation', $ticket) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item"
                            onclick="return confirm('Renvoyer l\'email de confirmation ?')">
                            <i class="fa-regular fa-envelope me-2"></i>Renvoyer confirmation
                        </button>
                    </form>
                </li>
            @endrole
            <li>
                <hr class="dropdown-divider">
            </li>
        @endcan
        <li>
            <a class="dropdown-item" href="{{ route('ticket.index') }}">
                {!! __('global.Back') !!}
            </a>
        </li>
    </ul>
</div>
