@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                {{-- <th class="{{ $class_ci }}">Cédula</th> --}}
                <th class="{{ $class_planpago }}">P.Benéfico</th>
                <th class="{{ $class_planpago }}">Monto (%)</th>
                <th class="{{ $class_planpago }}">P.Pago</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_fecha }}">F.Inicial</th>
                <th class="{{ $class_fecha }}">F.Final</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

        @foreach($plan_beneficos as $plan_benefico)
            @php
                $estudiant = $plan_benefico->estudiant;
                $exchange_ammount_expire_bill = ($estudiant) ? $estudiant->exchange_ammount_expire_bill : null;
                $enable_inscription = ($estudiant) ? $estudiant->enable_inscription : null;
                $status_administrativa = ($estudiant) ? empty($estudiant->administrativa->id) : null;
                $ci_estudiant = ($estudiant) ? $estudiant->ci_estudiant : null;
                $id = ($estudiant) ? $estudiant->id : null;
                $grado = ($estudiant) ? $estudiant->grado : null;
                $seccion = ($estudiant) ? $estudiant->seccion : null;
            @endphp

            {{-- @if ($estudiant->active('true')) --}}
                <tr data-id="{{$plan_benefico->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-users-username-{{ $estudiant->id ?? null }}" class="{{ $class_estudiant  ?? ''}}">
                        @admin [{{$estudiant->id ?? ''}}] @endadmin
                        <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$ci_estudiant]) }}">
                            <span class=" font-weight-bold text-{{ ($exchange_ammount_expire_bill > 0) ? 'danger':'dark'}}">{{$estudiant->fullname ?? null}}</span>
                        </a>
                        <span class="small text-muted">{{ $ci_estudiant ?? ''}}</span>
                    </td>

                    <td id="td-planpago-estudiant-{{ $estudiant->id ?? null }}" class="{{ $class_planpago ?? '' }}">
                        {{ $plan_benefico->descuento->descuento_name ?? ''}}
                    </td>
                    <td id="td-planpago-estudiant-{{ $estudiant->id ?? null }}" class="{{ $class_planpago ?? '' }}">
                        {{ f_float($plan_benefico->descuento->descuento_ammount) }}
                    </td>

                    <td id="td-planpago-estudiant-{{ $estudiant->id ?? null }}" class="{{ $class_planpago ?? '' }}">
                        {{ $estudiant->planpago_name ?? null}}
                    </td>

                    <td id="td-users-is_active-{{ $estudiant->id ?? null }}" class="{{ $class_grado ?? '' }}">
                        {{($grado) ? $grado->name : null}} {{($seccion) ? $seccion->name : null}}
                    </td>
                    <td id="td-users-is_active-{{ $estudiant->id ?? null }}" class="{{ $class_grado ?? '' }}">
                        {{ ($plan_benefico->created_at) ? $plan_benefico->created_at->format('d-m-Y') : null }}
                    </td>
                    <td id="td-users-is_active-{{ $estudiant->id ?? null }}" class="{{ $class_grado ?? '' }}">
                        {{ ($plan_benefico->ffinal) ? $plan_benefico->ffinal->format('d-m-Y') : null }}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id ?? null }}">
                        <div class="btn-group btn-group-sm">
                            @php $id_modal = ($estudiant) ? 'modal_abono_'.$estudiant->id : 'modal_abono_'.rand(0,5000); @endphp
                            <a title="Mostrar detalles del credito a favor" class="btn btn-info btn-sm" href="#" data-toggle="modal" data-target="#{{$id_modal ?? 'id_modal'}}">
                                <i class="fas fa-info"></i>
                            </a>

                            <a title="Editar Registro" class="btn btn-warning btn-sm" href="{{ route('administracion.configuraciones.plan_beneficos.edit',['id'=>$plan_benefico->id]) }}" role="button">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                            </a>

                            <a title="Eliminar" class="btn-destroy btn btn-danger btn-xs" href="#" id="btn-destroy_id_{{$plan_benefico->id}}">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            {{-- @endif --}}
            @endforeach

        </tbody>
    </table>

{{-- </div> --}}
{!! Form::open(['route' => ['administracion.configuraciones.plan_beneficos.destroy',':PLAN_BENEFICO_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':PLAN_BENEFICO_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/default/destroy.js") }}"></script>
@endsection


{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')
