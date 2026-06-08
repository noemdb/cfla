@php ($class_N="")
@php ($class_codigo="d-none d-lg-table-cell")
{{-- @php ($class_tipo="d-none d-xl-table-cell") --}}
{{-- @php ($class_evento="d-none d-xl-table-cell") --}}
@php ($class_finicial="d-none d-xl-table-cell")
@php ($class_ffinal="d-none d-xl-table-cell")
@php ($class_username="d-none d-xl-table-cell")
@php ($class_created_at="d-none d-md-table-cell")
@php ($class_updated_at="d-none d-lg-table-cell")
@php ($class_action="nosort")

<table width="100%" class="table {{-- table-striped table-hover --}} table-sm" id="table-data-tasks">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th>Usuario</th>
            <th class="{{ $class_codigo ?? ''}}">Código</th>
            <th class="">Descripción</th>
            {{-- <th class="{{ $class_tipo ?? ''}}">Tipo</th> --}}
            {{-- <th class="{{ $class_evento ?? ''}}">Evento</th> --}}
            <th class="">Estado</th>
            <th class="{{ $class_finicial ?? ''}}">F.Inicial</th>
            <th class="{{ $class_ffinal ?? '' }}">F.Final</th>
            <th align="right" class="{{$class_action}}"><strong>Aciones</strong></th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach($tasks as $task)

            @php ($user = $task->user)

            <tr data-task="{{$task->id}}" data-user="{{$user->id ?? ''}}" class="text-{{ $task->tipo ?? '' }} p-0 m-0">

                <td class="{{$class_N}}">
                    {{ ($loop->index + 1) }}
                </td>

                <td id="td-users-username-{{ $user->id ?? '' }}">
                    <span class="text-users-username-{{ $user->id ?? '' }} text-{{ $user->is_active ?? '' }}">
                        {{$user->username ?? ''}}
                    </span>
                </td>

                <td  id="td-tasks-codigo-{{$task->id ?? ''}}" title="{{ $task->codigo ?? ''}} " class="{{ $class_codigo ?? ''}}">
                    {{$task->codigo ?? ''}}
                </td>

                <td  id="td-tasks-descripcion-{{$task->id ?? ''}}" title="{{ $task->descripcion ?? ''}} ">
                    {{$task->truncdescripcion}}
                </td>

                {{-- <td  id="td-tasks-tipo-{{$task->id ?? ''}}" title="{{ $task->tipo ?? ''}}" class="{{ $class_tipo ?? ''}}" > --}}
                    {{-- {{$task->tipo ?? ''}} --}}
                {{-- </td> --}}

                {{-- <td  id="td-tasks-evento-{{$task->id ?? ''}}" title="{{ $task->evento ?? ''}} " class="{{ $class_evento ?? ''}}"> --}}
                    {{-- {{$task->evento ?? ''}} --}}
                {{-- </td> --}}

                <td  id="td-tasks-estado-{{$task->id ?? ''}}" title="{{ $task->estado ?? ''}}"  class="text-uppercase">
                    {{$task->estado ?? ''}}
                </td>

                <td id="td-tasks-finicial-{{ $task->id ?? ''}}" class="{{ $class_finicial ?? ''}}">
                    {{ (isset($task->finicial)) ? Carbon\Carbon::parse($task->finicial)->format('d-m-Y') : '' }}
                </td>

                <td id="td-tasks-ffinal-{{ $task->id ?? ''}}" class="{{ $class_ffinal ?? ''}}">
                    {{ (isset($task->ffinal)) ? Carbon\Carbon::parse($task->ffinal)->format('d-m-Y') : '' }}
                </td>

                <td style="padding: 2px; vertical-align: middle;" id="btn-action-{{ $task->id }}" class="text-center">

                    <div class="btn-group btn-group-sm">

                        {{-- boton para mostrar en un modal de info de regsitro --}}

                        <a title="Mostrar detalles" class="btn btn-info btn-xs" href="{{ route('tasks.show',$task->id) }}">
                            <i class="fas fa-info"></i>
                        </a>

                        <a title="Editar resgistro" class="btn btn-warning btn-xs btn-action-group-{{ $task->id }}" href="{{ route('tasks.edit',$task->id) }}" id="btn-edituser_{{$task->id}}">
                            <i class="fas fa-pencil-alt"></i>
                        </a>

                        <a title="Eliminar {{(isset($task->deleted_at) ? 'DEFINITIVAMENTE':'')}}" class="btn-delete btn btn-danger btn-xs" href="{{ route('tasks.destroy',$task->id) }}" id="btn-delete-taskid_{{$task->id}}">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>

                </td>


            </tr>
        @endforeach
    </tbody>
</table>

@section('stylesheet')
    @parent

    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">

@endsection

@section('scripts')
    @parent

    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js") }}"></script>

   <script type="text/javascript" class="init">

        $(document).ready(function() {
            $('#table-data-tasks').DataTable( {
                "pageLength": 10,
                "responsive": false,
                // "searching": false,
                "columnDefs": [ {
                    "targets": 'nosort',
                    "orderable": false
                } ],
                "language": {
                    "url": "{{ asset("vendor/datatables/lang/spanish.json") }}"
                },
                // "dom": '<"top"ifl>rt<"bottom"p><"clear">',
            } );
        } );

   </script>

@endsection