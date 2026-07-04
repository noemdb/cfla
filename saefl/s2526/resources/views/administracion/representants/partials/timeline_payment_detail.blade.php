{{-- Conceptos pagados --}}
<div class="mb-3">
    <h6 class="text-primary mb-2">
        <i class="fas fa-list mr-1"></i> Conceptos Pagados:
    </h6>
    @foreach($pago_combinado->registropagos as $registropago)
        <div class="concept-item">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $registropago->cuentaxpagar->name ?? 'Sin concepto' }}</strong>
                    @if($registropago->estudiant)
                        <br><small class="text-muted">
                            <i class="fas fa-user mr-1"></i>
                            {{ $registropago->estudiant->name }} {{ $registropago->estudiant->lastname }}
                        </small>
                    @endif
                </div>
                <div class="text-right">
                    <span class="badge badge-success">
                        {{ number_format($registropago->pagos->sum('exchange_ammount'), 2) }} $
                    </span>
                    <br><small class="text-muted">
                        {{ number_format($registropago->pagos->sum('pagos_ammount'), 2) }} Bs
                    </small>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Recursos utilizados --}}
<div class="payment-resources">
    <h6 class="text-info mb-2">
        <i class="fas fa-coins mr-1"></i> Recursos Utilizados:
    </h6>

    @if($pago_combinado->ammount_ingresos > 0)
        <div class="mb-2">
            <span class="badge badge-primary mr-2">INGRESOS</span>
            <strong>{{ number_format($pago_combinado->ammount_ingresos_exchange, 2) }} $</strong>
            <small class="text-muted">({{ number_format($pago_combinado->ammount_ingresos, 2) }} Bs)</small>

            @if($pago_combinado->ingresos && $pago_combinado->ingresos->isNotEmpty())
                <div class="mt-1">
                    @foreach($pago_combinado->ingresos as $ingreso)
                        <small class="d-block text-muted ml-3">
                            • Ref: {{ $ingreso->number_i_pay ?? 'N/A' }} -
                            {{ number_format($ingreso->ammount_exchange ?? 0, 2) }} $
                        </small>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if(isset($pago_combinado->ammount_abonos_aplicados) && $pago_combinado->ammount_abonos_aplicados > 0)
        <div class="mb-2">
            <span class="badge badge-warning mr-2">ABONOS</span>
            <strong>{{ number_format($pago_combinado->ammount_abonos_aplicados_exchange, 2) }} $</strong>
            <small class="text-muted">({{ number_format($pago_combinado->ammount_abonos_aplicados, 2) }} Bs)</small>
        </div>
    @endif

    @if(isset($pago_combinado->ammount_creditos_aplicados) && $pago_combinado->ammount_creditos_aplicados > 0)
        <div class="mb-2">
            <span class="badge badge-info mr-2">CRÉDITOS</span>
            <strong>{{ number_format($pago_combinado->ammount_creditos_aplicados_exchange, 2) }} $</strong>
            <small class="text-muted">({{ number_format($pago_combinado->ammount_creditos_aplicados, 2) }} Bs)</small>
        </div>
    @endif

    <hr class="my-2">
    <div class="text-right">
        <strong class="text-success">
            Total Recursos: {{ number_format(
                ($pago_combinado->ammount_ingresos_exchange ?? 0) +
                ($pago_combinado->ammount_abonos_aplicados_exchange ?? 0) +
                ($pago_combinado->ammount_creditos_aplicados_exchange ?? 0), 2
            ) }} $
        </strong>
    </div>
</div>

{{-- Resumen del pago --}}
<div class="row">
    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-body p-3">
                <h6 class="card-title text-success mb-2">
                    <i class="fas fa-money-bill-wave mr-1"></i> Total Pagado
                </h6>
                <h5 class="text-success mb-0">
                    {{ number_format($pago_combinado->ammount_pagado_exchange ?? 0, 2) }} $
                </h5>
                <small class="text-muted">
                    {{ number_format($pago_combinado->ammount_pagado ?? 0, 2) }} Bs
                </small>
            </div>
        </div>
    </div>

    @if(($pago_combinado->ammount_creditos_generados_exchange ?? 0) > 0)
        <div class="col-md-6">
            <div class="credit-generated">
                <h6 class="text-warning mb-2">
                    <i class="fas fa-gift mr-1"></i> Crédito Generado
                </h6>
                <h5 class="text-warning mb-0">
                    {{ number_format($pago_combinado->ammount_creditos_generados_exchange ?? 0, 2) }} $
                </h5>
                <small class="text-muted">
                    {{ number_format($pago_combinado->ammount_creditos_generados ?? 0, 2) }} Bs
                </small>
            </div>
        </div>
    @endif
</div>

{{-- Información adicional --}}
<div class="mt-3 pt-3 border-top">
    <div class="row">
        <div class="col-md-6">
            <small class="text-muted">
                <i class="fas fa-user mr-1"></i>
                <strong>Procesado por:</strong> {{ $pago_combinado->user->username ?? 'Sistema' }}
            </small>
        </div>
        <div class="col-md-6 text-right">
            <small class="text-muted">
                <i class="fas fa-clock mr-1"></i>
                <strong>Fecha:</strong> {{ $pago_combinado->created_at->format('d/m/Y H:i:s') }}
            </small>
        </div>
    </div>

    {{-- Información de anulación si aplica --}}
    @if($pago_combinado->status == 'cancelled')
        <div class="mt-2">
            <div class="alert alert-danger alert-sm mb-0">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>Pago Anulado</strong>
            </div>
        </div>
    @endif
</div>
