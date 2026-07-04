<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert alert-primary py-1 px-2 text-dark font-weight-bolder rounded">
            <button type="button" class="close" wire:click='close()'> <span aria-hidden="true">×</span> </button>
            <div class="pt-1">
                <b>Calendario de entrevistas del docente:</b>
                <div class="d-flex justify-content-between small">
                    <div>{{ $profesor->fullname }}</div>

                    <div class="text-muted small font-weight-bold">Desde {{$start->format('d-m-Y')}} hasta {{$end->format('d-m-Y')}} </div>
                </div>
            </div>
        </h5>

        <div class=" p-2 m-2">

            @include('livewire.bienestar.interview.mode.partials.calendar')

        </div>

    </div>

</div>


{{--

'estudiant_id', 'profesor_id', 'reason_id', 'type', 'description', 'observations', 'taken_actions', 'status_aggression', 'status_notify',
'date_notify_email', 'date_notify_agreement_email', 'status_notify_agreement', 'status_announcement', 'status_active', 'hour_announcement',
'date_announcement'

 --}}
