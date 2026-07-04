<div class=" bd-callout bd-callout-{{ ($cuentaxpagar->status_edit) ? 'primary':'info' }} h-100">
    <div class="card p-0 h-100 bg-light">
        <div class="card-header p-1">
            <h6 class="font-weight-bold p-0 m-0 text-right">
                <span>Detalles</span>
            </h6>
        </div>
        <div class="card-body p-1">
            <dl class="mb-1 border-bottom">
                <dt class="">Nombre</dt>
                <dd class="">{{$cuentaxpagar->name ?? ''}}</dd>
            </dl>
            <dl class="mb-1 border-bottom">
                <dt class="">Plan de pago</dt>
                <dd class="">{{$cuentaxpagar->planpago->name ?? ''}}</dd>
            </dl>
            <dl class="mb-1 border-bottom">
                <dt class="">Tipo</dt>
                <dd class="">{{$cuentaxpagar->type ?? ''}}</dd>
            </dl>
            <dl class="mb-1 border-bottom">
                <dt class="">Descripción</dt>
                <dd class="">{{$cuentaxpagar->description ?? ''}}</dd>
            </dl>
            <dl class="mb-1 border-bottom">
                <dt class="">Observaciones</dt>
                <dd class="">{{$cuentaxpagar->observations ?? ''}}</dd>
            </dl>
        </div>

        @if (!$cuentaxpagar->status_edit)
            {{-- <hr> --}}
            <small class="text-muted font-weight-bold pt-2">Existen pagos registrados relacionados a éste concepto.</small>
        @endif
    </div>
</div>
