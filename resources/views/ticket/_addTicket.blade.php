<div class="dropdown w-100">
    <button class="btn btn-orange dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-bars me-2"></i>Actions
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="{{ route('ticket.edit', $ticket) }}">
            {!! __('ticket.Answer') !!}
        </a></li>
        @can('ticket.edit')
        <li><a class="dropdown-item" href="{{ route('ticket.edit', $ticket) }}">
            {!! __('global.Edit') !!}
        </a></li>
        <li><hr class="dropdown-divider"></li>
        @endcan
        <li><a class="dropdown-item" href="{{ route('ticket.index') }}">
            {!! __('global.Back') !!}
        </a></li>
    </ul>
</div>
