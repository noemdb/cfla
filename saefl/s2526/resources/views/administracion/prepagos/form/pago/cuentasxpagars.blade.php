<div class="font-weight-bolder border-bottom">
    CONCEPTOS DE COBRO
</div>

<dl class=" pl-1 pt-1">

    @foreach ($representant->estudiants as $estudiant)

        <span class=" border-bottom small">
            <i class="{{ $icon_menus['estudiante'] ?? '' }} fa-1x text-dark "></i>
            {{ $estudiant->fullname ?? ''}}
        </span>

        <dd class=" pl-1 pt-1">

            @php $estudiant_saldo = $estudiant->ammount_expire_bill @endphp

            @if ($estudiant_saldo > 0)

                <dl class="pl-1 small">

                    <dt class="font-weight-bolder py-1">CONCEPTOS || CUENTAS</dt>

                    @php $cuentaxpagars = $estudiant->expire_bill_pendientes @endphp

                    {{-- {{ $cuentaxpagars ?? '' }} --}}

                    @foreach ($cuentaxpagars as $cuentaxpagar)

                        @php $ammont = $cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id); @endphp

                        @if ($ammont > 0)

                            @php $name = 'cuentasxpagars['.$estudiant->id.']['.$cuentaxpagar->id.']'; @endphp
                            {{Form::hidden($name,$ammont)}}

                            @php
                                $conceptopagos = $cuentaxpagar->ConceptosXPagar($estudiant->id);
                                $conceptos_pagados = $cuentaxpagar->ConceptosPagados($estudiant->id);
                                $monto_descuento = $estudiant->descuento_ammount($cuentaxpagar->id);
                                $descuento = 1 - ($monto_descuento / 100);
                            @endphp

                            <dd class=" pl-1 pb-0">

                                <dl>

                                    {{-- <dt class="font-weight-bolder py-1">{{ $cuentaxpagar->name ?? ''}}</dt> --}}

                                    <dd class=" pl-1 pb-0">

                                        <dl>
                                            {{-- <dt class="font-weight-bolder py-1">CUENTAS</dt> --}}
                                            @foreach ($conceptopagos as $conceptopago)

                                                @php
                                                    unset($concepto_ammount);
                                                    $concepto_ammount_p = $conceptopago->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');
                                                    $concepto_ammount = $conceptopago->concepto_ammount - $concepto_ammount_p;
                                                @endphp
                                                @if ($conceptopago->status_discount == "true")
                                                    @php
                                                        $concepto_ammount_p = $conceptopago->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');
                                                        $concepto_ammount = ($conceptopago->concepto_ammount * $descuento) - $concepto_ammount_p;
                                                    @endphp
                                                @endif

                                                <dd class=" pl-1 pb-0">

                                                    <div class="input-group py-0">

                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">

                                                                @php $name = 'conceptopagos['.$estudiant->id.']['.$conceptopago->id.']'; @endphp
                                                                {{ Form::checkbox($name, $concepto_ammount, true,['class'=>'text-danger']) }}

                                                            </div>
                                                        </div>

                                                        <div class="form-control py-0 px-1">
                                                            <div class="small">
                                                                <span class=" font-weight-bold"> {{ $cuentaxpagar->name ?? ''}}</span> ||
                                                                <span class=" font-weight-light">{{$conceptopago->concepto_name ?? ''}} </span>
                                                                <span class=" badge badge-light float-right text-danger"> Bs. {{ f_float($concepto_ammount) ?? ''}} </span>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </dd>

                                            @endforeach

                                        </dl>

                                    </dd>

                                </dl>

                            </dd>

                        @endif

                    @endforeach

                </dl>

            @else
                <span class="badge badge-success float-right" title="SOLVENTE">
                    <i class="{{ $icon_menus['check'] }} fa-1x"></i>
                </span>
            @endif
        </dd>
    @endforeach
</dl>
