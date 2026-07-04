@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = '';
    $class_grado = '';
    $class_action = '';
@endphp
{!! Form::open([
    'route' => 'administracion.administrativas.asignarStore',
    'method' => 'POST',
    'class' => 'form-signin',
]) !!}

{!! Form::hidden('search', $search) !!}
{!! Form::hidden('prosecucion_seccion_id', $prosecucion_seccion_id) !!}
{!! Form::hidden('status_preinscripcion', $status_preinscripcion) !!}

<table width="100%" class="table table-striped table-hover table-sm small p-1 " id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            <th class="{{ $class_ci }}">Cédula</th>
            <th class="{{ $class_grado }} text-center">Preinscripción</th>
            <th class="{{ $class_grado }} text-center">Ins.Académica</th>
            <th class="{{ $class_grado }} text-center" title="Saldo total a favor">SAF [$]</th>
            <th class="{{ $class_grado }} text-center" title="Deuda Actual">D. Actual [$]</th>
            <th class="{{ $class_grado }}">Plan de pago</th>
            {{-- <th class="{{ $class_grado }}">Descuento</th> --}}
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($estudiants as $estudiant)
            @php
                $preinscripcion = $estudiant->preinscripcion;
                $ammount_expire_bill = $estudiant->ammount_expire_bill;
                $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;
                $inscripcion = $estudiant->fullinscripcion ? $estudiant->fullinscripcion : null;
                $administrativa = $estudiant->administrativa;
                $enable_inscription = $ammount_expire_bill == 0 ? true : false;
                $status_administrativa = !empty($estudiant->administrativa->id);
                $plan_pago = $estudiant->plan_pago;
                $representant = $estudiant->representant;

                $prepagos = $representant->PrepagoDisponibles;
                $total_credito = $representant->total_credito > 0 ? $representant->total_credito : null;
                $total_abono = $representant->total_abono > 0 ? $representant->total_abono : null;
                $total_prepago = $representant->total_prepago > 0 ? $representant->total_prepago : null;
                $total_saf = $total_prepago + $total_credito + $total_abono;

                $total_credito_exchange = $representant->total_credito_exchange;
                $total_abono_exchange = $representant->total_abono_exchange;
                $total_saf_exchange = $total_credito_exchange + $total_abono_exchange;
                $ammount_bill_individuals = $estudiant->ammount_bill_individual;
            @endphp

            <tr data-estudiant="{{ $estudiant->id }}"
                class="table-{{ empty($estudiant->administrativa->id) ? 'default' : 'secondary font-weight-bold' }}"
                title="{{ $estudiant->status_blacklist == 'true' ? 'Este estudiante incumplió con el compromiso de pago en las fechas correspondientes.' : null }}">

                <td id="td-count" class="{{ $class_N }}">
                    {{ $loop->iteration }}
                </td>
                <td
                    class="{{ $class_user ?? '' }} {{ $estudiant->status_blacklist == 'true' ? 'text-danger' : null }}">
                    @admin
                        {{ $estudiant->id }}
                    @endadmin
                    <a class="btn-link"
                        href="{{ route('administracion.estudiants.index', ['search' => $estudiant->ci_estudiant]) }}">
                        {{ $estudiant->fullname }}
                    </a>
                    @if ($estudiant->status_blacklist == 'true')
                        <div class=" d-block text-danger">Este estudiante incumplió con el compromiso de pago en las
                            fechas correspondientes.</div>
                    @endif
                </td>
                <td class="{{ $class_email ?? '' }}">
                    {{ $estudiant->ci_estudiant ?? '' }}
                </td>

                <td
                    class="{{ $class_grado ?? '' }} text-center table-{{ $preinscripcion ? 'success' : 'danger' }} text-{{ $preinscripcion ? 'success' : 'danger' }}">
                    <span> {{ $preinscripcion ? $preinscripcion->getGradoName() : 'NO POSEE' }} </span> <br>
                    <span class="small"> {{ $preinscripcion ? $preinscripcion->getGrupoEtableCode() : null }}
                    </span>
                </td>

                <td class="{{ $class_grado ?? '' }} table-{{ $inscripcion ? 'success' : 'danger' }} text-center">
                    <span class=" text-{{ $inscripcion ? null : 'danger' }} font-weight-bold">
                        {{ $inscripcion ?? 'NO POSEE' }}
                    </span>
                </td>

                <td class="{{ $class_grado ?? '' }} table-{{ $total_saf ? 'success' : 'danger' }} text-right">
                    @if ($total_saf_exchange)
                        <span class="font-weight-bold">
                            <span class=" text-light bg-info p-1 border rounded-lg">
                                Bs. {{ f_float($total_saf, 2) ?? null }}
                            </span>
                            <span class=" text-light bg-dark p-1 border rounded-lg">
                                $ {{ round($total_saf_exchange, 2) ?? null }}
                            </span>
                        </span>
                    @endif
                </td>

                <td class="{{ $class_grado ?? '' }} text-right">
                    @include('elements.badges.exchange_ammount_expire_bill', [
                        'exchange_ammount_expire_bill' => $exchange_ammount_expire_bill,
                    ])
                </td>

                <td class="{{ $class_grado ?? '' }} table-{{ $administrativa ? 'success' : 'danger' }}">

                    @php $name = 'planpago_arr['.$estudiant->id.']'; @endphp
                    @php $value = (!empty($administrativa)) ? $administrativa->planpago->id:null; @endphp
                    {!! Form::select($name, $planpago_list, $value, [
                        'class' => 'm-1 p-1 btn btn-light text-left',
                        'id' => $name,
                        'placeholder' => '',
                    ]) !!}

                </td>

                {{-- <td class="{{ $class_grado ?? '' }} table-{{ ($administrativa) ? 'success':'danger' }}">

                    @php $name = 'descuentos_arr['.$estudiant->id.']'; @endphp
                    @php $value = (!empty($planbenefico)) ? $planbenefico->id:null; @endphp
                    {!! Form::select($name,$list_descuentos,$value,['class'=>'m-1 p-1 btn btn-light','id'=>$name,'placeholder'=>'']) !!}

                </td> --}}

                <td class="{{ $class_action ?? '' }}">
                    <div class="btn-group btn-group-sm">
                        @if ($administrativa)
                            <a title="Editar" class="btn btn-warning btn-sm"
                                href="{{ route('administracion.administrativas.edit', $estudiant->administrativa->id) }}"
                                role="button">
                                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                            </a>
                        @endif
                        <a class="btn-pago btn btn-light btn-sm"
                            href="{{ route('administracion.payments.crud', ['ci_representant' => $representant->ci_representant]) }}"
                            role="button" target="_blank">
                            <i class="{{ $icon_menus['prepagos'] }} fa-1x "></i>
                        </a>
                        <a title="Iniciar Asistente de Registro de Pagos.." class="btn btn-success btn-sm"
                            href="{{ route('administracion.registropagos.asistent.representant.create', ['id' => $representant->id]) }}"
                            role="button" target="_blank">
                            <i class="{{ $icon_menus['registro_pagos'] }} fa-1x"></i>
                        </a>
                        @if ($administrativa && $exchange_ammount_expire_bill <= 0)
                            <a title="Constacia de Inscripción Administrativa"
                                class="btn btn-dark btn-sm {{ $administrativa ? null : 'disabled' }}"
                                title="{{ $administrativa ? null : 'Sin inscripción administrativa' }}" target="_blank"
                                href="{{ route('administracion.administrativas.constancia.pdf', $estudiant->id) }}"
                                role="button">
                                <i class="{{ $icon_menus['pdf'] }} fa-1x"></i>
                            </a>
                        @endif
                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>
</table>
@php $disabled = (Session::get('pescolar_ffinal') < Carbon\Carbon::now()) ? true:false @endphp
<fieldset {{ $disabled ? 'disabled=disabled' : null }}>
    {!! Form::submit('Procesar inscripciones', [
        'class' => 'btn-create btn btn-primary btn-block',
        'id' => 'create',
    ]) !!}
</fieldset>

{!! Form::close() !!}

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.simple_search')
