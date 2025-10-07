<div class="row g-3">
    <div class="col-12 col-lg-6">
        <x-forms.input name="subject" :label="__('ticketmessage.fields.subject')" type="text" :value="old('subject', $ticketMessage->subject ?? null)" placeholder="" />
    </div>
    <div class="col-12 col-lg-6">
        <label for="status" class="form-label">{{ __('ticketmessage.fields.status') }}</label>
        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
            <option value="active" {{ old('status', $ticketMessage->status ?? null) == 'active' ? 'selected' : '' }}>{{ __('ticketmessage.status.active') }}</option>
            <option value="inactive" {{ old('status', $ticketMessage->status ?? null) == 'inactive' ? 'selected' : '' }}>{{ __('ticketmessage.status.inactive') }}</option>
            <option value="internal" {{ old('status', $ticketMessage->status ?? null) == 'internal' ? 'selected' : '' }}>{{ __('ticketmessage.status.internal') }}</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <x-forms.input name="body" :label="__('ticketmessage.fields.body')" type="textarea" :rows="6" :value="old('body', $ticketMessage->body ?? null)" />
    </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
    <button type="submit" class="btn btn-primary">{!! __('global.btn.Save') !!}</button>
    <a href="{{ route('ticket.show', $ticketMessage->ticket_id) }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
</div>
