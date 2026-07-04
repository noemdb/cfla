@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-area">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_asignatura }}">Plan Estudio</th>
            <th class="{{ $class_code_sm }}">Nombre</th>
            <th class="{{ $class_code_sm }}">Código</th>
            <th class="{{ $class_ht }}">Descripción</th>
            <th class="{{ $class_ht }}" title="Tomada en cuenta para índice o promedio académico">I. Académico</th>
            <th class="{{ $class_ht }}" title="Número de Asignaturas adscritas">N. Asignaturas</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($area_conocimientos as $area_conocimiento)

        <tr data-id="{{$area_conocimiento->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_asignatura  ?? ''}}">
                {{$area_conocimiento->pestudio->name ?? ''}}
            </td>
            <td class="{{ $class_code_sm  ?? ''}}">
                {{$area_conocimiento->name ?? ''}}
                <div>{{$area_conocimiento->leader->username ?? ''}}</div>
            </td>
            <td class="{{ $class_code_sm  ?? ''}}">
                {{$area_conocimiento->code ?? ''}}
            </td>
            <td class="{{ $class_ht  ?? ''}}">
                {{$area_conocimiento->description ?? ''}}
            </td>
            <td class="{{ $class_ht  ?? ''}}">
                {{($area_conocimiento->enable_academic_index=='true') ? 'SI':'NO'}}
            </td>
            <td class="{{ $class_ht  ?? ''}}">
                {{$area_conocimiento->campo_conocimientos->count() ?? ''}}
            </td>
            <td class="{{ $class_action ?? '' }} nosort">
                <div class="btn-group btn-group-sm">

                    @php $id_modal = 'modal_carga_area_conocimiento_'.$area_conocimiento->id; @endphp
                    <a title="Asignar área de formación al Área de Conocimiento" class="btn btn-success" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                        <i class="{{ $icon_menus['carga'] ?? ''}}" aria-hidden="true"></i>
                    </a>
                    @include('administracion.configuraciones.area_conocimientos.modals.create_campo')
                    {{-- @includeIf(!empty($area_conocimiento),'administracion.configuraciones.area_conocimientos.modals.create_campo') --}}

                    @php $id_modal = 'modal_show_area_conocimiento_'.$area_conocimiento->id; @endphp
                    <a title="{{$title ?? ''}}" class="btn btn-info" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                        <i class="{{ $icon_menus['show'] ?? ''}}" aria-hidden="true"></i>
                    </a>
                    @include('administracion.configuraciones.area_conocimientos.modals.details')

                    @php $id_modal = 'modal_edit_area_conocimiento_'.$area_conocimiento->id; @endphp
                    <a title="Editar" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}" href="#" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>
                    @include('administracion.configuraciones.area_conocimientos.modals.edit')

                    @php $disabled = ($area_conocimiento->status_delete) ? null:'disabled'; @endphp
                    <a title="Eliminar" class="btn-destroy-area btn btn-danger btn-sm {{$disabled ?? ''}}" href="#" id="btn-destroy_id_{{$area_conocimiento->id}}">
                        <i class="fas fa-trash"></i>
                    </a>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

{{-- </div> --}}

{!! Form::open(['route' => ['administracion.configuraciones.area_conocimientos.destroy',':AREA_CONOCIMIENTO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy-area', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/area_conocimientos/destroy.js") }}"></script>
@endsection

{{-- @include('administracion.datatables.default') --}}

@include('administracion.datatables.custom')

@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#table-data-area').DataTable( {
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
