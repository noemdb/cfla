<div class="text-right font-weight-bold card bd-callout bd-callout-{{ ($planpago->status_active == 'true') ? 'primary':'danger' }} px-1">
    <p class="text-dark">Resumen</p>
    <small class="px-1">
        <dl class="mb-1 ">
            <dt>Estudiantes asignados a éste plan</dt>
            <dd class="text-secondary">{{ $planpago->administrativas->count() ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>Conceptos de Cobro</dt>
            <dd class="text-secondary">{{ $planpago->cuentaxpagars->where('type','GENERAL')->count() ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>Cuentas de Cobro</dt>
            <dd class="text-secondary">{{ $planpago->conceptopagos->count() ?? '' }}</dd>
        </dl>
        <dl class="mb-1">
            <dt>Última vez actualizado</dt>
            <dd class="text-secondary">{{ $planpago->updated_at ?? '' }}</dd>
        </dl>
    </small>
</div>
