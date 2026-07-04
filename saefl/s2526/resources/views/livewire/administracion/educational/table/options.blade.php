@php
    $class['iteration']="";
    $class['question_id']= "d-none d-lg-table-cell";
    $class['text']="d-none d-sm-table-cell";
    $class['observation']= "d-none d-lg-table-cell";
    $class['status_correct_response']= "d-none d-lg-table-cell";
    $class['attachment']="d-none d-sm-table-cell";
    $class['action']="";
    $table_id = 'table-data-default-competition';
    // name, description, motive, date, status_active, attachment
@endphp

<div class="text-muted font-weight-bolder">
    Listado de las opciones registradas para la pregunta seleccionada.
</div>

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['question_id'] ?? ''}}">{{$list_comment['question_id'] ?? ''}}</th>
            <th class="{{ $class['text'] ?? ''}}">{{$list_comment['text'] ?? ''}}</th>
            <th class="{{ $class['observation'] ?? ''}}">{{$list_comment['observation'] ?? ''}}</th>
            <th class="{{ $class['status_option_correct'] ?? ''}}">{{$list_comment['status_option_correct'] ?? ''}}</th>
            @admin
            <th class="{{ $class['attachment'] ?? ''}}">{{$list_comment['attachment'] ?? ''}}</th>
            @endadmin
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($options as $item)

            @php   
                $key = Str::random().'-'.$item->id;             
                $attachment_url = $item->attachment_url;
                $user = $item->user;
            @endphp

            <tr data-id="{{$item->id}}" class="{{($item->id == $option_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['question_id'] ?? ''}}"> {{$item->question_id ?? ''}} </td>
                <td class="{{ $class['text'] ?? ''}}"> 
                    {{$item->text ?? ''}} 
                    <div class="text-dark font-weight-light pl-2">Últ. revisión: <strong class="font-weight-bold text-uppercase small rounded-1 border table-light text-black p-1 m-1">{{$user->username ?? ''}}</strong></div>
                </td>
                <td class="{{ $class['observation'] ?? ''}}"> {{$item->observation ?? ''}} </td>
                <td class="{{ $class['status_option_correct'] ?? ''}}"> {{($item->status_option_correct) ? '-SI-':'-NO-' }} </td>
                @admin
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
                        @php $disabled = null @endphp
                        <button class="btn btn-warning btn-sm" wire:click="edit({{$item->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Editar"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>
                        
                        @php $disabled = ($item->user_id <> auth()->id()) ? 'disabled' : false; @endphp
                        {{-- @admin --}}
                        <button wire:click="option_delete({{$item->id}})" class="btn btn-danger btn-sm" wire:key="{{$key}}-btn-item-debate_delete-{{$item->id}}" {{ $disabled ?? null }} {{ $status ?? null }} {{ $status_date ?? null }} title="Eliminar"><i class="{{ $icon_menus['eliminar'] ?? ''}} fa-1x"></i></button>
                        {{-- @endadmin --}}
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


