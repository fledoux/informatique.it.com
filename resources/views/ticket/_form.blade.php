@csrf
<div class="row g-3">
        <div class="col-12 col-lg-4">
            <x-forms.select name="status" 
                            :label="__('ticket.fields.status')" 
                            :options="['new' => __('ticket.enum.status.new'), 'in_progress' => __('ticket.enum.status.in_progress'), 'waiting' => __('ticket.enum.status.waiting'), 'resolved' => __('ticket.enum.status.resolved'), 'closed' => __('ticket.enum.status.closed'), 'canceled' => __('ticket.enum.status.canceled')]"
                            :value="$ticket->status ?? 'new'" />
        </div>
        <div class="col-12 col-lg-4">
            <x-forms.select name="priority" 
                            :label="__('ticket.fields.priority')" 
                            :options="['low' => __('ticket.enum.priority.low'), 'normal' => __('ticket.enum.priority.normal'), 'high' => __('ticket.enum.priority.high'), 'urgent' => __('ticket.enum.priority.urgent')]"
                            :value="$ticket->priority ?? 'low'" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.relation name="company_id" 
                              :label="__('ticket.fields.company_id')" 
                              model="Company" 
                              display-field="name"
                              :value="$ticket->company_id ?? null" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.relation name="author_id" 
                              :label="__('ticket.fields.author_id')" 
                              model="User" 
                              display-field="name"
                              :value="$ticket->author_id ?? null" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="assigned_to" 
                           :label="__('ticket.fields.assigned_to')" 
                           type="text"
                           :value="old('assigned_to', $ticket->assigned_to ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="assigned_at" 
                           :label="__('ticket.fields.assigned_at')" 
                           type="datetime-local"
                           :value="old('assigned_at', $ticket->assigned_at ? ($ticket->assigned_at instanceof \Carbon\Carbon ? $ticket->assigned_at->format('Y-m-d\\TH:i') : $ticket->assigned_at) : '')" />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="due" 
                           :label="__('ticket.fields.due')" 
                           type="text"
                           :value="old('due', $ticket->due ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="folder_code" 
                           :label="__('ticket.fields.folder_code')" 
                           type="text"
                           :value="old('folder_code', $ticket->folder_code ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12 col-lg-6">
            <x-forms.input name="subject" 
                           :label="__('ticket.fields.subject')" 
                           type="text"
                           :value="old('subject', $ticket->subject ?? null)"
                           placeholder=""  />
        </div>
        <div class="col-12">
            <x-forms.input name="question" 
                           :label="__('ticket.fields.question')" 
                           type="textarea" 
                           :rows="4"
                           :value="old('question', $ticket->question ?? null)" />
        </div>
        <div class="col-12 col-lg-4">
            <x-forms.select name="billable" 
                            :label="__('ticket.fields.billable')" 
                            :options="['0' => 'Non', '1' => 'Oui']"
                            :value="old('billable', $ticket->billable ?? false) ? '1' : '0'" />
        </div>

</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{{ __('crud.Save') }}</button>
<a href="{{ route('ticket.index') }}" class="btn btn-outline-primary">{{ __('crud.Back') }}</a>
</div>