@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['name']= ($mode=="index") ? "d-none d-lg-table-cell" : "d-none";
    $class['description']="d-none d-sm-table-cell";
    $class['motive']= ($mode=="index") ? "d-none d-lg-table-cell" : "d-none";
    $class['date']= ($mode=="index") ? "d-none d-md-table-cell" : "d-none";
    $class['status_active']= ($mode=="index") ? "d-none d-md-table-cell" : "d-none";
    $class['attachment']= ($mode=="index") ? "d-none d-md-table-cell" : "d-none";
    $class['action']="d-none d-sm-table-cell";
    $table_id = 'table-data-default-competition';
    // name, description, motive, date, status_active, attachment
@endphp


<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['motive'] ?? ''}}">{{$list_comment['motive'] ?? ''}}</th>
            <th class="{{ $class['date'] ?? ''}}">{{$list_comment['date'] ?? ''}}</th>
            <th class="{{ $class['status_active'] ?? ''}}">{{$list_comment['status_active'] ?? ''}}</th>
            <th class="{{ $class['attachment'] ?? ''}}">{{$list_comment['attachment'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($debate_competitions as $item)

            @php
                $key = Str::random().'-'.$item->id;
                $attachment_url = $item->attachment_url;
            @endphp

            <tr data-id="{{$item->id}}" class="{{($item->id == $competition_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['name'] ?? ''}}"> {{$item->name ?? ''}} <span class="text-dark d-block">Token: <span class="font-weight-bold">{{$item->token}}</span></span> </td>
                <td class="{{ $class['description'] ?? ''}}"> {{$item->description ?? ''}} </td>
                <td class="{{ $class['motive'] ?? ''}}"> {{$item->motive ?? ''}} </td>
                <td class="{{ $class['date'] ?? ''}}"> {{$item->date ?? ''}} </td>
                <td class="{{ $class['status_active'] ?? ''}}"> {{($item->status_active) ? '-SI-':'-NO-' }} </td>
                <td class="{{ $class['attachment'] ?? ''}}">
                    @if ($attachment_url)
                        <center>
                            <div class="">
                                <div class="text-muted">Vista previa:</div>
                                <div class="card" style="width: 4rem;">
                                    <img src="{{ asset($attachment_url) }}" class="card-img-top" alt="...">
                                </div>
                            </div>
                        </center>
                    @endif
                </td>
                <td class="{{ $class['action'] ?? ''}}">
                    <div class="btn-group btn-group-sm" wire:key="{{$key}}-group-btn-{{$item->id}}">
                        {{-- @php $disabled = ($item->status_delete) ? ' disabled ' : null ; @endphp --}}
                        @php $disabled = ($item->status_delete) ? null : null ; @endphp
                        <button class="btn btn-warning btn-sm" wire:click="edit({{$item->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Editar"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>
                        <button class="btn btn-info btn-sm" wire:click="setModeDebate({{$item->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Debate"><i class="{{ $icon_menus['nuevo'] ?? ''}} fa-1x"></i></button>
                        <button wire:click="alertQuestion({{$item->id}},'remove')" class="btn btn-danger btn-sm" wire:key="{{$key}}-btn-item-queuing-{{$item->id}}" {{ $disabled ?? null }} {{ $status ?? null }} {{ $status_date ?? null }} title="Eliminar"><i class="{{ $icon_menus['eliminar'] ?? ''}} fa-1x"></i></button>
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

{{-- @include('administracion.datatables.exportBootstrap') --}}


