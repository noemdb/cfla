@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-sm table-hover p-1 small" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_profesor }}">Profesor</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                {{-- <th class="{{ $class_lapso }}">Sección</th> --}}
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_lapso }}" title="Número de Evaluaciones">N.Evaluaciones</th>
                <th class="{{ $class_lapso }}" title="Número de Notas cargadas">N.Notas</th>
                <th class="{{ $class_lapso }}" title="Porcentaje de Carga de Notas">% de Carga</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
            @php
                $e = 0;
                $n = 0;
            @endphp
        @foreach($pevaluacions as $pevaluacion)

            @php
                $pensum = $pevaluacion->pensum;
                $grado = $pevaluacion->pensum->grado;
                $seccion = $pevaluacion->seccion;
                $lapso = $pevaluacion->lapso;
                $pensum = $pevaluacion->pensum;
                $goal_notas_cargadas = $pevaluacion->goal_notas_cargadas;
                $real_notas_cargadas = $pevaluacion->real_notas_cargadas;
                $porc_notas_cargadas = (!empty($goal_notas_cargadas)) ? round((100 * $real_notas_cargadas/$goal_notas_cargadas),2):null ;
                // $porc_notas_cargadas = ($porc_notas_cargadas>100) ? 100 : $porc_notas_cargadas ; //FixNMDB
                $count_evaluacions = (!empty($pevaluacion->evaluacions)) ? $pevaluacion->evaluacions->count():null;
                $e = $e + $count_evaluacions;
                $n = $n + $real_notas_cargadas;
            @endphp

            <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}" class="">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{ $pevaluacion->profesor->fullname ?? ''}}
                </td>
                <td class="{{ $class_email ?? '' }}">
                    {{ $pevaluacion->pensum->asignatura->name ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }} {{$grado->class_text_color}}">
                    {{ $grado->name ?? ''}} {{ $pevaluacion->seccion->name ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->lapso->name ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ $count_evaluacions ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ $real_notas_cargadas ?? '' }}
                </td>
                <td class="{{ $class_state ?? '' }} table-{{($porc_notas_cargadas==100) ? 'default':'danger'}}">
                    {{ $porc_notas_cargadas ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group btn-group-sm">

                        @php $disabled  = ($pevaluacion->evaluacions->isEmpty()) ? ' disabled ': null ; @endphp
                        @php $class_btn = ($pevaluacion->evaluacions->isEmpty()) ? 'btn-outline-secondary' : 'btn-success' ; @endphp
                        <a title="Registro de Notas" class="btn {{$class_btn ?? ''}} {{$disabled ?? ''}}" href="{{route('administracion.boletins.carga',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                            <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x"></i>
                        </a>

                        <a title="Acta de Notas por Asignatura" target="_blank" class="btn btn-info {{$disabled ?? ''}}" href="{{route('administracion.boletins.sabana_profesor.pdf',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                            <i class="{{ $icon_menus['acta_notas'] ?? ''}} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    {{-- partials contentivo de los scripts datatables --}}
    @include('administracion.datatables.default')
