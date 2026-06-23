@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="nosort";
    $table_id = "table-data-".$pestudio->code_oficial."-".$registro_titulo->id;
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{$table_id ?? "defualt"}}">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_profesor }}">Estudiante</th>
                <th class="{{ $class_profesor }}">Identificador</th>
                <th class="{{ $class_profesor }}">Sección</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)

                <tr data-id="{{$estudiant->id}}" data-estudiant="{{$estudiant->id ?? ''}}" class="table-{{(empty($estudiant->registro_titulo->id)) ? 'light':'success'}}">
                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>
                    <td class="{{ $class_profesor  ?? ''}}">
                        {{ $estudiant->fullname ?? ''}}
                    </td>
                    <td class="{{ $class_profesor  ?? ''}}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>
                    <td class="{{ $class_profesor  ?? ''}}">
                        {{ $estudiant->getInscripcion()->seccion->name ?? ''}}
                    </td>

                    <td class="{{ $class_action ?? '' }}">
                        <div class="btn-group btn-group-sm">

                            <a title="Imprimir Contancia de Promoción" class="btn btn-dark btn-sm" target="_blank"
                                href="{{route('administracion.registro_titulos.constancia.promocion.pdf',$estudiant->id)}}"
                                role="button" >
                                <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                            </a>
                        </div>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.custom')
@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#{{$table_id ?? "defualt"}}').DataTable( {
                "pagingType": "simple_numbers",
                "pageLength": 10,
                "bLengthChange": false,
                "bPaginate": false,
                "searching": false,
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
