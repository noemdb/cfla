{{-- 'user_id','date','name','description','observations','icon','status_holidays' --}}
@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['user_id']="d-none d-sm-table-cell";
    $class['name']="d-none d-sm-table-cell";
    $class['date']="d-none d-sm-table-cell";
    $class['status_holidays']="d-none d-sm-table-cell";

    $class['description']= "d-none";
    $class['status']= "d-none";

    $class['action']="d-none d-sm-table-cell";

    $table_id = 'table-data-default-calendar_event';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['date'] ?? ''}}">{{$list_comment['date'] ?? ''}}</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['status_holidays'] ?? ''}}">{{$list_comment['status_holidays'] ?? ''}}</th>

            <th class="{{ $class['user_id'] ?? ''}}">{{$list_comment['user_id'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($calendar_events as $calendar_event)

            <tr data-id="{{$calendar_event->id}}" class="{{($calendar_event->id == $calendar_event_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['date'] ?? ''}}">{{$calendar_event->date}}</td>
                <td class="{{ $class['name'] ?? ''}}">{{$calendar_event->name}}</td>
                <td class="{{ $class['description'] ?? ''}}">{{$calendar_event->description}}</td>
                <td class="{{ $class['status_holidays'] ?? ''}}">{{($calendar_event->status_holidays) ? "SI" : "NO"}}</td>

                <td class="{{ $class['user_id'] ?? ''}}">{{$calendar_event->user->username}}</td>
                <td class="{{ $class['action'] ?? ''}}">
                    @php $key = $table_id.'_key_'; @endphp
                    @php $disabled = ($calendar_event->status_delete) ? ' disabled ' : null ; @endphp
                    <div class="btn-group btn-group-sm" wire:key="{{$key}}-group-btn-{{$calendar_event->id}}">
                        <button class="btn btn-warning btn-sm" wire:click="edit({{$calendar_event->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Editar"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>
                        <button wire:click="alertConfirm({{$calendar_event->id}})" class="btn btn-danger btn-sm" {{ $disabled ?? null }} wire:key="{{$key}}-btn-calendar_event-delete-{{$calendar_event->id}}" {{ $disabled ?? null }} {{ $status ?? null }}><i class="{{ $icon_menus['eliminar'] ?? ''}} fa-1x" title="Eliminar"></i></button>
                    </div>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="8" align="center">
                    <strong>No hay datos</strong>
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

{{ $calendar_events->links() }}

{{-- @include('administracion.datatables.exportBootstrap') --}}
