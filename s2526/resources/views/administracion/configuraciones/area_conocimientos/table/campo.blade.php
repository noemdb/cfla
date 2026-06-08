@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-campo">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_asignatura }}">Área de Conocimiento</th>
            <th class="{{ $class_code_sm }}">Asignatura</th>
            <th class="{{ $class_code_sm }}">Observaciones</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($campo_conocimientos as $campo_conocimiento)

        <tr data-id="{{$campo_conocimiento->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_asignatura  ?? ''}}">
                {{$campo_conocimiento->area_conocimiento->name ?? ''}}
            </td>
            <td class="{{ $class_asignatura  ?? ''}}">
                {{$campo_conocimiento->asignatura->fullname ?? ''}}
            </td>
            <td class="{{ $class_code_sm  ?? ''}}">
                {{$campo_conocimiento->observations ?? ''}}
            </td>
            <td class="{{ $class_action ?? '' }}">
                <div class="btn-group btn-group-sm">

                    @php $id_modal = 'modal_edit_campo_conocimiento_'.$campo_conocimiento->id; @endphp
                    <a title="Editar" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}" href="#" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>
                    @include('administracion.configuraciones.area_conocimientos.modals.campo_conocimientos.edit')

                    <a title="Eliminar" class="btn-destroy-campo btn btn-danger btn-sm" href="#" id="btn-destroy_id_{{$campo_conocimiento->id}}">
                        <i class="fas fa-trash"></i>
                    </a>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- </div> --}}

{!! Form::open(['route' => ['administracion.configuraciones.campo_conocimientos.destroy',':CAMPO_CONOCIMIENTO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy-campo', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/campo_conocimientos/destroy.js") }}"></script>
@endsection

@include('administracion.datatables.custom')

@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-campo').DataTable( {
                "pagingType": "full_numbers",
                "pageLength": 10,
                "bLengthChange": false,
                "bPaginate": true,
                "searching": true,
                "bInfo" : false,
                "responsive": false,
                "columnDefs": [ {
                    "targets": 'nosort',
                    "orderable": false
                } ],
                "language": {
                    "url": "/vendor/datatables/lang/spanish.json"
                }
            } );
            $.fn.DataTable.ext.pager.numbers_length = 5;
        } );
    </script>
@endsection
