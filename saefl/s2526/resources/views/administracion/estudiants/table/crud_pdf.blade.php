@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_contacto="d-none d-lg-table-cell";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_ci }}">Indent.</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>                
                <th class="{{ $class_planpago }}">P.Pago</th>
                <th class="{{ $class_grado }}">Grado</th>
                {{-- <th class="{{ $class_fecha }}" title="Fecha de Inscripción Administrativa">Fecha</th> --}}
                {{-- <th class="{{ $class_action }}">Acción</th> --}}
                <th style="padding-left:2px;padding-right:2px;" class="{{ $class_contacto }}">Inf. Contacto</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)
                <tr data-estudiant_id="{{$estudiant->id ?? ''}}" data-id="{{$estudiant->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>
                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>
                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->fullname}}
                    </td>
                    <td id="td-planpago-estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        {{ $estudiant->administrativa->planpago->name ?? ''}}
                    </td>
                    <td id="td-estudiant-inscripcion-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                        {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                    </td>
                    <td style="white-space: wrap !important">
                        <span class="small">
                            {{ $estudiant->representant->fullphone ?? ''}}<br>{{ $estudiant->representant->email ?? ''}}
                        </span>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

{!! Form::open(['route' => ['administracion.estudiants.destroy',':ESTUDIANT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/estudiants/destroy.js") }}"></script> @endsection

{{-- </div> --}}

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/default.js") }}"></script>
@endsection
