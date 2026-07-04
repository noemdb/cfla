<div class="ingreso-card">
    @php
        $isDeleted = $ingreso->deleted_at !== null;
    @endphp

    <div class="card-header bg-success text-white">
        <h6 class="mb-0">
            <i class="fas fa-arrow-down mr-2"></i>
            {{ $isDeleted ? 'Ingreso Anulado' : 'Registro de Ingreso' }}
            <small class="d-block mt-1">
                <i class="fas fa-calendar"></i>
                {{ $ingreso->date_transaction ? $ingreso->date_transaction->format('d/m/Y H:i') : 'N/A' }}
            </small>
        </h6>
    </div>

    <div class="card-body {{ $isDeleted ? 'bg-danger text-white' : '' }}">
        <div class="row">
            <div class="col-md-6">
                <strong>Número de Transacción:</strong><br>
                <span class="text-muted">{{ $ingreso->number_i_pay }}</span>
            </div>
            <div class="col-md-6">
                <strong>Banco:</strong><br>
                <span class="text-muted">{{ $ingreso->banco->name ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Fecha de Transacción:</strong><br>
                <span class="text-muted">
                    {{ $ingreso->date_transaction ? $ingreso->date_transaction->format('d/m/Y') : 'N/A' }}
                </span>
            </div>
            <div class="col-md-6">
                <strong>Fecha de Pago:</strong><br>
                <span class="text-muted">
                    {{ $ingreso->date_payment ? $ingreso->date_payment->format('d/m/Y') : 'N/A' }}
                </span>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Método de Pago:</strong><br>
                <span class="text-muted">{{ $ingreso->metodo_pago->name ?? 'N/A' }}</span>
            </div>
            <div class="col-md-6">
                <strong>Monto:</strong><br>
                <span class="badge badge-success">
                    ${{ number_format($ingreso->exchange_ammount ?? 0, 2) }}
                </span>
            </div>
        </div>

        @if ($ingreso->ingreso_observations)
            <div class="mt-3">
                <strong>Observaciones:</strong><br>
                <span class="text-muted font-italic">{{ $ingreso->ingreso_observations }}</span>
            </div>
        @endif
    </div>
</div>
