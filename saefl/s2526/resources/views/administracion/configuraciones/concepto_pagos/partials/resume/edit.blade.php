<div class="text-right font-weight-bold card bd-callout bd-callout-{{ ($conceptopago->status_active == 'true') ? 'primary':'danger' }} px-1">
    <p class="text-primary">Resumen</p>
    <small class="px-1">
        <dl class="mb-1 ">
            <dt>Plan de Pago</dt>
            <dd class="text-secondary">{{ $conceptopago->cuentaxpagar->planpago->name ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>Concepto de Cobro</dt>
            <dd class="text-secondary">{{ $conceptopago->cuentaxpagar->name ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>Fecha de Vencimiento</dt>
            <dd class="text-secondary">{{ $conceptopago->cuentaxpagar->date_expiration ?? '' }}</dd>
        </dl>
        <dl class="mb-1">
            <dt>Creado</dt>
            <dd class="text-secondary">{{ $conceptopago->created_at ?? '' }}</dd>
        </dl>
        <dl class="mb-1">
            <dt>Última vez actualizado</dt>
            <dd class="text-secondary">{{ $conceptopago->updated_at ?? '' }}</dd>
        </dl>
    </small>
</div>
