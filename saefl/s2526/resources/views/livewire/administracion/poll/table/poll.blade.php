@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['user_id']= ($modeIndex) ? "d-none d-lg-table-cell" : "d-none";
    $class['name']="d-none d-sm-table-cell";
    $class['description']= ($modeIndex) ? "d-none d-lg-table-cell" : "d-none";
    $class['poll_group_id']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['start_end']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['ffinal']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['observations']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['status_notifiled']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['status_test']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['tokens']="d-none d-md-table-cell";
    $class['participations']="d-none d-md-table-cell";
    $class['status_tet']="d-none d-md-table-cell";
    $class['action']="d-none d-sm-table-cell";
    $table_id = 'table-data-default-polls';

    //'poll_group_id','name','description','observations','finicial'.'time','ffinal'
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['poll_group_id'] ?? ''}}">{{$list_comment['poll_group_id'] ?? ''}}</th>
            <th class="{{ $class['user_id'] ?? ''}}">{{$list_comment['user_id'] ?? ''}}</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            {{-- <th class="{{ $class['observations'] ?? ''}}">{{$list_comment['observations'] ?? ''}}</th> --}}
            <th class="{{ $class['start_end'] ?? ''}}">{{$list_comment['start_end'] ?? ''}}</th>
            <th class="{{ $class['tokens'] ?? ''}}">{{$list_comment['tokens'] ?? ''}}</th>
            <th class="{{ $class['participations'] ?? ''}}">{{$list_comment['participations'] ?? ''}}</th>
            @admin
            <th class="{{ $class['status_notifiled'] ?? ''}}">{{$list_comment['status_notifiled'] ?? ''}}</th>
            <th class="{{ $class['status_test'] ?? ''}}">{{$list_comment['status_test'] ?? ''}}</th>
            @endadmin
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($polls as $poll)
            @php
                $key = 'key-'.$poll->id;
                $disabled = ( $poll->status_notifiled == "true") ? 'disabled' : 'false' ;
                $count_tokens = $poll->poll_tokens->count();
                $count_answer = ($poll->poll_answer_by_tokens->isNotEmpty()) ? $poll->poll_answer_by_tokens->count() : null;
                $participations = ($count_tokens) ? 100 * $count_answer / $count_tokens : null;
                $participations = round($participations,2);
                $status_notifiled = $poll->status_notifiled;
                $status_test = $poll->status_test;
                $attendees = $poll->attendees;
            @endphp

            <tr data-id="{{$poll->id}}" class="{{($poll->id == $poll_main_id) ? 'bg-secondary font-weight-bold text-light' : null}} {{ ($status_notifiled=="true") ? 'font-weight-bold text-success' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['poll_group_id'] ?? ''}}"> {{$poll->groupname ?? ''}} </td>
                <td class="{{ $class['user_id'] ?? ''}}">{{$poll->username}}</td>
                <td class="{{ $class['name'] ?? ''}}"> {{$poll->name ?? ''}} </td>
                <td class="{{ $class['description'] ?? ''}}" title="{{$poll->description ?? ''}}">{{Str::limit($poll->description,32,'...') ?? ''}}</td>
                <td class="{{ $class['start_end'] ?? ''}}">
                    <div>{{$poll->start->format('d-m-Y g:i A') ?? ''}}</div>
                    <div>{{$poll->end->format('d-m-Y g:i A') ?? ''}}</div>
                </td>
                <td class="{{ $class['tokens'] ?? ''}}"> {{ $count_tokens ?? ''}} <i>[{{$count_answer ?? '0'}}]</i></td>
                <td class="{{ $class['participations'] ?? ''}}"> {{ $participations ?? ''}} % [{{ $attendees->count() }}] </td>
                <td class="{{ $class['status_notifiled'] ?? ''}}"> {{ ($status_notifiled=="true") ? 'SI' : 'NO'}}</td>
                @admin
                <td class="{{ $class['status_test'] ?? ''}}"> {{ ($status_test=="true") ? 'SI' : 'NO'}}</td>
                @endadmin
                <td class="{{ $class['action'] ?? ''}}">
                    <div class="btn-group btn-group-sm" wire:key="{{$key}}-group-btn-{{$poll->id}}">
                        @php $disabled = ($poll->status_delete) ? ' disabled ' : null ; @endphp
                        <button class="btn btn-warning btn-sm" wire:click="edit({{$poll->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Editar"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>
                        <button wire:click="GenerateToken({{$poll->id}})" class="btn btn-info btn-sm" wire:key="table-btn-poll-generate-token--{{$poll->id}}" title="Generar token"><i class="{{ $icon_menus['sendmail'] ?? ''}} fa-1x"></i></button>
                        <button wire:click="preview({{$poll->id}})" class="btn btn-secondary btn-sm" wire:loading.attr="disabled" wire:key="{{$key}}-btn-poll-preview-{{$poll->id}}"><i class="{{ $icon_menus['eye'] ?? ''}} fa-1x" title="Vista previa"></i></button>
                        <button wire:click="previewSend({{$poll->id}})" class="btn btn-dark btn-sm" wire:loading.attr="disabled" wire:key="{{$key}}-btn-poll-previewSend{$poll->id}}"><i class="{{ $icon_menus['eye'] ?? ''}} fa-1x" title="Vista previa Participaciòn"></i></button>
                        <button wire:click="alertQuestion({{$poll->id}},'EmailForQueuing')" class="btn btn-success btn-sm" wire:key="{{$key}}-btn-poll-queuing-{{$poll->id}}" {{ $disabled ?? null }} {{ $status ?? null }} {{ $status_date ?? null }} title="Enviar tokens"><i class="{{ $icon_menus['sendmail'] ?? ''}} fa-1x"></i></button>
                        <button wire:click="show({{$poll->id}})" class="btn btn-primary btn-sm" wire:loading.attr="disabled" wire:key="{{$key}}-btn-poll-show-{{$poll->id}}"><i class="{{ $icon_menus['representante'] ?? ''}} fa-1x" title="Participantes"></i></button>
                        <button wire:click="results({{$poll->id}})" class="btn btn-dark btn-sm" wire:loading.attr="disabled" wire:key="{{$key}}-btn-poll-results"><i class="{{ $icon_menus['chartline'] ?? ''}} fa-1x" title="Resultados"></i></button>
                    </div>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="13" align="center">
                    <strong>No hay datos</strong>
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

{{-- @include('administracion.datatables.exportBootstrap') --}}
