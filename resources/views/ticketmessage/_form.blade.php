<div class="row g-3">
    <div class="col-12 col-lg-6">
        <x-forms.input name="subject" :label="__('ticketmessage.fields.subject')" type="text" :value="old('subject', $ticketMessage->subject ?? null)" placeholder="" />
    </div>
    <div class="col-12 col-lg-6">
        <x-forms.select 
            name="status" 
            :label="__('ticketmessage.fields.status')" 
            :value="old('status', $ticketMessage->status ?? 'active')"
            :options="__('ticketmessage.status')"
            :required="true" />
    </div>
    <div class="col-12">
        <x-forms.textarea name="body" :label="__('ticketmessage.fields.body')" type="textarea" :rows="6" :value="old('body', $ticketMessage->body ?? null)" />
    </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
    <button type="submit" class="btn btn-primary">{!! __('global.btn.Save') !!}</button>
    <a href="{{ route('ticket.show', $ticketMessage->ticket_id) }}" class="btn btn-outline-primary">{!! __('global.btn.Back') !!}</a>
</div>
