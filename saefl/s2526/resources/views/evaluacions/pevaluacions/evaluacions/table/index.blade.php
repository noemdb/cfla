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
            <th class="{{ $class_descripcion }}">Descripción</th>
            <th class="{{ $class_profesor }}">Profesor</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_grado }}">Grado/Sección/Lapso</th>
            <th class="{{ $class_lapso }}">Fecha</th>
            <th class="{{ $class_lapso }}">Notas</th>
            <th class="{{ $class_lapso }}">Promedio</th>
            {{-- <th class="{{ $class_action }}">Acciones</th> --}}
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($evaluacions as $evaluacion)

        @php $pevaluacion = $evaluacion->pevaluacion; @endphp
        @php $pensum = ($pevaluacion) ? $pevaluacion->pensum : null; @endphp
        @php $asignatura = ($pensum) ? $pensum->asignatura : null; @endphp
        @php $profesor = ($pevaluacion) ? $pevaluacion->profesor : null; @endphp
        @php $grado = ($pensum) ? $pensum->grado : null; @endphp
        @php $seccion = ($pevaluacion) ? $pevaluacion->seccion : null; @endphp
        @php $lapso = ($pevaluacion) ? $pevaluacion->lapso : null; @endphp


        @php $status_active = (!empty($profesor)) ? $profesor->status_active:null; @endphp
        @php $notas_count = (!empty($evaluacion->notas_count)) ? $evaluacion->notas_count:null; @endphp
        @php $pevaluacion = (!empty($evaluacion->pevaluacion)) ? $evaluacion->pevaluacion:null; @endphp

        <tr data-id="{{$evaluacion->id}}" data-evaluacion="{{$evaluacion->id ?? ''}}" class="table-{{(empty($notas_count)) ? 'danger':'default'}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
                @admin [{{$pevaluacion->id ?? ''}}] @endadmin
            </td>
            <td id="td-evaluacion-description-{{ $evaluacion->id }}" class="{{ $class_descripcion  ?? ''}} text-uppercase" title="{{ $evaluacion->description ?? ''}}">
                {{ Str::limit($evaluacion->description,20,'...') ?? ''}}
            </td>

            <td id="td-evaluacion-profesor-{{ $evaluacion->id }}" class="{{ $class_profesor  ?? ''}} text-{{ ($status_active == 'false') ? 'secondary':'dark' }}" title="{{$profesor->fullname ?? ''}}">
                {{ (!empty($profesor->id)) ? Str::limit($profesor->fullname,20,'...') : null }}
            </td>
            <td id="td-evaluacion-asignatura-{{ $evaluacion->id }}" class="{{ $class_asignatura ?? '' }}" title="{{$evaluacion->pevaluacion->pensum->asignatura->name ?? '' }}">
                {{ ($asignatura) ? Str::limit($asignatura->code,15,'...') : null}}
                <div class="text-muted small">
                    {{ ($asignatura) ? $asignatura->name : null}}
                </div>
            </td>
            <td id="td-grado-{{ $evaluacion->id }}" class="{{ $class_grado ?? '' }}">
                {{ $grado->name ?? ''}}
                {{ $seccion->name ?? ''}} ||
                {{ $lapso->name ?? ''}}
            </td>
            <td id="td-fecha-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ f_date($evaluacion->fecha) ?? ''}}
            </td>
            <td id="td-boletins-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ $notas_count ?? ''}}
            </td>

            <td id="td-boletins-{{ $evaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ $evaluacion->promedio ?? ''}}
            </td>

        </tr>
        @endforeach

    </tbody>

</table>

@section('stylesheet') @parent <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}"> @endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection
