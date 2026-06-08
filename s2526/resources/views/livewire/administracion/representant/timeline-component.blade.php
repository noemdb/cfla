<div>

    <!-- Loading Overlay Global -->
    <div wire:loading>
        <div class="position-fixed w-100 h-100 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; background: rgba(255,255,255,0.8); z-index: 9999;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            </div>
            <span class="m-2 p-2 font-weight-bold text-dark">Obteniendo información...</span>
        </div>
    </div>

    <!-- Buscador de Representante -->
    <div class="row mb-4">
        <div class="col-md-12">
            <label for="search" class="form-label">Buscar Representante</label>
            <div class="input-group">
                <input type="text" class="form-control" wire:model.debounce.300ms="search"
                    placeholder="Ingrese CI o nombre del representante...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            @if ($search && $this->representants->count() > 0)
                <div class="list-group mt-2" style="max-height: 200px; overflow-y: auto;">
                    @foreach ($this->representants as $rep)
                        <button type="button"
                            class="list-group-item list-group-item-action {{ $representant_id == $rep->id ? 'active' : '' }}"
                            wire:click="selectRepresentant({{ $rep->id }})">
                            <strong>{{ $rep->name }}</strong>
                            <small class="text-muted d-block">CI: {{ $rep->ci_representant }}</small>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if ($representant)
        <!-- Información del Representante Seleccionado -->
        <div class="alert alert-info">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-1">{{ $representant->name }}</h5>
                    <p class="mb-1"><strong>CI:</strong> {{ $representant->ci_representant }}</p>
                    <p class="mb-0"><strong>Teléfono:</strong> {{ $representant->contacts }}</p>
                </div>
                <div class="col-md-6">
                    {{-- @includeWhen($representant, 'administracion.elements.badges.bills_representant') --}}
                </div>
            </div>
        </div>

        <!-- Filtros de Fecha y Opciones -->
        <div class="row mb-3">
            <div class="col-md-5">
                <label for="dateFrom">Desde:</label>
                <input type="date" class="form-control" wire:model="dateFrom">
            </div>
            <div class="col-md-5">
                <label for="dateTo">Hasta:</label>
                <input type="date" class="form-control" wire:model="dateTo">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-secondary" wire:click="resetFilters">
                    <i class="fas fa-redo"></i> Resetear
                </button>
            </div>
        </div>

        <div class="continer-fluid ">
            <div class="row">
                <div class="col-4 px-2 border-1 rounded alert-info">
                    <div class="font-weight-bold">Ingresos registrados:</div>
                    <div class="timeline-container">

                        @php
                            // Obtener ingresos con filtros específicos
                            $ingresos = $representant->getIngresosWithTrashed(
                                $dateFrom, // desde
                                $dateTo, // hasta
                                null, // límite
                                'date_transaction', // ordenar por
                                'desc', // dirección
                            );
                        @endphp

                        @foreach ($ingresos as $ingreso)
                            @php
                                $isDeleted = $ingreso->deleted_at !== null;
                                $destino = $ingreso->destino;
                            @endphp

                            <div class="timeline-item {{ $ingreso->deleted_at ? 'alert-danger' : null }}"
                                wire:key="ingresos-{{ $ingreso->id }}">

                                <div class="timeline-marker">
                                    <i class="fas fa-credit-card text-primary"></i>
                                </div>

                                <div class="timeline-content">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-1 p-2 text-dark rounded">

                                        <table class="table table-sm border rounded">
                                            <tr class="alert-light text-black">
                                                <td class="font-weight-bold text-center" colspan="2">
                                                    <span class="text-muted d-block font-weight-bold">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $ingreso->created_at->format('d/m/Y H:i') }}
                                                </span>
                                                </td>
                                            </tr>
                                            @if ($ingreso->deleted_at)
                                                <tr>
                                                    <td class="font-weight-bold text-center" colspan="2">Registro de
                                                        Ingreso Anulado</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Número de Transacción:</strong></td>
                                                <td>{{ $ingreso->number_i_pay }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha de Transacción:</strong></td>
                                                <td>{{ $ingreso->date_transaction ? $ingreso->date_transaction->format('d/m/Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha de Pago:</strong></td>
                                                <td>{{ $ingreso->date_payment ? $ingreso->date_payment->format('d/m/Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Banco:</strong></td>
                                                <td>{{ $ingreso->banco->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Método de Pago:</strong></td>
                                                <td>{{ $ingreso->metodo_pago->name ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        @endforeach


                    </div>
                </div>
                <div class="col-8 px-2">
                    <div class="font-weight-bold text-center">Pagos registrados:</div>
                    <!-- Timeline de Pagos -->
                    <div class="timeline-container mx-2">
                        @if ($this->payments->count() > 0)
                            @foreach ($this->payments as $payment)
                                @php
                                    $isCancelled = $payment->deleted_at !== null;
                                    $annulmentData = $payment->registropagos->first();
                                @endphp

                                @if ($isCancelled)
                                    <!-- Alerta de Pago Anulado -->
                                    <div class="timeline-item" wire:key="payment-cancelled-{{ $payment->id }}">
                                        <div class="timeline-marker">
                                            {{-- <i class="fas fa-credit-card text-primary"></i> --}}
                                            <i class="fas fa-ban fa-2x text-danger"></i>
                                        </div>
                                        <div class="alert alert-danger mx-2">
                                            <h5 class="alert-heading">
                                                {{-- <i class="fas fa-ban"></i>  --}}
                                                PAGO ANULADO
                                            </h5>
                                            <span
                                                class="text-muted small">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i:s') ?? null }}</span>

                                            @if ($annulmentData)
                                                @if ($annulmentData->justification_annulment)
                                                    <p><strong>Motivo:</strong>
                                                        {{ $annulmentData->justification_annulment }}</p>
                                                @endif

                                                <div class="row">
                                                    @if ($annulmentData->cancelled_at)
                                                        <div class="col-md-6">
                                                            <strong>Fecha de Anulación:</strong><br>
                                                            {{ \Carbon\Carbon::parse($annulmentData->cancelled_at)->format('d/m/Y H:i:s') }}
                                                        </div>
                                                    @endif
                                                    @if ($annulmentData->approval_date)
                                                        <div class="col-md-6">
                                                            <strong>Fecha de Aprobación:</strong><br>
                                                            {{ \Carbon\Carbon::parse($annulmentData->approval_date)->format('d/m/Y H:i:s') }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="row mt-2">
                                                    @if ($annulmentData->cancellation_user_id)
                                                        <div class="col-md-6">
                                                            <strong>Anulado por Usuario:</strong>
                                                            {{ $annulmentData->cancellation->username }}
                                                        </div>
                                                    @endif
                                                    @if ($annulmentData->approval_user_id)
                                                        <div class="col-md-6">
                                                            <strong>Aprobado por Usuario:</strong>
                                                            {{ $annulmentData->approval->username }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- ------------------- --}}
                                @if (!$isCancelled)
                                    <div class="timeline-item" wire:key="payment-{{ $payment->id }}">
                                        <div class="timeline-marker">
                                            <i class="fas fa-credit-card text-primary"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="card">
                                                <div
                                                    class="card-header d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            {{-- <span class="badge badge-primary mr-2">#{{ $payment->correlative }}</span> --}}
                                                            {{-- Pago Combinado --}}
                                                            <span class="text-muted d-block font-weight-bold">
                                                                <i class="fas fa-calendar"></i>
                                                                {{ $payment->created_at->format('d/m/Y H:i') }}
                                                            </span>
                                                        </h6>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-info"
                                                            wire:click="showPaymentDetail({{ $payment->id }})">
                                                            <i class="fas fa-eye"></i> Ver Detalle
                                                        </button>
                                                        @if (!$isCancelled)
                                                            <a href="{{ route('administracion.representants.recibo.pdf', $payment->id) }}"
                                                                target="_blank" class="btn btn-sm btn-dark">
                                                                <i class="fas fa-file-pdf"></i> PDF
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <!-- Conceptos Pagados -->
                                                        <div class="col-md-6">
                                                            <h6 class="text-primary">
                                                                <i class="fas fa-list-check"></i> Conceptos Pagados
                                                            </h6>
                                                            <div class="mb-3">
                                                                @foreach ($payment->registropagos as $registro)
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                                                        <div>
                                                                            <strong
                                                                                class="text-dark">{{ $registro->cuentaxpagar->name ?? 'N/A' }}</strong>
                                                                            <br>
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-user-graduate"></i>
                                                                                {{ $registro->estudiant->fullname ?? 'N/A' }}
                                                                            </small>
                                                                        </div>
                                                                        <div class="text-right">
                                                                            <span class="badge badge-success">
                                                                                ${{ number_format($registro->exchange_ammount ?? 0, 2) }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <!-- Información de Ingresos -->
                                                        <div class="col-md-6">
                                                            <h6 class="text-success">
                                                                <i class="fas fa-money-bill-wave"></i> Detalles de
                                                                Ingresos
                                                            </h6>

                                                            @php
                                                                $ingresos = $payment->ingresos ?? collect();
                                                            @endphp

                                                            @if ($ingresos->count() > 0)
                                                                @foreach ($ingresos as $ingreso)
                                                                    <div class="card border-left-success mb-2">
                                                                        <div class="card-body p-2">
                                                                            <div class="row">
                                                                                <div class="col-8">
                                                                                    <div
                                                                                        class="d-flex align-items-center mb-1">
                                                                                        <i
                                                                                            class="fas fa-university text-success mr-2"></i>
                                                                                        <strong>{{ $ingreso->banco_name ?? 'N/A' }}</strong>
                                                                                    </div>
                                                                                    <div class="small text-muted">
                                                                                        <i class="fas fa-hashtag"></i>
                                                                                        <strong>Ref:</strong>
                                                                                        {{ $ingreso->number_i_pay ?? 'N/A' }}
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-4 text-right">
                                                                                    <div
                                                                                        class="text-success font-weight-bold">
                                                                                        ${{ number_format($ingreso->exchange_ammount ?? 0, 2) }}
                                                                                    </div>
                                                                                    <div class="small text-muted">
                                                                                        {{ number_format($ingreso->ingreso_ammount ?? 0, 2) }}
                                                                                        Bs.
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Fechas del Ingreso -->
                                                                            <div class="row mt-2">
                                                                                <div class="col-6">
                                                                                    <div class="small">
                                                                                        <i
                                                                                            class="fas fa-calendar-alt text-info"></i>
                                                                                        <strong>F. Banco:</strong>
                                                                                        <br>
                                                                                        <span class="text-muted">
                                                                                            {{ $ingreso->date_transaction ? \Carbon\Carbon::parse($ingreso->date_transaction)->format('d/m/Y') : 'N/A' }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <div class="small">
                                                                                        <i
                                                                                            class="fas fa-calendar-check text-primary"></i>
                                                                                        <strong>F. Pago:</strong>
                                                                                        <br>
                                                                                        <span class="text-muted">
                                                                                            {{ $ingreso->date_payment ? \Carbon\Carbon::parse($ingreso->date_payment)->format('d/m/Y') : 'N/A' }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Estado y Observaciones -->
                                                                            @if ($ingreso->status_late_payment == 'true')
                                                                                <div class="mt-2">
                                                                                    <span class="badge badge-warning">
                                                                                        <i
                                                                                            class="fas fa-exclamation-triangle"></i>
                                                                                        Pago Extemporáneo
                                                                                    </span>
                                                                                </div>
                                                                            @endif

                                                                            @if ($ingreso->ingreso_observations)
                                                                                <div class="mt-2">
                                                                                    <div class="small">
                                                                                        <i
                                                                                            class="fas fa-sticky-note text-warning"></i>
                                                                                        <strong>Observaciones:</strong>
                                                                                        <div
                                                                                            class="text-muted font-italic">
                                                                                            {{ Str::limit($ingreso->ingreso_observations, 100) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif

                                                                            @if ($ingreso->comment)
                                                                                <div class="mt-2">
                                                                                    <div class="small">
                                                                                        <i
                                                                                            class="fas fa-comment text-info"></i>
                                                                                        <strong>Comentario:</strong>
                                                                                        <div
                                                                                            class="text-muted font-italic">
                                                                                            {{ Str::limit($ingreso->comment, 100) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif

                                                            <!-- Abonos Aplicados -->
                                                            @php
                                                                $abonos = $payment->abonos_aplicados ?? collect();
                                                            @endphp
                                                            @if ($abonos->count() > 0)
                                                                <div class="mt-3">
                                                                    <h6 class="text-info">
                                                                        <i class="fas fa-piggy-bank"></i> Abonos
                                                                        Aplicados
                                                                    </h6>
                                                                    @foreach ($abonos as $abono)
                                                                        <div class="card border-left-info mb-2">
                                                                            <div class="card-body p-2">
                                                                                <div
                                                                                    class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <strong>{{ $abono->banco_name ?? 'N/A' }}</strong>
                                                                                        <br>
                                                                                        <small class="text-muted">
                                                                                            Ref:
                                                                                            {{ $abono->number_i_pay ?? 'N/A' }}
                                                                                        </small>
                                                                                    </div>
                                                                                    <div class="text-right">
                                                                                        <span
                                                                                            class="text-info font-weight-bold">
                                                                                            ${{ number_format($abono->ingresos_exchange_ammount ?? 0, 2) }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            <!-- Créditos Aplicados -->
                                                            @php
                                                                $creditos = $payment->creditos_aplicados ?? collect();
                                                            @endphp
                                                            @if ($creditos->count() > 0)
                                                                <div class="mt-3">
                                                                    <h6 class="text-warning">
                                                                        <i class="fas fa-coins"></i> Créditos Aplicados
                                                                    </h6>
                                                                    @foreach ($creditos as $credito)
                                                                        <div class="card border-left-warning mb-2">
                                                                            <div class="card-body p-2">
                                                                                <div
                                                                                    class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <strong>CAF
                                                                                            #{{ $credito->id }}</strong>
                                                                                        <br>
                                                                                        <small class="text-muted">
                                                                                            {{ $credito->number_i_pay ?? 'Crédito a Favor' }}
                                                                                        </small>
                                                                                    </div>
                                                                                    <div class="text-right">
                                                                                        <span
                                                                                            class="text-warning font-weight-bold">
                                                                                            ${{ number_format($credito->exchange_ammount ?? 0, 2) }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Resumen Financiero -->
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div class="card bg-light">
                                                                <div class="card-body p-3">
                                                                    <h6 class="text-dark mb-3">
                                                                        <i class="fas fa-calculator"></i> Resumen
                                                                        Financiero
                                                                    </h6>
                                                                    <div class="row">
                                                                        <div class="col-md-3 text-center">
                                                                            <div class="border-right">
                                                                                <h5 class="text-success mb-0">
                                                                                    ${{ number_format(($payment->ammount_ingresos_exchange ?? 0) + ($payment->ammount_abonos_aplicados_exchange ?? 0) + ($payment->ammount_creditos_aplicados_exchange ?? 0), 2) }}
                                                                                </h5>
                                                                                <small class="text-muted">Total
                                                                                    Recursos</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3 text-center">
                                                                            <div class="border-right">
                                                                                <h5 class="text-primary mb-0">
                                                                                    ${{ number_format($payment->ammount_pagado_exchange ?? 0, 2) }}
                                                                                </h5>
                                                                                <small class="text-muted">Total
                                                                                    Pagado</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3 text-center">
                                                                            <div class="border-right">
                                                                                <h5 class="text-warning mb-0">
                                                                                    ${{ number_format($payment->ammount_creditos_generados_exchange ?? 0, 2) }}
                                                                                </h5>
                                                                                <small class="text-muted">CAF
                                                                                    Generado</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3 text-center">
                                                                            <h5 class="text-info mb-0">
                                                                                {{ $payment->registropagos->count() }}
                                                                            </h5>
                                                                            <small class="text-muted">Conceptos</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Paginación -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $this->payments->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No se encontraron pagos</h5>
                                <p class="text-muted">No hay registros de pagos para el período seleccionado.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-user-search fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Seleccione un Representante</h5>
            <p class="text-muted">Use el buscador para encontrar y seleccionar un representante.</p>
        </div>
    @endif

    <!-- Modal de Detalle -->
    @if ($selectedPayment)
        @include('administracion.representants.modals.payment-detail')
    @endif
</div>

@section('stylesheet')
    @parent
    <style>
        .timeline-container {
            position: relative;
            padding-left: 30px;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            border: 2px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .timeline-content {
            margin-left: 20px;
        }

        .timeline-item:last-child .timeline-container::before {
            display: none;
        }

        /* Estilos adicionales para las tarjetas de ingresos */
        .border-left-success {
            border-left: 4px solid #28a745 !important;
        }

        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }

        .border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .timeline-content .row .col-md-6 {
                margin-bottom: 20px;
            }

            .timeline-content .card-body .row .col-md-3 {
                margin-bottom: 15px;
                border-right: none !important;
                border-bottom: 1px solid #dee2e6;
                padding-bottom: 10px;
            }

            .timeline-content .card-body .row .col-md-3:last-child {
                border-bottom: none;
            }
        }

        /* Mejorar la legibilidad */
        .small {
            font-size: 0.875rem;
        }

        .font-italic {
            font-style: italic;
        }

        .border-right {
            border-right: 1px solid #dee2e6;
        }

        /* Hover effects */
        .timeline-item .card {
            transition: all 0.3s ease;
        }

        .timeline-item .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
    </style>
@endsection

@section('livewireCustomeScripts')
    @parent
    <script>
        // Manejar eventos de Livewire para el modal
        window.addEventListener('show-payment-modal', event => {
            $('#paymentDetailModal').modal('show');
        });

        window.addEventListener('hide-payment-modal', event => {
            $('#paymentDetailModal').modal('hide');
        });

        // Asegurar que el modal se cierre completamente
        $(document).ready(function() {
            $('#paymentDetailModal').on('hidden.bs.modal', function() {
                // Remover cualquier backdrop que pueda quedar
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });

            // Manejar el cierre con la tecla ESC
            $(document).keyup(function(e) {
                if (e.keyCode === 27) { // ESC key
                    $('#paymentDetailModal').modal('hide');
                    @this.call('closeModal');
                }
            });
        });
    </script>
@endsection
