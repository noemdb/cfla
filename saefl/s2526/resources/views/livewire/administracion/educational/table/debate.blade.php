@php
    $class['iteration']="";
    $class['competition_id']= "d-none d-lg-table-cell";
    $class['grado_id']="d-none d-sm-table-cell";
    $class['seccion_id']= "d-none d-lg-table-cell";
    $class['name']= "d-none d-lg-table-cell";
    $class['token']= "d-none d-lg-table-cell";
    $class['description']= "d-none d-lg-table-cell";
    $class['status_active']= "d-none d-lg-table-cell";
    $class['winner_section_id']="d-none d-sm-table-cell";
    $class['attachment']="d-none d-sm-table-cell";
    $class['action']="";
    $table_id = 'table-data-default-competition';
    // name, description, motive, date, status_active, attachment
@endphp

<div class="text-muted font-weight-bolder">
    Listado de los debates registrados para la Competicióna seleccionada.
</div>


<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            {{-- <th class="{{ $class['competition_id'] ?? ''}}">{{$list_comment['competition_id'] ?? ''}}</th> --}}
            <th class="{{ $class['name'] ?? ''}}">{{$list_comment['name'] ?? ''}}</th>
            <th class="{{ $class['token'] ?? ''}}">{{$list_comment['token'] ?? ''}}</th>
            <th class="{{ $class['grado_id'] ?? ''}}">{{$list_comment['grado_full'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['status_active'] ?? ''}}">{{$list_comment['status_active'] ?? ''}}</th>
            <th class="{{ $class['winner_section_id'] ?? ''}}">{{$list_comment['winner_section_id'] ?? ''}}</th>
            <th class="{{ $class['attachment'] ?? ''}}">{{$list_comment['attachment'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($debates as $item)

            @php   
                $key = Str::random().'-'.$item->id;             
                $attachment_url = $item->attachment_url;
                $competition = $item->competition;
                $grado = $item->grado;
                $seccion = $item->seccion;
            @endphp

            <tr data-id="{{$item->id}}" class="{{($item->id == $debate_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                {{-- <td class="{{ $class['competition_id'] ?? ''}}"> {{$competition->name ?? ''}} </td> --}}
                <td class="{{ $class['name'] ?? ''}}"> {{$item->name ?? ''}}</td>
                <td class="{{ $class['name'] ?? ''}}"> {{$item->UrlToken ?? ''}}</td>
                <td class="{{ $class['grado_id'] ?? ''}}"> {{$grado->name ?? ''}} {{$seccion->name ?? ''}} </td>
                <td class="{{ $class['description'] ?? ''}}"> {{$item->description ?? ''}} </td>
                <td class="{{ $class['status_active'] ?? ''}}"> {{($item->status_active) ? '-SI-':'-NO-' }} </td>
                <td class="{{ $class['winner_section_id'] ?? ''}}"> {{$item->winner_section_id ?? ''}} </td>
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
                        <button class="btn btn-warning btn-sm" wire:click="edit({{$item->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Editar"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>
                        <button class="btn btn-info btn-sm" wire:click="setModeQuestions({{$item->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Preguntas"><i class="{{ $icon_menus['nuevo'] ?? ''}} fa-1x"></i></button>
                        <button wire:click="alertQuestion({{$item->id}},'debate_delete')" class="btn btn-danger btn-sm" wire:key="{{$key}}-btn-item-debate_delete-{{$item->id}}" {{ $disabled ?? null }} {{ $status ?? null }} {{ $status_date ?? null }} title="Eliminar"><i class="{{ $icon_menus['eliminar'] ?? ''}} fa-1x"></i></button>
                    </div>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="10" align="center">
                    <strong>No hay datos</strong>
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

{{-- @include('administracion.datatables.exportBootstrap') --}}


