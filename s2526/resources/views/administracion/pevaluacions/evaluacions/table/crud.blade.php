@php
    $class_N="d-none d-sm-table-cell";
    $class_plan="d-none d-md-table-cell";
    $class_descripcion="";
    $class_profesor="d-none d-md-table-cell";
    $class_asignatura="";
    $class_grado="d-none d-lg-table-cell";
    $class_lapso="d-none d-lg-table-cell";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            {{-- <th class="{{ $class_plan }}">P. Evaluación</th> --}}
            <th class="{{ $class_descripcion }}">Descripción</th>
            <th class="{{ $class_profesor }}">Profesor</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_grado }}">Grado/Sección/Lapso</th>
            {{-- <th class="{{ $class_lapso }}">Lapso</th> --}}
            <th class="{{ $class_lapso }}">Fecha</th>
            <th class="{{ $class_lapso }}">F.Registro</th>
            <th class="{{ $class_lapso }}">F.Actualización</th>
            <th class="{{ $class_lapso }}">Notas</th>
            <th class="{{ $class_lapso }}">Promedio</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($evaluacions as $evaluacion)

        @php $pensum = $evaluacion->pevaluacion->pensum; @endphp
        @php $profesor = $evaluacion->pevaluacion->profesor; @endphp
        @php $status_active = (!empty($profesor)) ? $profesor->status_active:null; @endphp
        @php $notas_count = (!empty($evaluacion->notas_count)) ? $evaluacion->notas_count:null; @endphp
        @php $pevaluacion = (!empty($evaluacion->pevaluacion)) ? $evaluacion->pevaluacion:null; @endphp

        <tr data-id="{{$evaluacion->id}}" data-evaluacion="{{$evaluacion->id ?? ''}}" class="table-{{(empty($notas_count)) ? 'danger':'default'}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
                @admin [{{$evaluacion->pevaluacion->id ?? ''}}] @endadmin
            </td>
            <td style="white-space:normal" id="td-evaluacion-description-{{ $evaluacion->id }}" class="{{ $class_descripcion  ?? ''}}" title="{{ $evaluacion->description ?? ''}}">
                {{-- {{ Str::limit($evaluacion->description,20,'...') ?? ''}} --}}
                <span>{{$evaluacion->description}}</span>
            </td>

            <td id="td-evaluacion-profesor-{{ $evaluacion->id }}" class="{{ $class_profesor  ?? ''}} text-{{ ($status_active == 'false') ? 'secondary':'dark' }}" title="{{$profesor->fullname ?? ''}}">
                {{-- {{ (!empty($profesor->id)) ? Str::limit($profesor->fullname,20,'...') : null }} --}}
                {{ (!empty($profesor->id)) ? $profesor->fullname : null }}
            </td>
            <td style="white-space:normal" id="td-evaluacion-asignatura-{{ $evaluacion->id }}" class="{{ $class_asignatura ?? '' }}" title="{{$evaluacion->pevaluacion->pensum->asignatura->name ?? '' }}">
                {{-- {{ Str::limit($evaluacion->pevaluacion->pensum->asignatura->code,15,'...') ?? ''}} --}}
                <span>{{$evaluacion->pevaluacion->pensum->asignatura->code}}</span>
            </td>
            <td id="td-grado-{{ $evaluacion->id }}" class="{{ $class_grado ?? '' }}">
                {{ $evaluacion->pevaluacion->pensum->grado->name ?? ''}}
                {{ $evaluacion->pevaluacion->seccion->name ?? ''}} ||
                {{ $evaluacion->pevaluacion->lapso->name ?? ''}}
            </td>
            <td id="td-fecha-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ f_date($evaluacion->fecha) ?? ''}}
            </td>
            </td>
            <td class="text-nowrap" id="td-created_at-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ ($evaluacion->created_at) ? $evaluacion->created_at->format('d-m-Y h:i A') : null }}
            </td>
            </td>
            <td class="text-nowrap" id="td-updated_at-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ ($evaluacion->updated_at) ? $evaluacion->updated_at->format('d-m-Y h:i A') : null }}
            </td>
            <td id="td-boletins-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ $notas_count ?? ''}}
            </td>

            <td id="td-boletins-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ $evaluacion->promedio ?? ''}}
            </td>

            <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $evaluacion->id }}">
                <div class="btn-group btn-group-sm">
                    <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.evaluacions.edit',$evaluacion->id)}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>

                    <a title="Registro de Notas" class="btn btn-success" href="{{route('administracion.boletins.carga',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                        <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x"></i>
                    </a>

                    
                    @php $disabled = ($evaluacion->promedio > 0 && !Auth::user()->isControlDir()) ? ' disabled ': null ; @endphp

                    <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled }}" href="#" id="btn-destroy_id_{{$evaluacion->id}}">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </td>

        </tr>
        @endforeach

    </tbody>

</table>

{!! Form::open(['route' => ['administracion.evaluacions.destroy',':EVALUACION_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!} {!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/evaluacions/destroy.js") }}"></script> @endsection

@include('administracion.datatables.exportBootstrap')
