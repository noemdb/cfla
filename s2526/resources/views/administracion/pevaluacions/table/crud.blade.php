@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_evaluacion="d-none d-lg-table-cell text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_profesor }}">username</th>
                <th class="{{ $class_profesor }}">Profesor</th>
                <th class="{{ $class_profesor }}">GSmail</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_asignatura }}">N.Actividades/N.Evaluaciones</th>
                <th class="{{ $class_grado }}">P. Estudio</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                @admin <th class="{{ $class_grado }}">SId</th> @endadmin
                <th class="{{ $class_evaluacion }}" title="Registro del 1er Indicador">F.1erIndicador</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_lapso }}">Escala</th>
                <th class="{{ $class_lapso }}">T.Nota</th>
                {{-- <th class="{{ $class_lapso }}">Sección</th> --}}
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pevaluacions as $pevaluacion)
            @php 
                $pestudio = $pevaluacion->pestudio;
                $profesor = $pevaluacion->profesor;
                $seccion = $pevaluacion->seccion;
                $pensum = $pevaluacion->pensum;
                $grado = $pensum->grado;
                $grupo_estable = $pevaluacion->grupo_estable;
                $evaluacions = $pevaluacion->evaluacions;
                $evaluacion = ($evaluacions->IsNotEmpty()) ? $evaluacions->sortByDesc('created_att')->first(): null ;
                $activities = $pevaluacion->activities;
            @endphp

            <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}" class="table-{{(empty($pevaluacion->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-pevaluacion-profesor-{{ $pevaluacion->id }}" class="{{ $class_user  ?? ''}}">
                    {{ $profesor->user->username ?? ''}}
                </td>
                
                <td id="td-pevaluacion-profesor-{{ $pevaluacion->id }}" class="{{ $class_user  ?? ''}}">
                    {{ $profesor->fullname ?? ''}}
                </td>

                <td id="td-pevaluacion-profesor-{{ $pevaluacion->id }}" class="{{ $class_user  ?? ''}}">
                    {{ $profesor->gsemail ?? ''}}
                </td>
                
                <td id="td-pevaluacion-asignatura-{{ $pevaluacion->id }}" class="{{ $class_email ?? '' }}">
                    {{ $pevaluacion->pensum->asignatura->name ?? ''}}
                    @if ($grupo_estable) <div class=" text-muted small">Comp. de Formación: {{$grupo_estable->name ?? null}}</div> @endif
                </td>
                <td id="td-pevaluacion-asignatura-{{ $pevaluacion->id }}" class="{{ $class_email ?? '' }}">
                    <div class="text-center">
                        {{ ( $activities->IsNotEmpty() ) ? $activities->count() : '0'}} /  {{ ( $evaluacions->IsNotEmpty() ) ? $evaluacions->count() : '0'}}
                    </div>
                </td>
                <td>
                    <div class="">{{ ($pestudio) ? $pestudio->name : null }}</div>
                    <div class="text-muted">{{ ($pestudio) ? $pestudio->code : null }}</div>
                </td>
                <td id="td-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $grado->name ?? ''}} {{ $seccion->name ?? ''}}
                </td>
                @admin <td class="{{ $class_state ?? '' }}"> {{ $seccion->id ?? ''}} </td> @endadmin
                </td>
                <td class="{{ $class_evaluacion ?? '' }}">
                    {{ ($evaluacion) ? $evaluacion->created_at->format('d-m-Y h:i A') : null}}
                </td>
                <td id="td-administrativa-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->lapso->name ?? ''}}
                </td>
                <td id="td-administrativa-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->escala->name ?? ''}}
                </td>
                <td id="td-administrativa-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->nota_type ?? ''}}
                </td>
                {{-- <td id="td-administrativa-{{ $pevaluacion->id }}" class="{{ $class_state ?? '' }}">
                    {{ $pevaluacion->seccion->name ?? ''}}
                </td> --}}

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $pevaluacion->id }}">
                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.pevaluacions.edit',$pevaluacion->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        @php
                            $grado_id=$pevaluacion->pensum->grado->id;
                            $seccion_id=$pevaluacion->seccion_id;
                            $pensum_id=$pevaluacion->pensum->id;
                            $lapso_id=1;
                        @endphp

                        @php $disabled = ($evaluacions->isNotEmpty() || $activities->isNotEmpty()) ? ' disabled ': null ; @endphp
                        {{-- {{$pevaluacion->evaluacions ?? 'fallo'}} --}}
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$pevaluacion->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                        {{-- <div class="dropdown"> --}}
                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="{{ $icon_menus['options'] ?? ''}} fa-1x"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            {{-- <a title="Duplicar éste plan a otra sección" class="btn-clone dropdown-item {{ ($pensum->pevaluacion_complete($pensum_id,$lapso_id)) ? 'disabled' : '' }}" href="#" role="button"> --}}
                            <a title="Duplicar éste plan y asignar a otra sección" class="btn-clone dropdown-item {{ ($pensum->pevaluacion_complete($pensum_id,$lapso_id)) ? 'disabled' : '' }}" href="{{route('administracion.pevaluacions.create_clone',$pevaluacion->id)}}" role="button">
                                <i class="{{ $icon_menus['clone'] ?? ''}} fa-1x text-dark"></i>
                                Duplicar plan
                            {{-- </a> --}}
                            <a title="Asignar Evaluaciones" class="dropdown-item" href="{{route('administracion.pevaluacions.create',['grado_id'=>$grado_id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum_id,'lapso_id'=>$lapso_id])}}" role="button">
                                <i class="{{ $icon_menus['evaluacion'] ?? ''}} fa-1x text-info"></i>
                                Asignar Evaluaciones
                            </a>
                            <a title="Cargar de Notas" class="dropdown-item" href="{{route('administracion.boletins.index',['grado_id'=>$grado_id,'seccion_id'=>$seccion_id,'pensum_id'=>$pensum_id,'lapso_id'=>$lapso_id])}}" role="button">
                                <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x  text-primary"></i>
                                Cargar de Notas
                            </a>
                        </div>
                        {{-- </div> --}}
                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.pevaluacions.destroy',':PEVALUCION_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/pevaluacions/destroy.js") }}"></script> @endsection

{{-- @section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection --}}

@include('administracion.datatables.exportBootstrap')
