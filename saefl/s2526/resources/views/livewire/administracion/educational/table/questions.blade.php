@php
    $class['iteration']="";
    $class['debate_id']= "d-none d-lg-table-cell";
    $class['category']="d-none d-sm-table-cell";
    $class['text']= "d-none d-lg-table-cell";
    $class['time']= "d-none d-lg-table-cell";
    $class['weighting']= "d-none d-lg-table-cell";
    $class['observation']= "d-none d-lg-table-cell";
    $class['pensum_id']= "d-none d-lg-table-cell";
    $class['status_active']= "d-none d-lg-table-cell";
    $class['attachment']="d-none d-sm-table-cell";
    $class['action']="";
    $table_id = 'table-data-default-competition';
    // name, description, motive, date, status_active, attachment
@endphp

<div class="text-muted font-weight-bolder">
    Listado de las preguntas registradas para el debate seleccionado.
</div>

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            {{-- <th class="{{ $class['debate_id'] ?? ''}}">{{$list_comment['debate_id'] ?? ''}}</th> --}}
            <th class="{{ $class['category'] ?? ''}}">Nivel/Categoría</th>
            <th class="{{ $class['text'] ?? ''}}">{{$list_comment['text'] ?? ''}}</th>
            <th class="{{ $class['time'] ?? ''}}">{{$list_comment['time'] ?? ''}}</th>
            <th class="{{ $class['weighting'] ?? ''}}">{{$list_comment['weighting'] ?? ''}}</th>
            {{-- <th class="{{ $class['observation'] ?? ''}}">{{$list_comment['observation'] ?? ''}}</th> --}}
            @admin
            <th class="{{ $class['status_active'] ?? ''}}">{{$list_comment['status_active'] ?? ''}}</th>
            <th class="{{ $class['attachment'] ?? ''}}">{{$list_comment['attachment'] ?? ''}}</th>
            @endadmin
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($questions as $item)

            @php   
                $key = Str::random().'-'.$item->id;  
                $debate = $item->debate;           
                $pensum = $item->pensum;           
                $attachment_url = $item->attachment_url;
            @endphp

            <tr data-id="{{$item->id}}" class="{{($item->id == $question_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['category'] ?? ''}} font-weight-bold">
                    <div class="">{{$pensum->fullname ?? ''}}</div>
                    <div class="text-success">{{$item->category ?? ''}}</div>
                </td>
                <td class="{{ $class['text'] ?? ''}}"> 
                    {{$item->text ?? ''}} 
                    <div class="small text-muted">{{$item->observation ?? ''}}</div>
                </td>
                <td class="{{ $class['time'] ?? ''}}"> {{$item->time ?? ''}} </td>
                <td class="{{ $class['weighting'] ?? ''}}"> {{$item->weighting ?? ''}} </td>
                {{-- <td class="{{ $class['observation'] ?? ''}}"> {{$item->observation ?? ''}} </td> --}}
                @admin
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
                @endadmin
                <td class="{{ $class['action'] ?? ''}}">
                    <div class="btn-group btn-group-sm" wire:key="{{$key}}-group-btn-{{$item->id}}">
                        {{-- @php $disabled = ($item->status_delete) ? ' disabled ' : null ; @endphp --}}
                        <button class="btn btn-warning btn-sm" wire:click="edit({{$item->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Editar"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>
                        <button class="btn btn-info btn-sm" wire:click="setModeOptions({{$item->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Opciones"><i class="{{ $icon_menus['nuevo'] ?? ''}} fa-1x"></i></button>
                        <button wire:click="alertQuestion({{$item->id}},'question_delete')" class="btn btn-danger btn-sm" wire:key="{{$key}}-btn-item-debate_delete-{{$item->id}}" {{ $disabled ?? null }} {{ $status ?? null }} {{ $status_date ?? null }} title="Eliminar"><i class="{{ $icon_menus['eliminar'] ?? ''}} fa-1x"></i></button>
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


