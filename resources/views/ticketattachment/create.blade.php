@extends('layouts.app')

@section('title', __('ticketattachment.Add Attachment'))

@section('content')
    <div class="row">
        <div class="col-12 col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fa-regular fa-file-import me-2"></i>
                        {{ __('ticketattachment.Add Attachment') }}
                    </h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ticketattachment.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                        <input type="hidden" name="company_id" value="{{ $ticket->company_id }}">
                        <input type="hidden" name="uploaded_by" value="{{ auth()->id() }}">
                        <input type="hidden" name="status" value="active">

                        <div class="mb-5">
                            <h5>Support #{{ $ticket->id }}</h5>
                            <div class="alert alert-warning" role="alert">
                                <p><i class="fa-regular fa-triangle-exclamation me-2"></i> Vous êtes sur le point d'ajouter un fichier à ce support. Assurez-vous que le fichier est pertinent et respecte notre politique de confidentialité.</p>
                                <p class="fw-bold mb-0">Maximum {{ \App\Helpers\Helper::getMaxFileSizeFormatted() }} par fichier.</p>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="files" class="form-label">
                                {{ __('ticketattachment.fields.files') }} <span class="text-danger">*</span>
                            </label>
                            <input type="file" 
                                   class="form-control @error('files') is-invalid @enderror" 
                                   id="files" 
                                   name="files[]" 
                                   multiple 
                                   required>
                            @error('files')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fa-regular fa-info-circle me-1"></i>
                                Vous pouvez sélectionner plusieurs fichiers. Taille max : {{ \App\Helpers\Helper::getMaxFileSizeFormatted() }} par fichier.
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                {!! __('ticketattachment.btn.Upload') !!}
                            </button>
                            <a href="{{ route('ticket.show', $ticket) }}" class="btn btn-outline-primary">
                                {!! __('global.btn.Back') !!}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection