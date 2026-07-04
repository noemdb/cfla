@php
    $isCancelled = $payment->deleted_at !== null;
    $annulmentData = $payment->registropagos->first();
@endphp

<div class="{{ $isCancelled ? 'pago-anulado' : 'pago-card' }}">
    @if ($isCancelled)
        <!-- Pago Anulado -->
        <div class="card-header bg-danger text-white">
            <h6 class="mb-0">
                <i class="fas fa-ban mr-2"></i>
                PAGO ANULADO
                <small class="d-block mt-1">
                    <i class="fas fa-calendar"></i>
                    {{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i:s') }}
                </small>
            </h6>
        </div>

        <div class="card-body">
            @if ($annulmentData && $annulmentData->justification_annulment)
                <p><strong>Motivo:</strong> {{ $annulmentData->justification_annulment }}</p>
            @endif

            <div class="row">
                @if ($annulmentData && $annulmentData->cancelled_at)
                    <div class="col-md-6">
                        <strong>Fecha de Anulación:</strong><br>
                        <span class="text-muted">
                            {{ \Carbon\Carbon::parse($annulmentData->cancelled_at)->format('d/m/Y H:i:s') }}
                        </span>
                    </div>
                @endif
                @if ($annulmentData && $annulmentData->approval_date)
                    <div class="col-md-6">
                        <strong>Fecha de Aprobación:</strong><br>
                        <span class="text-muted">
                            {{ \Carbon\Carbon::parse($annulmentData->approval_date)->format('d/m/Y H:i:s') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Pago Normal -->
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">
                        <span class="badge badge-light text-primary mr-2">#{{ $payment->correlative }}</span>
                        Pago Combinado
                        <small class="d-block mt-1">
                            <i class="fas fa-calendar"></i>
                            {{ $payment->created_at->format('d/m/Y H:i') }}
                        </small>
                    </h6>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-light text-primary"
                        wire:click="showPaymentDetail({{ $payment->id }})">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                    <a href="{{ route('administracion.representants.recibo.pdf', $payment->id) }}"
                        target="_blank" class="btn btn-sm btn-dark">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Conceptos Pagados -->
            <div class="mb-3">
                <h6 class="text-primary">
                    <i class="fas fa-list-check"></i> Conceptos Pagados
                </h6>
                @foreach ($payment->registropagos as $registro)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <strong>{{ $registro->cuentaxpagar->name ?? 'N/A' }}</strong><br>
                            <small class="text-muted">
                                <i class="fas fa-user-graduate"></i>
                                {{ $registro->estudiant->fullname ?? 'N/A' }}
                            </small>
                        </div>
                        <span class="badge badge-success">
                            ${{ number_format($registro->exchange_ammount ?? 0, 2) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Resumen Financiero -->
            <div class="card bg-light">
                <div class="card-body p-3">
                    <h6 class="text-dark mb-3">
                        <i class="fas fa-calculator"></i> Resumen Financiero
                    </h6>
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-success mb-0">
                                ${{ number_format(($payment->ammount_ingresos_exchange ?? 0) + ($payment->ammount_abonos_aplicados_exchange ?? 0) + ($payment->ammount_creditos_aplicados_exchange ?? 0), 2) }}
                            </h5>
                            <small class="text-muted">Total Recursos</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-primary mb-0">
                                ${{ number_format($payment->ammount_pagado_exchange ?? 0, 2) }}
                            </h5>
                            <small class="text-muted">Total Pagado</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
