{{-- <span class=" d-block font-weight-bold pt-2 mt-2">CONCEPTOS PAGADOS</span> --}}

@foreach ($conceptocancelados as $conceptocancelado)
@php
    $concepto_ammount = $conceptocancelado->concepto_ammount;
    $exchange_ammount = $conceptocancelado->exchange_ammount;
@endphp
<div class="">
    <dl class="mb-1">
        <dt>
            <span id="concepto_pago_id" class="">
                {{$conceptocancelado->concepto_pago->nomconceptopago->name ?? ''}}
            </span>
        </dt>
        {{-- <dt>NOMBRE</dt> --}}
        <dd>
            {{-- <span id="concepto_pago_id" class="">
                {{$conceptocancelado->concepto_pago->nomconceptopago->name ?? ''}}
            </span>
            <br> --}}

            <span id="credito_ammount" class="pt-0 mt-0">
                Bs. {{f_float($concepto_ammount)}}
                <span class=" table-secondary text-dark px-1 mx-1 border border-dark rounded shadow-sm">$ {{ round($exchange_ammount,2) }}</span>
                @php $cuentaxpagar = $conceptocancelado->concepto_pago->cuentaxpagar; @endphp
                @admin
                [{{ $conceptocancelado->concepto_pago->id ?? '' }}]
                [{{ $registropago->estudiant->id ?? '' }}]
                [{{ $registropago->estudiant->descuento_ammount($cuentaxpagar->id) ?? '' }}]
                @endadmin
            </span>
        </dd>
    </dl>

    {{-- <dl class="mb-1">
        <dt>OBSERVACIONES</dt>
        <dd>
            <span id="concepto_pago_observations" class="">
                {{$conceptocancelado->concepto_pago_observations ?? ''}}
            </span>
        </dd>
    </dl> --}}

    {{-- <dl class="mb-1">
        <dt>MONTO</dt>
        <dd>Bs.
            <span id="credito_ammount" class="pt-0 mt-0">
                {{f_float($conceptocancelado->concepto_pago->MontoConceptoDescuento($registropago->estudiant->id))}}
                @php
                   $cuentaxpagar = $conceptocancelado->concepto_pago->cuentaxpagar;
                @endphp
                @admin
                [{{ $conceptocancelado->concepto_pago->id ?? '' }}]
                [{{ $registropago->estudiant->id ?? '' }}]
                [{{ $registropago->estudiant->descuento_ammount($cuentaxpagar->id) ?? '' }}]
                @endadmin
            </span>
        </dd>
    </dl> --}}
</div>
<hr>
<div class="dropdown-divider mb-0"></div>
@endforeach
