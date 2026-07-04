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
            <th class="{{ $class['text'] ?? ''}}">Texto</th>
            {{-- <th class="{{ $class['observation'] ?? ''}}">{{$list_comment['observation'] ?? ''}}</th> --}}
            <th class="{{ $class['status_option_correct'] ?? ''}}">Opción Correcta</th>
        </tr>
    </thead>

    <tbody id="tdatos-{{$table_id}}">

        @forelse($options as $item)

            <tr data-id="{{$item->id}}" class="">
                <td class="{{ $class['iteration'] ?? ''}}">{{$loop->iteration}}</td>
                <td class="{{ $class['text'] ?? ''}}">
                     {{$item->text ?? ''}} 
                     <div class="small text-muted">{{$item->observation ?? ''}}</div>
                </td>
                {{-- <td class="{{ $class['observation'] ?? ''}}"> {{$item->observation ?? ''}} </td> --}}
                <td class="{{ $class['status_option_correct'] ?? ''}}"> {{($item->status_option_correct) ? '-SI-':'-NO-' }} </td>
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


