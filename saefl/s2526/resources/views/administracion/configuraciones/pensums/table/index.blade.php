@php 
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp 

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($asignaturas as $asignatura)

            <tr data-asignatura="{{$asignatura->id}}" data-asignatura="{{$asignatura->id ?? ''}}" class="table-{{(empty($asignatura->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-users-username-{{ $asignatura->id }}" class="{{ $class_user  ?? ''}}">
                    {{$asignatura->fullname}}
                </td>
                <td id="td-profiles-ci_asignatura-{{ $asignatura->id }}" class="{{ $class_email ?? '' }}">
                    <span class="text-profiles-ci_asignatura-{{ $asignatura->id ?? ''}}">
                        {{ $asignatura->ci_asignatura ?? ''}}
                    </span>
                </td>
                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $asignatura->id }}">
                    <a title="Boleta de Retiro" class="btn btn-info btn-sm" target="_blank" href="{{ route('administracion.inscripciones.constancia.pdf',$asignatura->id) }}" role="button">
                        <i class="{{ $icon_menus['pdf'] }} fa-1x"></i>
                    </a>
                </td>

            </tr>
            @endforeach
            
        </tbody>
    
    </table>

{{-- </div> --}}

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