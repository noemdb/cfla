@php
    $class_N="d-none d-sm-table-cell";
    $class_pProfesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

    {{-- <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"> --}}
    <table width="100%" class="table table-striped table-hover table-sm small p-1">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_pProfesor }}">Profesor</th>
                <th class="{{ $class_asignatura }}" title="Descripción del Plan de Evaluación">Desc. PE.</th>
                <th class="{{ $class_asignatura }}">N.Evaluaciones</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_asignatura }}">Grupos Estables</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pevaluacions as $pevaluacion)

            @php
                $pensum = $pevaluacion->pensum;
                $seccion = $pevaluacion->seccion;
                $lapso = $pevaluacion->lapso;
                $pProfesor = $pevaluacion->pProfesor;
                $asignatura = ($pensum) ? $pensum->asignatura : null ;
                // $grupo_estables = $pevaluacion->grupo_estables ;
                $grupo_estables = $pevaluacion->getGrupoEstables($profesor->id) ;
                $grado = ($pensum) ? $pensum->grado : null ;
                $status_gestable = $pevaluacion->status_gestable;
                $selected_id = ($selected) ? $selected->id : null ;
            @endphp

            <tr data-id="{{ $pevaluacion->id ?? ''}}" class="{{ ( $pevaluacion->id == $selected_id ) ? ' table-secondary font-weight-bold ':null }}">
                <td class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{ ($pProfesor) ? $pProfesor->smname : ''}}
                </td>
                <td class="{{ $class_asignatura ?? '' }}">
                    <span class=" text-truncate" title="{{$pevaluacion->description ?? null}}">
                        {{ ($asignatura) ? $asignatura->code : ''}}
                        @php $description = ($pevaluacion) ? Str::limit(strtoupper($pevaluacion->description),32): null @endphp
                        {{ $description ?? ''}}
                    </span>
                </td>

                <td class="{{ $class_asignatura ?? '' }} text-left pl-4">
                    {{ ( $pevaluacion->evaluacions->IsNotEmpty() ) ? $pevaluacion->evaluacions->count() : ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ ($grado) ? $grado->name : ''}} {{ ($seccion) ? $seccion->name : ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ ($lapso) ? $lapso->code_sm : ''}}
                </td>

                <td class="{{ $class_asignatura ?? '' }}">

                    <ul class="list-group">

                        @foreach ($grupo_estables as $grupo_estable)
                            <li class="list-group-item p-1">
                                {{$loop->iteration}}.- {{$grupo_estable->name ?? ''}}
                                @php $inputs = [
                                    'pevaluacion_id'=>$pevaluacion->id,
                                    'grupo_estable_id'=>$grupo_estable->id,
                                    'profesor_id'=>$profesor->id,
                                ];
                                @endphp
                                <a title="Cargar de Notas" class="btn btn-light btn-sm float-right" role="button" href="{{route('profesors.profesor_gestables.index',$inputs)}}" >
                                    <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x text-success"></i>
                                </a>
                            </li>
                        @endforeach

                    </ul>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

{{-- @include('administracion.datatables.default') --}}



