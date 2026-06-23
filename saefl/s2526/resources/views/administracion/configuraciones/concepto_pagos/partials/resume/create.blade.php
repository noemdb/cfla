<div class="text-right font-weight-bold card bd-callout bd-callout-primary">
    <p class="text-primary">Ùltimos conceptos de cobro registrados</p>
    <small class="px-1">
        @php $cuentaxpagars = $cuentaxpagars->take(3); @endphp
        @foreach ($cuentaxpagars as $cuentaxpagar)
        <span class=" font-weight-bolder">{{$loop->iteration}}</span>
        <dl class="mb-1 ">
            <dt>Plan de Pago: <span class="text-secondary">{{ $cuentaxpagar->planpago->name ?? '' }}</span></dt>
        </dl>
        <dl class="mb-1 ">
            <dt>Concepto de Cobro: <span class="text-secondary">{{ $cuentaxpagar->name ?? '' }}</span></dt>
        </dl>
        <dl class="mb-1 ">
            <dt>Fecha de Vencimiento: <span class="text-secondary">{{ f_date($cuentaxpagar->date_expiration) ?? '' }}</span></dt>
        </dl>
        {{-- <dl class="mb-1 ">
            <dt>Concepto de Cobro</dt>
            <dd class="text-secondary">{{ $cuentaxpagar->name ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>Fecha de Vencimiento</dt>
            <dd class="text-secondary">{{ $cuentaxpagar->date_expiration ?? '' }}</dd>
        </dl>
        <dl class="mb-1">
            <dt>Creado</dt>
            <dd class="text-secondary">{{ $created_at ?? '' }}</dd>
        </dl>
        <dl class="mb-1">
            <dt>Última vez actualizado</dt>
            <dd class="text-secondary">{{ $updated_at ?? '' }}</dd>
        </dl> --}}
        <hr>
        @endforeach
    </small>
</div>
