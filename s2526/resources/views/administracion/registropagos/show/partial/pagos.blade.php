{{-- <p class=" font-weight-bold pt-2 mt-2">PAGOS REALIZADOS</p> --}}

@foreach ($pagos as $pago)

    <dl class="mb-1">
        <dt>MONTO PAGADO</dt>
        <dd>
            <span id="pagos_ammount" class=" text-nowrap">
                Bs. {{f_float($pago->pagos_ammount)}}

                @php $exchange_ammount = $pago->exchange_ammount; $exchange_rate_ammount = null; @endphp
                @include('administracion.elements.badges.exchange_rate',['exchange_rate_ammount'=>$exchange_rate_ammount,'exchange_ammount'=>$exchange_ammount])
                @admin [{{$pago->id}}] @endadmin
            </span>
        </dd>
    </dl>

@endforeach
