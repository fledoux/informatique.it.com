@extends('layouts.app')

@section('title', __('global.Details') . ' ' . __('ticket.entity'))

@section('content')
    <div class="row">
        <div class="col-12 col-lg-9">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-1 gap-sm-2 mb-4">
                <h1 class="h3 mb-0">
                    <i class="fa-regular fa-comments me-2"></i> Conversation - </span>Support n°{{ $ticket->id }}
                </h1>
            </div>

            <div class="card mb-4">
                <div class="card-body p-2 p-sm-3">

                    {{-- Boutons de réponse --}}
                    @auth
                        <div class="row mb-4 text-center">
                            <div class="col-12 d-flex flex-column justify-content-center flex-sm-row gap-2">
                                <a href="{{ route('ticketmessage.create', [$ticket->id]) }}" class="btn btn-orange">
                                    {!! __('ticket.Answer') !!}
                                </a>
                                <a href="{{ route('ticketattachment.create', [$ticket->id]) }}" class="btn btn-outline-orange">
                                    <i class="fa-regular fa-file-import me-1"></i>
                                    {!! __('ticketattachment.Add Attachment') !!}
                                </a>
                                @can('ticketmessage.create')
                                    @hasanyrole(['super-admin'])
                                        <a href="{{ route('ticketmessage.create', [$ticket->id, 'internal']) }}"
                                            class="btn btn-warning">
                                            {!! __('ticket.Note Interne') !!}
                                        </a>
                                        <a href="{{ route('ticket.merge.form', $ticket->id) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fa-regular fa-code-merge me-1"></i>
                                            Fusionner
                                        </a>
                                    @endhasanyrole
                                @endcan
                            </div>
                        </div>
                    @endauth

                    {{-- Messages de conversation (plus récents en haut) --}}
                    @foreach ($ticket->messages as $message)
                        @if (
                            $message->status === 'active' ||
                                (auth()->user() &&
                                    auth()->user()->hasRole('super-admin') &&
                                    ($message->status === 'internal' || $message->status === 'inactive')))
                            <div class="row mb-3 {{ $message->status === 'inactive' ? ' opacity-25' : '' }}">
                                <div
                                    class="col-12 col-sm-11 {{ $message->author?->company_id === 1 ? '' : 'offset-sm-1' }}">
                                    <div class="card border-1">
                                        <div
                                            class="card-header {{ $message->status === 'internal' ? 'bg-warning text-warning' : ($message->author_id === $ticket->author_id ? 'bg-success text-success' : 'bg-primary text-primary') }} bg-opacity-25">
                                            @hasanyrole(['super-admin'])
                                                <div class="float-end">
                                                    <a href="{{ route('ticketmessage.edit', $message) }}"
                                                        class="btn btn-link m-0 p-0" title="Modifier">
                                                        <i class="fa-regular fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('ticketmessage.destroy', $message) }}"
                                                        class="d-inline" onsubmit="return confirm('Supprimer ce message ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger m-0 p-0"
                                                            title="Supprimer">
                                                            <i class="fa-regular fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endhasanyrole
                                            @if ($message->status === 'internal')
                                                <i class="fa-regular fa-eye-slash text-warning ms-1"
                                                    title="Message interne"></i>
                                            @elseif ($message->status === 'inactive')
                                                <i class="fa-regular fa-eye-slash ms-1" title="Message inactif"></i>
                                            @endif
                                            <strong class="me-2">
                                                @hasanyrole(['super-admin'])
                                                    {{ $message->author->name ?? '' }}
                                                @else
                                                    {{ $message->author_id === $ticket->author_id ? $message->author->name : 'Support' }}
                                                @endhasanyrole
                                            </strong>
                                            <br class="none d-sm-none">
                                            <small>
                                                {{ $message->created_at->format('d/m/Y') }} -
                                                {{ $message->created_at->format('H\hi') }}
                                            </small>
                                        </div>
                                        <div class="card-body p-2 p-sm-3">
                                            @if ($message->subject)
                                                <strong>{{ $message->subject }}</strong><br>
                                            @endif
                                            {!! $message->body !!}

                                            {{-- Pièces jointes du message --}}
                                            @if ($message->attachments->count() > 0)
                                                <div class="mt-3 pt-3 border-top">
                                                    <small class="text-muted">
                                                        <i class="fa-regular fa-paperclip me-1"></i>
                                                        <strong>{{ \App\Helpers\Helper::pluralize($message->attachments->count(), 'global.attachment', 'global.attachments') }}
                                                            :</strong>
                                                    </small>
                                                    <div class="mt-2">
                                                        @foreach ($message->attachments as $attachment)
                                                            @php
                                                                $fileIcon = \App\Helpers\Helper::getFileIcon(
                                                                    $attachment->original_filename,
                                                                );
                                                            @endphp
                                                            <div class="d-inline-block me-1 mt-2">
                                                                <a href="{{ route('ticketattachment.download', $attachment->id) }}"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    title="{{ $attachment->original_filename }}">
                                                                    <i
                                                                        class="fa-regular {{ $fileIcon['icon'] }} {{ $fileIcon['color'] }} me-1"></i>
                                                                    {{ \Illuminate\Support\Str::limit($attachment->original_filename, 20) }}
                                                                    <small
                                                                        class="text-muted">({{ $attachment->getFormattedSize() }})</small>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Message d'accueil --}}
                    <div class="row mb-3">
                        <div class="col-12 col-sm-11">
                            <div class="card border-1">
                                <div class="card-header bg-primary text-primary bg-opacity-25">
                                    <strong class="me-2">
                                        Support
                                    </strong>
                                    <br class="none d-sm-none">
                                    <small>
                                        {{ $ticket->created_at->format('d/m/Y') }} -
                                        {{ $ticket->created_at->format('H\hi') }}
                                    </small>
                                </div>
                                <div class="card-body p-2 p-sm-3">
                                    <p>Nous avons bien reçu votre demande.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Message client original (en bas pour le contexte) --}}
                    <div class="row">
                        <div class="col-12 col-sm-11 offset-sm-1">
                            <div class="card border-1">
                                <div class="card-header bg-success text-success bg-opacity-25">
                                    @hasanyrole(['super-admin'])
                                        <div class="float-end">
                                            <a href="{{ route('ticket.edit', $ticket) }}" class="btn btn-link m-0 p-0"
                                                title="Modifier">
                                                <i class="fa-regular fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('ticket.destroy', $ticket) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('⚠️ ATTENTION ⚠️\n\nVous êtes sur le point de supprimer définitivement ce ticket ainsi que :\n- Tous les messages associés\n- Tous les fichiers joints (sur S3 et en base)\n\nCette action est IRRÉVERSIBLE.\n\nConfirmer la suppression ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger m-0 p-0"
                                                    title="Supprimer">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endhasanyrole
                                    <strong class="me-2">
                                        {{ $ticket->author->name ?? '' }}
                                    </strong>
                                    <br class="none d-sm-none">
                                    <small>
                                        {{ $ticket->created_at->format('d/m/Y') }} -
                                        {{ $ticket->created_at->format('H\hi') }}
                                    </small>
                                </div>
                                <div class="card-body p-2 p-sm-3">

                                    <strong>{{ $ticket->subject }}</strong><br>
                                    {!! $ticket->question !!}

                                    {{-- Pièces jointes de la demande initiale (sans message_id) --}}
                                    @if ($ticket->initialAttachments->count() > 0)
                                        @php
                                            $initialAttachments = $ticket->initialAttachments;
                                        @endphp
                                        <div class="mt-3 pt-3 border-top">
                                            <small class="text-muted">
                                                <i class="fa-regular fa-paperclip me-1"></i>
                                                <strong>{{ \App\Helpers\Helper::pluralize($initialAttachments->count(), 'global.attachment', 'global.attachments') }}
                                                    :</strong>
                                            </small>
                                            <div class="mt-2">
                                                @foreach ($initialAttachments as $attachment)
                                                    @php
                                                        $fileIcon = \App\Helpers\Helper::getFileIcon(
                                                            $attachment->original_filename,
                                                        );
                                                    @endphp
                                                    <div class="d-inline-block me-1 mt-2">
                                                        <a href="{{ route('ticketattachment.download', $attachment->id) }}"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            title="{{ $attachment->original_filename }}">
                                                            <i
                                                                class="fa-regular {{ $fileIcon['icon'] }} {{ $fileIcon['color'] }} me-1"></i>
                                                            {{ \Illuminate\Support\Str::limit($attachment->original_filename, 20) }}
                                                            <small
                                                                class="text-muted">({{ $attachment->getFormattedSize() }})</small>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>













        {{-- Sidebar avec infos du ticket --}}
        <div class="col-12 col-lg-3">
            <div class="card border border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-message-question"></i> Demande n°{{ $ticket->id }}</h3>
                </div>
                <div class="card-body p-2 p-sm-3 pb-0">
                    <span
                        class="badge mb-3 {{ __('ticket.statusBadgeColor.' . $ticket->status) }}">{{ __('ticket.status.' . $ticket->status) }}
                    </span>
                    <span
                        class="badge mb-3 {{ __('ticket.priorityBadgeColor.' . $ticket->priority) }}">{{ __('ticket.priorityFull.' . $ticket->priority) }}
                    </span>
                    <p>
                        <small>
                            {{ \App\Helpers\Helper::formatDateWithFrenchDay($ticket->created_at, 'l, j F Y à H\hi', true) }}.
                        </small>
                    </p>
                    <p>
                        <i class="fa-regular fa-square-check text-success"></i> {!! __('ticket.yes') !!},
                        {{ $user->name ?? '' }}
                        {!! __('ticket.cgv') !!}
                    </p>
                    <p>
                        <span class="h4">
                            {{ $company->name ?? '' }}
                        </span><br>
                        {{ $user->name ?? '' }}<br>
                        @hasanyrole(['super-admin'])
                            <small class="text-muted">{{ $ticket->author?->getRoleNames()->implode(', ') }}</small><br>
                        @endhasanyrole
                        {!! \App\Helpers\Helper::mailTo($user->email) !!}<br>
                        {!! $user->phone
                            ? '<a href="tel:' . $user->phone . '" class="text-orange text-decoration-none">' . $user->phone . '</a>'
                            : '' !!}
                    </p>
                    <p>
                        @if ($ticket->folder_code)
                            <span class="text-muted">{{ __('ticket.fields.folder_code') }}
                                : </span>{{ $ticket->folder_code ?? '' }}<br>
                        @endif
                        @if ($ticket->due)
                            <span class="text-muted">{{ __('ticket.fields.due') }} :</span>
                            <span>{{ \App\Helpers\Helper::formatDateWithFrenchDay($ticket->due, 'l j/m/y - H\hi', true) }}</span><br>
                        @endif
                        @if ($ticket->assignedTo?->name)
                            <span class="text-muted">
                                {{ __('ticket.fields.assigned_to') }} :
                            </span>
                            {{ $ticket->assignedTo?->name ?? '' }}<br>
                        @endif
                        @if ($ticket->assigned_at)
                            <span class="text-muted">
                                {{ __('ticket.fields.assigned_at') }} :
                            </span>
                            {{ $ticket->assigned_at ? ($ticket->assigned_at instanceof \Carbon\Carbon ? $ticket->assigned_at->format('d/m/y à H\hi') : $ticket->assigned_at) : '' }}<br>
                        @endif
                        <span class="{{ $ticket->billable ? 'text-muted' : 'text-danger' }}">
                            {{ __('ticket.fields.billable') }} :
                        </span>
                        {!! $ticket->billable ? __('ticket.billable.yes') : __('ticket.billable.no') !!}
                    </p>
                </div>
            </div>

            {{-- Facturation --}}
            <div class="card border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-money-bill-1"></i> Facturation</h3>
                </div>
                <div class="card-body p-2 p-sm-3 pb-0">
                    <p>
                        Estimation : <span class="float-end">2 tickets</span><br>
                        Facturation : <span class="float-end">1 ticket</span><br>
                        Majoration : <span class="float-end">0 ticket</span>
                    </p>
                </div>
                <div class="card-footer bg-secondary-subtle">
                    <span class="text-secondary">Solde au {{ now()->format('d/m/y') }} : <span
                            class="float-end">{{ \App\Helpers\Helper::pluralize(12, 'ticket.ticket', 'ticket.tickets') }}</span></span><br>
                </div>
            </div>

            {{-- Fichiers joints --}}
            <div class="card border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0">
                        <i class="fa-regular fa-file-import"></i>
                        {{ \App\Helpers\Helper::pluralize($attachmentsCount, 'global.attached_file', 'global.attached_files', true, true) }}
                    </h3>
                </div>
                <div class="card-body p-2 p-sm-3 pb-0">
                    @if ($attachmentsCount > 0)
                        <ul class="list-unstyled">
                            @foreach ($attachments as $attachment)
                                <li class="mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-2">
                                            @php
                                                $fileIcon = \App\Helpers\Helper::getFileIcon(
                                                    $attachment->original_filename,
                                                );
                                            @endphp
                                            <i class="fa-regular {{ $fileIcon['icon'] }} {{ $fileIcon['color'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <a href="{{ route('ticketattachment.download', $attachment) }}"
                                                class="text-decoration-none" title="Télécharger">
                                                {{ \Illuminate\Support\Str::limit($attachment->original_filename, 30) }}
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                {{ $attachment->getFormattedSize() }}
                                                @if ($attachment->uploaded_by)
                                                    • {{ $attachment->created_at->format('d/m') }}
                                                    @if ($attachment->uploaded_by === $ticket->author_id)
                                                        • {{ $attachment->uploader->name ?? 'Support' }}
                                                    @else
                                                        • Support
                                                    @endif
                                                @endif
                                            </small>
                                        </div>
                                        @hasanyrole(['super-admin'])
                                            <div class="ms-2">
                                                <form method="POST"
                                                    action="{{ route('ticketattachment.destroy', $attachment) }}"
                                                    class="d-inline" onsubmit="return confirm('Supprimer ce fichier ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0 m-0"
                                                        title="Supprimer">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endhasanyrole
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-3">
                            <i class="fa-regular fa-info-circle me-2"></i>
                            Aucun fichier joint pour ce ticket.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Approbation --}}
            <div class="card border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-thumbs-up"></i> Demande d'Approbation</h3>
                </div>
                <div class="card-body p-2 p-sm-3 pb-0">
                    @if ($managers->count() > 0)
                        Voici la liste des approbateurs actuels, que nous pouvons solliciter uniquement pour vos demandes
                        liées à la sécurité :
                        </p>
                        <ul class="small fa-ul">
                            @foreach ($managers as $manager)
                                <li class="mb-2">
                                    <span class="fa-li"><i class="fa-regular fa-dash"></i></span>
                                    <div>{!! $manager->name !!}</div>
                                    <div>{!! \App\Helpers\Helper::mailTo($manager->email) !!}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">
                            <i class="fa-regular fa-info-circle me-2"></i>
                            Aucun Manager assigné à cette société et/ou compte pour approbation.
                        </p>
                    @endif
                </div>
            </div>

            {{-- TeamViewer --}}
            <div class="card border border-secondary border-opacity-25 mb-4">
                <div class="card-header bg-secondary bg-opacity-75 text-white">
                    <h3 class="h6 mb-0"><i class="fa-regular fa-desktop"></i> {{ config('app.teamviewer_name') }}</h3>
                </div>
                <div class="card-body p-2 p-sm-3">
                    <p class="mb-2">
                        Si un technicien vous invite à accéder à votre poste, vous pouvez télécharger une version sécurisée
                        de
                        {{ config('app.teamviewer_name') }}.
                    </p>
                    <a href="{{ config('app.teamviewer_url') }}" target="_blank"
                        class="btn btn-outline-primary btn-sm w-100">
                        <i class="fa-regular fa-download me-2"></i>Télécharger {{ config('app.teamviewer_name') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
