@foreach ($descuentos_aplicados as $descuento_aplicado)
    <div class="">
        <dl class="mb-1">
            <dd>
                {{$descuento_aplicado->descuento->descuento_name ?? ''}}
                {{$descuento_aplicado->descuento->descuento_ammount}}%
            </dd>
        </dl>
    </div>
@endforeach
