<!-- Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1" role="dialog" aria-labelledby="paymentDetailModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentDetailModalLabel">
                    <i class="fas fa-receipt"></i>
                    Detalle del Pago #{{ $selectedPayment->correlative ?? '' }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="closeModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($selectedPayment)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            @php
                                $isCancelled = $selectedPayment->deleted_at !== null;
                                $annulmentData = null;

                                if ($isCancelled) {
                                    $annulmentData = $selectedPayment->registropagos->first(function($registro) {
                                        return $registro->cancellable == 'true' &&
                                            ($registro->cancelled_at || $registro->approval_date || $registro->justification_annulment);
                                    });
                                }
                            @endphp



                            @if($isCancelled)
                                <!-- Alerta de Pago Anulado -->
                                <div class="alert alert-danger">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-ban"></i> PAGO ANULADO
                                    </h5>

                                    @if($annulmentData)
                                        @if($annulmentData->justification_annulment)
                                            <p><strong>Motivo:</strong> {{ $annulmentData->justification_annulment }}</p>
                                        @endif

                                        <div class="row">
                                            @if($annulmentData->cancelled_at)
                                                <div class="col-md-6">
                                                    <strong>Fecha de Anulación:</strong><br>
                                                    {{ \Carbon\Carbon::parse($annulmentData->cancelled_at)->format('d/m/Y H:i:s') }}
                                                </div>
                                            @endif
                                            @if($annulmentData->approval_date)
                                                <div class="col-md-6">
                                                    <strong>Fecha de Aprobación:</strong><br>
                                                    {{ \Carbon\Carbon::parse($annulmentData->approval_date)->format('d/m/Y H:i:s') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="row mt-2">
                                            @if($annulmentData->cancellation_user_id)
                                                <div class="col-md-6">
                                                    <strong>Anulado por Usuario ID:</strong> {{ $annulmentData->cancellation_user_id }}
                                                </div>
                                            @endif
                                            @if($annulmentData->approval_user_id)
                                                <div class="col-md-6">
                                                    <strong>Aprobado por Usuario ID:</strong> {{ $annulmentData->approval_user_id }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <h6>Información General</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Fecha:</strong></td>
                                    <td>{{ $selectedPayment->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Representante:</strong></td>
                                    <td>{{ $selectedPayment->representant->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>CI:</strong></td>
                                    <td>{{ $selectedPayment->representant->ci_representant ?? 'N/A' }}</td>
                                </tr>
                            </table>

                        </div>

                        <div class="col-md-6">
                            <h6>Resumen Financiero</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Total Recursos:</strong></td>
                                    <td class="text-right">
                                        ${{ number_format(($selectedPayment->ammount_ingresos_exchange ?? 0) + ($selectedPayment->ammount_abonos_aplicados_exchange ?? 0) + ($selectedPayment->ammount_creditos_aplicados_exchange ?? 0), 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Pagado:</strong></td>
                                    <td class="text-right">${{ number_format($selectedPayment->ammount_pagado_exchange ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>CAF Generado:</strong></td>
                                    <td class="text-right">${{ number_format($selectedPayment->ammount_creditos_generados_exchange ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                    </div>

                    <!-- Conceptos Pagados -->
                    <div class="mb-4">
                        <h6>Conceptos Pagados</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th>Estudiante</th>
                                        <th class="text-right">Monto Bs.</th>
                                        <th class="text-right">Monto $</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedPayment->registropagos as $registro)
                                        <tr>
                                            <td>{{ $registro->cuentaxpagar->name ?? 'N/A' }}</td>
                                            <td>{{ $registro->estudiant->fullname ?? 'N/A' }}</td>
                                            <td class="text-right">{{ number_format($registro->ammount ?? 0, 2) }}</td>
                                            <td class="text-right">${{ number_format($registro->exchange_ammount ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recursos Utilizados -->
                    <div class="row">
                        @php
                            $ingresos = $selectedPayment->ingresos ?? collect();
                        @endphp
                        @if($ingresos->count() > 0)
                            <div class="col-md-4">
                                <h6>Ingresos</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Banco</th>
                                                <th>Referencia</th>
                                                <th class="text-right">Monto $</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ingresos as $ingreso)
                                                <tr>
                                                    <td>{{ $ingreso->banco_name ?? 'N/A' }}</td>
                                                    <td>{{ $ingreso->number_i_pay ?? 'N/A' }}</td>
                                                    <td class="text-right">${{ number_format($ingreso->exchange_ammount ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @php
                            $abonos = $selectedPayment->abonos_aplicados ?? collect();
                        @endphp
                        @if($abonos->count() > 0)
                            <div class="col-md-4">
                                <h6>Abonos Aplicados</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Banco</th>
                                                <th>Referencia</th>
                                                <th class="text-right">Monto $</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($abonos as $abono)
                                                <tr>
                                                    <td>{{ $abono->banco_name ?? 'N/A' }}</td>
                                                    <td>{{ $abono->number_i_pay ?? 'N/A' }}</td>
                                                    <td class="text-right">${{ number_format($abono->ingresos_exchange_ammount ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @php
                            $creditos = $selectedPayment->creditos_aplicados ?? collect();
                        @endphp
                        @if($creditos->count() > 0)
                            <div class="col-md-4">
                                <h6>Créditos Aplicados</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Origen</th>
                                                <th>Referencia</th>
                                                <th class="text-right">Monto $</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($creditos as $credito)
                                                <tr>
                                                    <td>CAF #{{ $credito->id }}</td>
                                                    <td>{{ $credito->number_i_pay ?? 'N/A' }}</td>
                                                    <td class="text-right">${{ number_format($credito->exchange_ammount ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    @php
                        $creditosGenerados = $selectedPayment->creditos_generados ?? collect();
                    @endphp
                    @if($creditosGenerados->count() > 0)
                        <div class="mt-4">
                            <h6>Créditos a Favor Generados</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Estudiante</th>
                                            <th class="text-right">Monto Bs.</th>
                                            <th class="text-right">Monto $</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($creditosGenerados as $credito)
                                            <tr>
                                                <td>{{ $credito->id }}</td>
                                                <td>{{ $credito->estudiant->fullname ?? 'N/A' }}</td>
                                                <td class="text-right">{{ number_format($credito->credito_ammount ?? 0, 2) }}</td>
                                                <td class="text-right">${{ number_format($credito->exchange_ammount ?? 0, 2) }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $credito->status_omitted == 'true' ? 'secondary' : 'success' }}">
                                                        {{ $credito->status_omitted == 'true' ? 'Omitido' : 'Disponible' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" wire:click="closeModal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                @if($selectedPayment)
                    <a href="{{ route('administracion.representants.recibo.pdf', $selectedPayment->id) }}"
                        target="_blank"
                        class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
