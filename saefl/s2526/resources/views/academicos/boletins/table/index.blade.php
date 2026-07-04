@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-sm table-hover p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_lapso }}" title="Porcentaje de Carga de Notas">% de Carga</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pevaluacions as $pevaluacion)

            @php
                $pensum = $pevaluacion->pensum;
                $grado = $pevaluacion->pensum->grado;
                $seccion = $pevaluacion->seccion;
                $lapso = $pevaluacion->lapso;
                $goal_notas_cargadas = $pevaluacion->goal_notas_cargadas;
                $real_notas_cargadas = $pevaluacion->real_notas_cargadas;
                $porc_notas_cargadas = (!empty($goal_notas_cargadas)) ? round((100 * $real_notas_cargadas/$goal_notas_cargadas),2):null ;
            @endphp

            <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}"
            class="table-{{(empty($pevaluacion->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-pevaluacion-asignatura-{{ $pevaluacion->id }}" class="{{ $class_email ?? '' }}">
                    {{ $pevaluacion->pensum->asignatura->name ?? ''}}
                </td>
                <td id="td-grado-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }} {{$grado->class_text_color}}">
                    {{ $grado->name ?? ''}} {{ $pevaluacion->seccion->name ?? ''}}
                </td>
                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->lapso->name ?? ''}}
                </td>

                <td class="{{ $class_state ?? '' }} table-{{($porc_notas_cargadas==100) ? 'default':'danger'}}">
                    {{ $porc_notas_cargadas ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $pevaluacion->id }}">
                    <div class="btn-group">

                        @php $disabled  = ($pevaluacion->evaluacions->isEmpty()) ? ' disabled ': null ; @endphp
                        @php $class_btn = ($pevaluacion->evaluacions->isEmpty()) ? 'btn-outline-secondary' : 'btn-success' ; @endphp
                        <a title="Registro de Notas" class="btn {{$class_btn ?? ''}} {{$disabled ?? ''}}" href="{{route('academicos.boletins.carga',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                            <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Acta de Notas por Profesor" target="_blank" class="btn btn-info" href="{{route('academicos.boletins.sabana.pdf',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                            <i class="{{ $icon_menus['acta_notas'] ?? ''}} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>


@section('scripts') @parent <script src="{{ asset("js/models/pevaluacions/destroy.js") }}"></script> @endsection

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection
