@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_profesor }}">Estudiante</th>
                <th class="{{ $class_profesor }}">Grado</th>
                <th class="{{ $class_asignatura }}">Plan de Estudio</th>
                <th class="{{ $class_asignatura }}">% Carga</th>
                <th class="{{ $class_asignatura }}" title="Índice Académico">I.A.</th>
                <th class="{{ $class_asignatura }}">N.Instituciones</th>
                <th class="{{ $class_lapso }}">Eliminado</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($historico_notas as $historico_nota)

                @php
                    $estudiant = $historico_nota->estudiant;
                    $pestudio = $historico_nota->pestudio;
                @endphp

                <tr data-id="{{$historico_nota->id}}" data-historico_nota="{{$historico_nota->id ?? ''}}" class="table-{{(empty($historico_nota->administrativa->id)) ? 'default':'success'}} {{($historico_nota->deleted_at) ? 'font-weight-bold text-danger':null}} ">
                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>
                    <td class="{{ $class_profesor  ?? ''}}">
                        {{ $estudiant->fullname ?? ''}} <small class="text-muted">{{ $estudiant->ci_estudiant ?? ''}}</small>
                    </td>
                    <td class="{{ $class_profesor  ?? ''}}">
                        {{ $estudiant->full_inscripcion ?? ''}}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $pestudio->name ?? ''}}
                    </td>
                    @php
                        $real = $historico_nota->getRealNotas($pestudio->id);
                        $goal = $historico_nota->getGoalNotas($pestudio->id);
                        $indicador = ($goal) ? round((100*$real/$goal),2):0;
                    @endphp
                    <td id="td-historico_nota-carga-{{ $historico_nota->id }}" class="{{ $class_asignatura ?? '' }} text-{{ ($real>$goal) ? 'danger':'dark'}}">
                        {{ ($real>$goal) ? 'ERROR':$indicador.'%'}}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $estudiant->GetIA($pestudio->id) ?? '' }}
                    </td>
                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ $historico_nota->oinstitucions->count() ?? ''}}
                    </td>

                    <td class="{{ $class_asignatura ?? '' }}">
                        {{ ($historico_nota->deleted_at) ? 'SI':'NO' }}
                        {{-- {{$historico_nota->deleted_at}} --}}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $historico_nota->id }}">
                        <div class="btn-group btn-group-sm">
                            {{-- <a title="Detalles" class="btn btn-info btn-sm"  href="{{route('administracion.historico_notas.show',$historico_nota->id)}}" role="button">
                                <i class="{{ $icon_menus['show'] ?? ''}} fa-1x"></i>
                            </a> --}}

                            @php $route = route('administracion.historico_notas.index',['estudiant_id'=>$historico_nota->estudiant_id,'pestudio_id'=>$pestudio->id]) @endphp
                            <a title="Editar" class="btn btn-warning btn-sm {{ ($historico_nota->deleted_at) ? 'disabled' : null }}"  href="{{ $route ?? '' }}" role="button">
                                <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                            </a>

                            <a title="Imprimir" class="btn-print btn btn-dark btn-sm {{ ($historico_nota->deleted_at) ? 'disabled' : null }}"
                                data-url="{{route('administracion.historico_notas.certificacion.pdf',$historico_nota->id)}}"
                                href="{{route('administracion.historico_notas.certificacion.pdf',$historico_nota->id)}}" target="_blank"
                                role="button" >
                                <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                            </a>

                            {{-- Botón RESTORE - Solo visible para registros eliminados --}}
                            @if($historico_nota->deleted_at)
                                @control
                                <a title="Restaurar" class="btn-restore btn btn-success btn-xs" href="#" 
                                   data-id="{{ $historico_nota->id }}"
                                   data-estudiant="{{ $estudiant->fullname ?? '' }}">
                                    <i class="fas fa-undo"></i>
                                </a>
                                @endcontrol
                            @else
                                {{-- Botón ELIMINAR - Solo visible para registros activos --}}
                                @control
                                <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs" href="#" id="btn-destroy">
                                    <i class="fas fa-trash"></i>
                                </a>
                                @endcontrol
                            @endif
                        </div>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.historico_notas.destroy',':HISTORICO_NOTA_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}

{!! Form::open(['route' => ['administracion.historico_notas.restore',':HISTORICO_NOTA_ID'], 'method' => 'POST', 'id'=>'form-restore', 'role'=>'form']) !!}
{!! Form::close() !!}

@section('scripts') 
@parent 
<script src="{{ asset("js/models/historico_notas/destroy.js") }}"></script>
<script src="{{ asset("js/models/historico_notas/restore.js") }}"></script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
