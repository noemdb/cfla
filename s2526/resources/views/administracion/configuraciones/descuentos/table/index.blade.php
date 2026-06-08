@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_code_sm }}">Abreviación</th>
                <th class="{{ $class_asignatura }}">Código</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th title="Horas Teóricas" class="{{ $class_ht }}">H.Teóricas</th>
                <th title="Horas Prácticas" class="{{ $class_hp }}">H.Prácticas</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($descuentos as $descuento)

            <tr data-id="{{$descuento->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-users-username-{{ $descuento->id }}" class="{{ $class_code_sm  ?? ''}}">
                    {{$descuento->code_sm}}
                </td>
                <td id="td-users-username-{{ $descuento->id }}" class="{{ $class_user  ?? ''}}">
                    {{$descuento->code}}
                </td>
                <td id="td-users-username-{{ $descuento->id }}" class="{{ $class_user  ?? ''}}">
                    {{$descuento->name}}
                </td>
                <td id="td-users-username-{{ $descuento->id }}" class="{{ $class_ht  ?? ''}}">
                    {{$descuento->hour_t_week}}
                </td>
                <td id="td-users-username-{{ $descuento->id }}" class="{{ $class_hp  ?? ''}}">
                    {{$descuento->hour_p_week}}
                </td>
                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $descuento->id }}">
                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.configuraciones.descuentos.edit',$descuento->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs" href="#" id="btn-destroy_id_{{$descuento->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
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
