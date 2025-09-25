<form>
    @csrf
    @hasanyrole('super-admin')
        <div class="row g-3  bg-warning bg-opacity-10 rounded-3 p-3 my-3">
            <div class="col-12 col-lg-4">
                <x-forms.select name="status" :label="__('ticket.fields.status')" :options="[
                    'new' => __('ticket.status.new'),
                    'in_progress' => __('ticket.status.in_progress'),
                    'waiting' => __('ticket.status.waiting'),
                    'resolved' => __('ticket.status.resolved'),
                    'closed' => __('ticket.status.closed'),
                    'canceled' => __('ticket.status.canceled'),
                ]" :value="$ticket->status ?? 'new'" />
            </div>
            <div class="col-12 col-lg-8">
                <x-forms.relation name="company_id" :label="__('ticket.fields.company_id')" model="Company" display-field="name" :value="$ticket->company_id ?? null" />
            </div>
            <div class="col-12 col-lg-6">
                <x-forms.relation name="author_id" :label="__('ticket.fields.author_id')" model="User" display-field="name" :value="$ticket->author_id ?? null" />
            </div>

            <div class="col-12 col-lg-6">
                <x-forms.select name="assigned_to" :label="__('ticket.fields.assigned_to')" :options="App\Models\Ticket::getAssignedToOptions()" :value="$ticket->assigned_to ?? null" />
            </div>
            <div class="col-12 col-lg-6">
                <x-forms.input name="assigned_at" :label="__('ticket.fields.assigned_at')" type="datetime-local" :value="old(
                    'assigned_at',
                    $ticket->assigned_at
                        ? ($ticket->assigned_at instanceof \Carbon\Carbon
                            ? $ticket->assigned_at->format('Y-m-d\\TH:i')
                            : $ticket->assigned_at)
                        : '',
                )" />
            </div>
            <div class="col-12 col-lg-6">
                <x-forms.input name="due" :label="__('ticket.fields.due')" type="datetime-local" :value="old(
                    'due',
                    $ticket->due
                        ? ($ticket->due instanceof \Carbon\Carbon
                            ? $ticket->due->format('Y-m-d\\TH:i')
                            : $ticket->due)
                        : '',
                )" />
            </div>
            <div class="col-12 col-lg-4">
                <x-forms.switch name="billable" :label="__('ticket.fields.billable')" :checked="old('billable', $ticket->billable ?? true)" />
            </div>
        </div>
    @endhasanyrole

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <x-forms.select name="priority" :label="__('ticket.fields.priority')" :required="true" :options="__('ticket.priority')"
                :value="$ticket->priority ?? ''" />
        </div>
        <div class="col-12 col-lg-4">
            <x-forms.input name="folder_code" :label="__('ticket.fields.folder_code')" type="text" :value="old('folder_code', $ticket->folder_code ?? null)" placeholder="" />
        </div>
        <div class="col-12 col-lg-12">
            <x-forms.input name="subject" :label="__('ticket.fields.subject')" type="text" :required="true" :value="old('subject', $ticket->subject ?? null)"
                placeholder="" />
        </div>
        <div class="col-12">
            <x-forms.input name="question" :label="__('ticket.fields.question')" type="textarea" :rows="4" :required="true"
                :value="old('question', $ticket->question ?? null)" />
        </div>
    </div>
    <div class="btn-group mt-3" role="group" aria-label="Basic example">
        <button type="submit" class="btn btn-primary">{{ __('global.Save') }}</button>
        <a href="{{ route('ticket.index') }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
    </div>
</form>
