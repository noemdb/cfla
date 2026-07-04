<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark border-bottom py-1">Resumen</p>
    <div class="px-1">
        <dl class="ml-1 pl-1">
            <dt>Descuentos registrados</dt>
            @foreach ($descuentos as $descuento)
                <dd class="small">
                    {{ $loop->iteration ?? '' }}.
                    {{$descuento->descuento_name}} - {{ (!empty($descuento->descuento_ammount)) ? f_float($descuento->descuento_ammount): '' }}
                </dd>
            @endforeach
        </dl>
    </div>
</div>

