<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen - Últimos registros</p>
    <small class="px-1">
        @foreach ($exchange_rates as $exchange_rate)

            <div class="row align-items-center">
                <div class="col-2 text-right h-auto">
                    <span class=" font-weight-bold">{{$loop->iteration}}</span>
                </div>
                <div class="col-10">
                    <dl class="mb-1 ">
                        <dt>Fecha</dt>
                        <dd class="text-secondary">{{ ($exchange_rate->date) ? $exchange_rate->date->format('d-m-Y') : null }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>Monto</dt>
                        {{f_float($exchange_rate->ammount) }}
                    </dl>
                    <dl class="mb-1 ">
                        <dt>Moneda</dt>
                        <dd>{{ ($exchange_rate->currency) ? $exchange_rate->currency->name : null  }}</dd>
                    </dl>
                    <dl class="mb-1 ">
                        <dt>Moneda Referencial</dt>
                        <dd>{{ ($exchange_rate->currency_referential) ? $exchange_rate->currency_referential->name : null  }}</dd>
                    </dl>
                </div>
            </div>

            <hr>
        @endforeach
    </small>
</div>
