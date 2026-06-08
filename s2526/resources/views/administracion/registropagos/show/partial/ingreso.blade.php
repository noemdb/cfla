{{-- <span class=" d-block font-weight-bold pt-2 mt-2">TRANSACCIÓN REALIZADA</span> --}}

{{-- {{$ingresos}} --}}

<div class="">
    {{-- @foreach ($ingreso as $ingreso) --}}

        <dl class="mb-1">
            <dt>MÉTODO DE PAGO</dt>
            <dd>
                <span id="metodo_pagos" class="">
                    {{-- {{$ingreso->method_pay_id ?? ''}} --}}
                    {{$ingreso->metodo_pago->name ?? ''}}
                </span>
            </dd>
        </dl>

        <dl class="mb-1">
            <dt>BANCO</dt>
            <dd>
                <span id="banco" class="">
                    {{$ingreso->banco->name ?? ''}}
                </span>
            </dd>
        </dl>

        <dl class="mb-1">
            <dt>REFERENCIA</dt>
            <dd>
                <span id="credito_a_ammount" class="">
                    {{$ingreso->number_i_pay ?? ''}}
                </span>
            </dd>
        </dl>

        <dl class="mb-1">
            <dt>FECHA DE PAGO</dt>
            <dd>
                <span id="date_transaction" class="">
                    {{ ($ingreso->date_payment) ? $ingreso->date_payment->format('d-m-Y') : null}}
                </span>
            </dd>
        </dl>
        <dl class="mb-1">
            <dt>FECHA EN BANCO</dt>
            <dd>
                <span id="date_transaction" class="">
                    {{ ($ingreso->date_transaction) ? $ingreso->date_transaction->format('d-m-Y') : null}}
                </span>
            </dd>
        </dl>
        <dl class="mb-1">
            <dt>MONTO</dt>
            <dd class=" text-nowrap">
                <span id="ingreso_ammount" class=""> Bs. {{ f_float($ingreso->ingreso_ammount) }} </span>
                @php
                    $exchange_ammount = $ingreso->exchange_ammount;
                    $exchange_rate_ammount = ($ingreso->exchange_rate) ? $ingreso->exchange_rate->ammount : null;
                @endphp
                @include('administracion.elements.badges.exchange_rate',['exchange_rate_ammount'=>$exchange_rate_ammount,'exchange_ammount'=>$exchange_ammount])
                @admin [{{ $ingreso->id ?? '' }}] @endadmin
            </dd>
        </dl>
        <dl class="mb-1">
            <dt>NOMBRE</dt>
            <dd>
                <span id="credito_a_ammount" class="">
                    {{$ingreso->person_bill_name ?? ''}}
                </span>
            </dd>
        </dl>
        <dl class="mb-1">
            <dt>CÉDULA</dt>
            <dd>
                <span id="credito_a_ammount" class="">
                    {{$ingreso->person_bill_ci ?? ''}}
                </span>
            </dd>
        </dl>
        <dl class="mb-1">
            <dt>OBS.</dt>
            <dd>
                <span id="credito_a_ammount" class="">
                    {{$ingreso->ingreso_observations ?? ''}}
                </span>
            </dd>
        </dl>

    {{-- @endforeach --}}
</div>
