@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-sm small table-hover p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Asignatura.</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_lapso }}">F.Corte</th>
                <th class="{{ $class_lapso }}">F.Cierre</th>
                <th class="{{ $class_lapso }}" title="Porcentaje de Carga de Notas">% de Carga</th>
                <th class="{{ $class_action }} text-right">Acciones</th>
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
                $grupo_estable = $pevaluacion->grupo_estable;
            @endphp

            <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}"
            class="table-{{(empty($pevaluacion->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-pevaluacion-asignatura-{{ $pevaluacion->id }}" class="{{ $class_email ?? '' }}">
                    {{ $pevaluacion->pensum->asignatura->name ?? ''}}
                    @if ($grupo_estable) <div class=" text-muted small">Comp. de Formación: {{$grupo_estable->name ?? null}}</div> @endif
                </td>
                <td id="td-grado-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }} {{$grado->class_text_color}}">
                    {{ $grado->name ?? ''}} {{ $pevaluacion->seccion->name ?? ''}}
                </td>
                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                    {{ $lapso->name ?? ''}}
                </td>
                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                    {{ f_date($lapso->date_cutnote) }}
                </td>

                <td id="td-pevaluacion-{{ $pevaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                    {{ ($lapso->full_date_preclosing) ? $lapso->full_date_preclosing->format('d-m-Y h:ia') : null }}
                </td>

                <td class="{{ $class_state ?? '' }} table-{{($porc_notas_cargadas==100) ? 'default':'danger'}}">
                    {{ $porc_notas_cargadas ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }} text-right" id="btn-action-{{ $pevaluacion->id }}">
                    <div class="btn-group">
                        @php
                            $disabled_evaluation  = ( $pevaluacion->evaluacions->isEmpty() ) ? true : false ;
                            $disabled_lapse  = ( $fecha > $lapso->ffinal) ? true : false ;
                            $disabled_preclosing  = ( $lapso->status_preclosing) ? false : true ;

                            $disabled  = ( $disabled_evaluation || $disabled_lapse || $disabled_preclosing) ? ' disabled ': null ;

                            $pestudio = $pevaluacion->pestudio;
                            //$disabled  = ($pestudio->status_carga_notas=='true' && $lapso->status_last=='true') ? null : ' disabled ' ;
                        @endphp

                        @php $class_btn = ($pevaluacion->evaluacions->isEmpty()) ? 'btn-outline-secondary' : 'btn-success' ; @endphp
                        <a title="Registro de Notas" class="btn {{$class_btn ?? ''}} {{$disabled ?? ''}}" href="{{route('profesors.boletins.carga',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                            <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Acta de Notas por Profesor" target="_blank" class="btn btn-info" href="{{route('profesors.boletins.sabana.pdf',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                            <i class="{{ $icon_menus['acta_notas'] ?? ''}} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>


{{-- partials contentivo de los scripts datatables --}}
@include('profesors.datatables.default')
