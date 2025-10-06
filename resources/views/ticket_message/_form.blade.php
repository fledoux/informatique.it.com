<div class="row g-3">
        <div class="col-12 col-lg-6">
            <x-forms.input name="status" 
                           :label="__('ticket_message.fields.status')" 
                           type="text"
                           :value="old('status', $ticketMessage->status ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="subject" 
                           :label="__('ticket_message.fields.subject')" 
                           type="text"
                           :value="old('subject', $ticketMessage->subject ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12">
            <x-forms.input name="body" 
                           :label="__('ticket_message.fields.body')" 
                           type="textarea" 
                           :rows="4"
                           :value="old('body', $ticketMessage->body ?? null)" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.relation name="company_id" 
                              :label="__('ticket_message.fields.company_id')" 
                              model="Company" 
                              display-field="name"
                              :value="$ticketMessage->company_id ?? null" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.relation name="ticket_id" 
                              :label="__('ticket_message.fields.ticket_id')" 
                              model="Ticket" 
                              display-field="public_uuid"
                              :value="$ticketMessage->ticket_id ?? null" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.relation name="author_id" 
                              :label="__('ticket_message.fields.author_id')" 
                              model="User" 
                              display-field="name"
                              :value="$ticketMessage->author_id ?? null" />
        </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{{ __('global.Save') }}</button>
<a href="{{ route('ticket_message.index') }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
</div>