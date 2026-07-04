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

<table width="100%" class="table table-striped table-sm table-hover p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_plan }}">P. Evaluación</th>
            <th class="{{ $class_descripcion }}">Descripción</th>
            {{-- <th class="{{ $class_profesor }}">Profesor</th> --}}
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_grado }}">Grado</th>
            <th class="{{ $class_lapso }}">Lapso</th>
            <th class="{{ $class_lapso }}">Fecha</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($evaluacions as $evaluacion)

        @php $pensum = $evaluacion->pevaluacion->pensum; @endphp

        <tr data-id="{{$evaluacion->id}}" data-evaluacion="{{$evaluacion->id ?? ''}}" class="table-{{(empty($evaluacion->administrativa->id)) ? 'default':'success'}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td id="td-evaluacion-pevaluacion-{{ $evaluacion->id }}" class="{{ $class_plan  ?? ''}}">
                {{ $evaluacion->pevaluacion->description ?? ''}}
            </td>
            <td id="td-evaluacion-description-{{ $evaluacion->id }}" class="{{ $class_descripcion  ?? ''}}">
                {{ $evaluacion->description ?? ''}}
            </td>
            {{-- <td id="td-evaluacion-profesor-{{ $evaluacion->id }}" class="{{ $class_profesor  ?? ''}}">
                {{ $evaluacion->pevaluacion->profesor->fullname ?? ''}}
            </td> --}}
            <td id="td-evaluacion-asignatura-{{ $evaluacion->id }}" class="{{ $class_asignatura ?? '' }}">
                {{ $evaluacion->pevaluacion->pensum->asignatura->name ?? ''}}
            </td>
            <td id="td-grado-{{ $evaluacion->id }}" class="{{ $class_grado ?? '' }}">
                {{ $evaluacion->pevaluacion->pensum->grado->name ?? ''}}
                {{ $evaluacion->pevaluacion->seccion->name ?? ''}}
            </td>
            <td id="td-lapso-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ $evaluacion->pevaluacion->lapso->name ?? ''}}
            </td>
            <td id="td-fecha-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ f_date($evaluacion->fecha) ?? ''}}
            </td>

            <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $evaluacion->id }}">
                <div class="btn-group">

                    @php $pevaluacion = $evaluacion->pevaluacion  @endphp

                    @php $disabled  = ($evaluacion->boletins->isNotEmpty()) ? ' disabled ': null ; @endphp
                    <a title="Editar" class="btn btn-warning {{ $disabled }}"  href="{{route('representants.evaluacions.edit',$evaluacion->id)}}" role="button">
                        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                    </a>

                    <a title="Registro de Notas" class="btn btn-primary" href="{{route('representants.boletins.carga',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                        <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x"></i>
                    </a>

                    @php $disabled = ($evaluacion->boletins->isNotEmpty()) ? ' disabled ': null ; @endphp
                    <a title="Eliminar" class="btn-destroy btn btn-danger {{ $disabled }}" href="#" id="btn-destroy_id_{{$evaluacion->id}}">
                        <i class="fas fa-trash"></i>
                    </a>

                </div>
            </td>

        </tr>
        @endforeach

    </tbody>

</table>

{{-- </div> --}}
{!! Form::open(['route' => ['representants.evaluacions.destroy',':EVALUACION_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/evaluacions/destroy.js") }}"></script>
@endsection

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
