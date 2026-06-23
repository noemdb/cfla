{{-- <span class=" d-block font-weight-bold pt-2 mt-2">CRÉDITOS APLICADOS</span> --}}

@foreach ($creditos_aplicados as $credito_aplicado)
    {{-- {{$credito_aplicado ?? 'fallo'}} --}}
    {{-- {{$creditoaplicado->credito_a_favor ?? ''}} --}}
    @php $credito = $credito_aplicado; @endphp
    {{-- @php $credito = $credito_aplicado->credito_trash; @endphp --}}

    <div class="">
        @admin
        <dl class="mb-1 ">
            {{-- <dt>Descripción</dt> --}}
            <dd>
                <span id="concepto_pago_id" class="">
                    Descripción: {{$credito->credito_description ?? ''}}
                </span>
            </dd>
        </dl>
        @endadmin

        {{-- <dl class="mb-1">
            <dt>OBSERVACIONES</dt>
            <dd>
                <span id="concepto_pago_observations" class="">
                    {{$credito_aplicado->credito_aplicado_observations ?? ''}}
                </span>
            </dd>
        </dl> --}}

        <dl class="mb-1 text-{{ (empty($credito->deleted_at)) ? 'dark' : 'danger'}}">
            {{-- <dt>MONTO</dt> --}}
            <dd>
                <span id="credito_ammount" class="">
                    Bs.{{ (!empty($credito->credito_ammount)) ? f_float($credito->credito_ammount):''}}
                </span>
                @php $exchange_ammount = $credito->exchange_ammount; $exchange_rate_ammount = null; @endphp
                @include('administracion.elements.badges.exchange_rate',['exchange_rate_ammount'=>$exchange_rate_ammount,'exchange_ammount'=>$exchange_ammount])
                <span class="border rounded table-light p-1 small text-dark float-right font-weight-bold">[{{$credito->id ?? null}}]</span>
            </dd>
        </dl>
    </div>

    <div class="dropdown-divider mb-0"></div>
@endforeach
