<dl class="mb-1 text-{{ (empty($credito_generado->deleted_at)) ? 'dark' : 'danger'}}">
    {{-- <dt>MONTO</dt> --}}
    <dd>Bs.
        <span id="credito_ammount" class="">
            {{f_float($credito_generado->credito_ammount)}}
            @php $exchange_ammount = $credito_generado->exchange_ammount; $exchange_rate_ammount = null; @endphp
            @include('administracion.elements.badges.exchange_rate',['exchange_rate_ammount'=>$exchange_rate_ammount,'exchange_ammount'=>$exchange_ammount])
        </span>
    </dd>
</dl>
