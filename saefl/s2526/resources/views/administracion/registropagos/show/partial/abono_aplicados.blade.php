@foreach ($abonos_aplicados as $abono)

    {{-- @php $abono = $abonos_aplicado; @endphp --}}

    {{-- {{$abono}} --}}

    <div class="{{ (!empty($abono->ingresos_deleted_at)) ? 'text-danger':null}}"
        title="{{ (!empty($abono->ingresos_deleted_at)) ? 'Transferecia eliminada el '.f_date($abono->ingresos_deleted_at):null}}">
        <dl class="mb-1">
            <dt>{{$loop->iteration}}</dt>
            <dd>
                <span id="concepto_pago_id" class="">
                    <b>BANCO</b>: {{$abono->banco_name ?? ''}}
                </span>
            </dd>
        </dl>
        <dl class="mb-1">
            <dd>
                <span id="concepto_pago_id" class="">
                    <b>REFERENCIA</b>: {{$abono->number_i_pay ?? ''}}
                </span>
            </dd>
            <dd>
                <span id="concepto_pago_id" class="">
                    <b>FECHA DE PAGO.</b>: {{ ($abono->ingreso) ? $abono->ingreso->date_payment->format('d-m-Y') : null }}
                </span>
            </dd>
            <dd>
                <span id="concepto_pago_id" class="">
                    <b>FECHA EN BANCO.</b>: {{ f_date($abono->date_transaction) }}
                </span>
            </dd>
        </dl>
        <dl class="mb-1">
            <dd class=" text-nowrap">
                <span id="credito_ammount" class="">
                    <b>MONTO</b>: Bs. {{ f_float($abono->abono_ammount) }}
                </span>
                @php $exchange_ammount = $abono->exchange_ammount; $exchange_rate_ammount = $abono->exchange_rate_ammount; @endphp
            @include('administracion.elements.badges.exchange_rate',['exchange_rate_ammount'=>$exchange_rate_ammount,'exchange_ammount'=>$exchange_ammount])
            </dd>
        </dl>
        <dl class="mb-1">
            <dd>
                <span id="credito_ammount" class="">
                    <b>OBS:</b> {{$abono->ingreso->ingreso_observations ?? ''}}
                </span>
            </dd>
        </dl>
    </div>

    <div class="dropdown-divider mb-0"></div>
@endforeach
