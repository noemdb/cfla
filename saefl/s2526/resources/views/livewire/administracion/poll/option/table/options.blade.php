@php
    $class['iteration']="d-none d-sm-table-cell";
    $class['poll_main_id']="d-none d-sm-table-cell";
    $class['poll_question_id']="d-none d-sm-table-cell";
    $class['description']= ($modeIndex) ? "d-none d-lg-table-cell" : "d-none";
    $class['text']= "d-none d-md-table-cell";
    $class['observations']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['body']= ($modeIndex) ? "d-none d-md-table-cell" : "d-none";
    $class['action']="d-none d-sm-table-cell";
    $table_id = 'table-data-default-options';

    //'poll_main_id','text','description','observations','body'
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id}}">
    <thead>
        <tr>
            <th class="{{ $class['iteration'] ?? ''}}">N</th>
            <th class="{{ $class['poll_main_id'] ?? ''}}">{{$list_comment['poll_main_id'] ?? ''}}</th>
            <th class="{{ $class['poll_question_id'] ?? ''}}">{{$list_comment['poll_question_id'] ?? ''}}</th>
            <th class="{{ $class['text'] ?? ''}}">{{$list_comment['text'] ?? ''}}</th>
            <th class="{{ $class['description'] ?? ''}}">{{$list_comment['description'] ?? ''}}</th>
            <th class="{{ $class['observations'] ?? ''}}">{{$list_comment['observations'] ?? ''}}</th>
            <th class="{{ $class['action'] ?? ''}}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($options as $option)
            @php
                $key = 'key-'.$option->id;
                $question = $option->poll_question; //dd($question);
                $poll = $question->poll_main;
            @endphp

            <tr data-id="{{$option->id}}" class="{{($option->id == $poll_option_id) ? 'bg-secondary font-weight-bold text-light' : null}}">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['poll_main_id'] ?? ''}}"> {{$poll->name ?? ''}}</td>
                <td class="{{ $class['poll_question_id'] ?? ''}}"> {{$question->text ?? ''}}</td>
                <td class="{{ $class['text'] ?? ''}}"> {{$option->text ?? ''}} </td>
                <td class="{{ $class['description'] ?? ''}}" title="{{$option->description ?? ''}}">{{Str::limit($option->description,32,'...') ?? ''}}</td>
                <td class="{{ $class['observations'] ?? ''}}" title="{{$option->observations ?? ''}}">{{Str::limit($option->observations,32,'...') ?? ''}}</td>
                <td class="{{ $class['action'] ?? ''}}">
                    <div class="btn-group btn-group-sm" wire:key="{{$key}}-group-btn-{{$option->id}}">
                        @php $disabled = ($option->status_delete) ? ' disabled ' : null ; @endphp
                        <button class="btn btn-warning btn-sm" wire:click="edit({{$option->id}})" {{ $disabled ?? null }} wire:loading.attr="disabled" title="Editar"><i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i></button>
                        {{-- <button wire:click="preview({{$option->id}})" class="btn btn-secondary btn-sm" wire:loading.attr="disabled" wire:key="{{$key}}-btn-option-preview-{{$option->id}}"><i class="{{ $icon_menus['eye'] ?? ''}} fa-1x" title="Vista previa"></i></button> --}}
                        <button wire:click="alertConfirm({{$option->id}})" class="btn btn-danger btn-sm" wire:key="{{$key}}-btn-option-delete-{{$option->id}}" {{ $disabled ?? null }}><i class="{{ $icon_menus['eliminar'] ?? ''}} fa-1x" title="Eliminar"></i></button>
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
