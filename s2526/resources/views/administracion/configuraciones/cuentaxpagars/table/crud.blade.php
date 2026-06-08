@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="";
    $class_grado="";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Nombre</th>
                <th class="{{ $class_ci }}">Plan de Pago</th>
                <th class="{{ $class_ci }}">Tipo</th>
                <th class="{{ $class_grado }}">F. Vencimiento</th>
                <th class="{{ $class_grado }}">Descripción</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($cuentaxpagars as $cuentaxpagar)

            <tr data-cuentaxpagar="{{$cuentaxpagar->id}}" data-cuentaxpagar="{{$cuentaxpagar->id ?? ''}}" class="table-{{(empty($cuentaxpagar->administrativa->id)) ? 'default':'success'}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_email ?? '' }}">
                    {{$cuentaxpagar->name ?? ''}}
                </td>
                <td class="{{ $class_user  ?? ''}}">
                    {{$cuentaxpagar->planpago->name ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{$cuentaxpagar->type ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{$cuentaxpagar->date_expiration ?? ''}}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{$cuentaxpagar->description ?? ''}}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $cuentaxpagar->id }}">
                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.configuraciones.cuentaxpagars.edit',$cuentaxpagar->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs" href="#" id="btn-destroy_id_{{$cuentaxpagar->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

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
