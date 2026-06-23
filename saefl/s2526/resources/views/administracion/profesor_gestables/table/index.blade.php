@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

<div>Planes de Evaluación asignados.</div>

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                {{-- <th class="{{ $class_profesor }}">Profesor</th> --}}
                <th class="{{ $class_asignatura }}" title="Área de Formación">Área de Formación</th>
                <th class="{{ $class_asignatura }}" title="Área de Formación">Grado</th>
                <th class="{{ $class_asignatura }}">Grupos Estables</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pensums as $pensum)

            @php
                $asignatura = ($pensum) ? $pensum->asignatura : null ;
                $grado = ($pensum) ? $pensum->grado : null ;
                $selected_id = ($selected) ? $selected->id : null ;
                $pensum_seccion_id = $pensum->seccion_id;
            @endphp

            <tr data-id="{{ $pensum->id ?? ''}}">
                <td class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                {{-- <td class="{{ $class_user  ?? ''}}">
                    {{ ($profesor) ? $profesor->smname : ''}}
                </td> --}}

                <td class="{{ $class_asignatura ?? '' }}">
                    {{ ($asignatura) ? $asignatura->code.' - '.$asignatura->name : ''}}
                </td>

                <td class="{{ $class_asignatura ?? '' }}">
                    {{ ($grado) ? $grado->name : null}}
                    {{ ($pensum) ? $pensum->seccion_name : null}}
                </td>

                <td class="{{ $class_asignatura ?? '' }}">
                    @php $grupo_estables = $pensum->getGrupoEstables($pensum_seccion_id) ; @endphp
                    @forelse ($grupo_estables as $grupo_estable)
                    
                        <div class=" border-bottom text-nowrap" title="{{$grupo_estable->name ?? ''}}">
                            {{$loop->iteration}}. {{$grupo_estable->code ?? ''}} <span class=" font-weight-normal text-muted">{{$grupo_estable->profesor_name ?? ''}}</span>
                        </div>
                    @empty
                        <div class="text-muted">NO HAY GRUPOS ASIGNADOS</div>
                    @endforelse                    
                </td>
                
                {{-- <td class="{{ $class_asignatura ?? '' }}">
                    @foreach ($grupo_estables as $grupo_estable)
                        <div class=" border-bottom text-nowrap" title="{{$grupo_estable->name ?? ''}}">
                            {{$loop->iteration}}. {{$grupo_estable->code ?? ''}}
                        </div>
                    @endforeach
                </td> --}}
                {{-- <td class="{{ $class_asignatura ?? '' }} text-left pl-4">
                    {{ ( $pevaluacion->evaluacions->IsNotEmpty() ) ? $pevaluacion->evaluacions->count() : ''}}
                </td> --}}
                {{-- <td class="{{ $class_state ?? '' }}">
                    {{ ($grado) ? $grado->name : ''}} {{ ($seccion) ? $seccion->name : ''}} {{ ($lapso) ? $lapso->code_sm : ''}}
                </td> --}}
                {{-- <td class="{{ $class_state ?? '' }}">
                    {{ ($lapso) ? $lapso->code_sm : ''}}
                </td> --}}
                <td class="{{ $class_action ?? '' }}" >
                    <div class="btn-group btn-group-sm">
                        @php
                            $inputs = [
                                'grado_id'=>$grado->id,
                                'pensum_seccion_id'=>$pensum_seccion_id,
                                // 'lapso_id'=>$lapso_id,
                                'pensum_id'=>$pensum->id,
                                // 'pevaluacion_id'=>$pevaluacion->id,
                            ];
                        @endphp
                        <a href="{{route('administracion.profesor_gestables.index',$inputs)}}" title="Asignar Evaluaciones" class="btn btn-light" role="button">
                            <i class="{{ $icon_menus['carga'] ?? ''}} fa-1x text-success"></i>
                        </a>
                        {{-- {{$inputs ?? ''}} --}}

                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

@include('administracion.datatables.default')
