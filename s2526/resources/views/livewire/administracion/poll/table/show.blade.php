<div class="alert alert-info" role="alert">
    <button type="button" class="close" wire:click='showModeIndex()'>
        <span aria-hidden="true">×</span>
    </button>
    <strong>Participantes del Proceso de Consulta. </strong> {{ $poll_main->name ?? null }}
    {{-- <hr class="p-0 m-0"> --}}
    <div class="text-muted pl-4"><i>{{ $poll_main->description ?? null }}<i></div>
</div>


@php
    //'user_id','ci_attendee','name','phone','cellphone','pmovilphone','email','gsemail','status_active','status_blacklist'
    $class['iteration']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['ci_attendee']="d-none d-sm-table-cell";
    $class['email']="d-none d-sm-table-cell";
    $class['status_notifiled']="d-none d-sm-table-cell text-right";
    $class['action']="d-none d-sm-table-cell";
    $table_id = 'table-data-default-show';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment_attendee['name'] ?? ''}}</th>
            {{-- <th class="{{ $class['ci_attendee'] ?? ''}}">{{$list_comment_attendee['ci_attendee'] ?? ''}}</th> --}}
            <th class="{{ $class['email'] ?? ''}}">{{$list_comment_attendee['email'] ?? ''}}</th>
            <th class="{{ $class['status_notifiled'] ?? ''}}">{{$list_comment['status_notifiled'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($attendees as $attendee)

            <tr class="">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['name'] ?? ''}}">

                    @if($attendee->IsRepresentant())
                        <div>{{ $attendee->representant->name ?? null }}</div>
                    @else
                        <div>{{$attendee->fullname ?? ''}}</div>
                    @endif
                    {{-- {{$attendee->fullname ?? ''}} --}}
                    <span class="text-small text-muted">CI: {{$attendee->ci ?? ''}}</span>
                </td>
                <td class="{{ $class['email'] ?? ''}}">
                    {{$attendee->email ?? ''}}
                </td>
                <td class="{{ $class['status_notifiled'] ?? ''}}">{{($attendee->statusNotifiledPollToken($poll_main->id)) ? '-SI-' : '-NO-'}}</td>
                <td class="{{ $class['action'] ?? ''}}">
                    @php
                        $poll_answers = $attendee->getPollAnswers($poll_main->id);
                        $btnDisabled = ($poll_answers->count()) ? null : 'disabled' ;
                        // $btnDisabled = ($poll_answers->count()) ? null : null ;
                    @endphp
                    <button wire:click="EmailForQueuingIndividual({{$poll_main->id}},{{$attendee->id}})" wire:loading.attr="disabled" class="btn btn-success btn-sm" wire:key="table-btn-poll_main-queuing-individual-{{$poll_main->id}}-{{$attendee->id}}" title="Enviar notificación individual"><i class="{{ $icon_menus['mail'] ?? ''}} fa-1x"></i></button>
                    <button {{$btnDisabled}} wire:click="EmailForTicketIndividual({{$poll_main->id}},{{$attendee->id}})" wire:loading.attr="disabled" class="btn btn-danger btn-sm" wire:key="table-btn-poll_main-queuing-ticket-individual-{{$poll_main->id}}-{{$attendee->id}}" title="Enviar ticket de participación individual"><i class="{{ $icon_menus['mail'] ?? ''}} fa-1x"></i></button>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="4" align="center">
                    <strong>No hay datos</strong>
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

{{-- @include('academicos.datatables.default') --}}
{{-- @include('administracion.datatables.exportBootstrap') --}}

