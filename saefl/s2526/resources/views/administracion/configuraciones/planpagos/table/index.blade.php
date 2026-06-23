@php
    $class_N="d-none d-sm-table-cell";
    $class_user="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_code_sm }}">Nombre</th>
                <th class="{{ $class_user }}">Descripción</th>
                <th class="{{ $class_user }}">Observaciones</th>
                <th class="{{ $class_ht }}">Estado</th>
                <th class="{{ $class_ht }}">Cont.Inscrp.</th>
                <th class="{{ $class_user }}" title="Número de estudiantes asignados a éste plan">N.Estudiantes</th>
                <th class="{{ $class_user }}" title="Número de conceptos de cobro">N.Conceptos</th>
                <th class="{{ $class_user }}" title="Número de cuentas de cobro">N.Cuentas</th>
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($planpagos as $planpago)

            <tr data-id="{{$planpago->id}}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td id="td-name-{{ $planpago->id }}" class="{{ $class_code_sm  ?? ''}}">
                    {{$planpago->name ?? ''}}
                </td>
                <td id="td-description-{{ $planpago->id }}" class="{{ $class_user  ?? ''}}">
                    {{$planpago->description ?? ''}}
                </td>
                <td id="td-observations-{{ $planpago->id }}" class="{{ $class_user  ?? ''}}">
                    {{$planpago->observations ?? ''}}
                </td>
                <td id="td-status_active-{{ $planpago->id }}" class="{{ $class_ht  ?? ''}}">
                    {{($planpago->status_active=='true') ? 'Activo':'Desactivo'}}
                </td>
                <td id="td-status_active-{{ $planpago->id }}" class="{{ $class_ht  ?? ''}}">
                    {{($planpago->status_inscription_affects=='true') ? 'SI':'NO'}}
                </td>
                <td id="td-planpago-{{ $planpago->id }}" class="{{ $class_ht  ?? ''}}">
                    {{ $planpago->administrativas->count() ?? '' }}
                </td>
                <td id="td-cuentaxpagars-{{ $planpago->id }}" class="{{ $class_ht  ?? ''}}">
                    {{ $planpago->cuentaxpagars->where('type','GENERAL')->count() ?? '' }}
                </td>
                <td id="td-planpago-{{ $planpago->id }}" class="{{ $class_ht  ?? ''}}">
                    {{ $planpago->conceptopagos->count() ?? '' }}
                </td>
                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $planpago->id }}">
                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"  href="{{route('administracion.configuraciones.planpagos.edit',$planpago->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        @php $disabled = ($planpago->status_delete) ? null : ' disabled ' ; @endphp
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled ?? '' }}" href="#" id="btn-destroy_id_{{$planpago->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach

        </tbody>

    </table>

{!! Form::open(['route' => ['administracion.configuraciones.planpagos.destroy',':PLAN_PAGO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':PLAN_PAGO_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/default/destroy.js") }}"></script>
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
