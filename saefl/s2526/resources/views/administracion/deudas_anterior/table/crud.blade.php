@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="text-nowrap";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell";
    $class_contacto="d-none d-lg-table-cell";
    $class_action="";
    $monto_total = null;
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_ci }}">Represemtamte</th>
                <th class="{{ $class_ci }}">Identificador</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_contacto }}">Cuenta de Cobro</th>
                <th class="{{ $class_contacto }}">Monto($)</th>
                <th class="{{ $class_contacto }}">Saldo($)</th>
                <th class="{{ $class_contacto }} align-middle text-center">Solvente</th>
                <th class="{{ $class_contacto }}">Activo</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">
            @php
                $ammount_exchange_total = 0;
                $count = 0;
            @endphp
            @foreach($cuentaxpagars as $cuentaxpagar)

                @if (!empty($cuentaxpagar->estudiant))
                    @php
                        $estudiant = $cuentaxpagar->estudiant;
                        $representant = ($estudiant) ? $estudiant->representant : null;
                        //$ammount = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                        $ammount_exchange = $cuentaxpagar->TotalExchangeMontoCuentasXPagar($estudiant->id);
                        $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill
                    @endphp

                    {{-- @if ($exchange_ammount_expire_bill > 0) --}}

                        @php $ammount_exchange_total = $ammount_exchange_total + $ammount_exchange; $count++; @endphp

                        {{-- <tr data-estudiant_id="{{$estudiant->id ?? ''}}" class="table-{{ ($pagado) ? 'secondary':''}}"> --}}
                        <tr data-estudiant_id="{{$estudiant->id ?? ''}}">

                                <td id="td-count" class="{{ $class_N ?? '' }}">
                                    {{-- {{$loop->iteration}} --}}
                                    {{$cuentaxpagar->id}}
                                </td>

                                <td class="{{ $class_estudiant }}">
                                    @if($representant)
                                        <a class="btn-link text-dark" href="{{ route('administracion.representants.index',['search'=>$representant->ci_representant]) }}"><b>{{$representant->name}}</b></a>
                                    @endif
                                </td>

                                <td id="td-estudiant" class="{{ $class_ci ?? '' }}">
                                    <b> {{ $estudiant->ci_estudiant ?? ''}}</b>
                                </td>

                                <td id="td-estudiant-{{ $estudiant->name ?? ''}}" class="{{ $class_estudiant  ?? ''}}">
                                    <b>{{$estudiant->fullname ?? ''}}</b>
                                </td>
                                <td id="td-cuentaxpagar-{{ $cuentaxpagar->id ?? ''}}" class="{{ $class_estudiant  ?? ''}}">
                                    {{-- <b>{{$estudiant->fullname ?? ''}}</b> --}}

                                    @foreach ($cuentaxpagar->conceptopagos as $conceptopago)
                                        <span>{{Str::limit($conceptopago->concepto_description,24) ?? ''}}</span><br>
                                    @endforeach

                                </td>

                                <td id="td-ammount_bill-{{ $estudiant->id ?? '' }}" class="{{ $class_contacto ?? '' }} align-middle">
                                    {{ f_number($ammount_exchange) }}
                                </td>

                                <td id="td-ammount_bill-{{ $estudiant->id ?? '' }}" class="{{ $class_contacto ?? '' }} align-middle">
                                    {{ f_number($exchange_ammount_expire_bill) }}
                                </td>

                                <td id="td-ammount_bill-{{ $estudiant->id ?? '' }}" class="{{ $class_contacto ?? '' }} align-middle text-center">
                                    {{ ($exchange_ammount_expire_bill > 0 ) ? 'NO-SOLVENTE' :  'SI-SOLVENTE' }}
                                </td>
                                <td class="{{ $class_contacto ?? '' }} align-middle">
                                    @if($representant)
                                        {{ ($representant->enable) ? "SI":"NO" }}
                                    @endif
                                </td>

                                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                                <a title="Editar" class="btn btn-warning btn-sm disabled" href="{{ route('administracion.deudas_anterior.edit',$cuentaxpagar->id) }}" role="button">
                                        <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                                    </a>
                                    {{-- @php
                                        $id_modal = 'modal_deudas_anterior_'.$estudiant->id;
                                    @endphp
                                    <button title="Editar monto" type="button" class="btn btn-warning " data-toggle="modal" data-target="#{{$id_modal}}" data-whatever="@mdo">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </button>
                                    @include('administracion.deudas_anterior.modal.edit',['id'=>$cuentaxpagar->id,'id_modal'=>$id_modal]) --}}
                                </td>

                        </tr>

                    {{-- @endif --}}
                @endif

            @endforeach

        </tbody>
    </table>

    <hr>

    @if($ammount_exchange_total)
        <div class=" bg-light pb-1 font-weight-bold d-block alert-dark">
            <div class="float-right d-block">
                Total General: <span>$ {{f_number($ammount_exchange_total) ?? ''}}</span> || Nº <span>{{ $count ?? ''}}</span>
            </div>
        </div>
    @endif

{{-- </div> --}}

@include('administracion.datatables.exportBootstrap')
