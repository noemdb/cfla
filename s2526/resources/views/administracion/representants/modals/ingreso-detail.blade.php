<div class="modal fade" id="ingresoDetailModal" tabindex="-1" role="dialog" aria-labelledby="ingresoDetailModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="ingresoDetailModalLabel">
                    <i class="fas fa-money-bill-wave"></i> Detalle del Ingreso
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    wire:click="closeIngresoModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($selectedIngreso)
                    @php
                        $isDeleted = $selectedIngreso->deleted_at !== null;
                        $destino = $selectedIngreso->destino;
                    @endphp

                    @if ($isDeleted)
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">
                                <i class="fas fa-ban"></i> INGRESO ANULADO
                            </h5>
                            <p>Este ingreso ha sido eliminado del sistema.</p>
                            <small>Eliminado el: {{ $selectedIngreso->deleted_at->format('d/m/Y H:i:s') }}</small>
                        </div>
                    @endif

                    <!-- Información Principal -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-info-circle"></i> Información General</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Número de Transacción:</strong></td>
                                            <td>{{ $selectedIngreso->number_i_pay }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha de Transacción:</strong></td>
                                            <td>{{ $selectedIngreso->date_transaction ? $selectedIngreso->date_transaction->format('d/m/Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha de Pago:</strong></td>
                                            <td>{{ $selectedIngreso->date_payment ? $selectedIngreso->date_payment->format('d/m/Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Banco:</strong></td>
                                            <td>{{ $selectedIngreso->banco->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Método de Pago:</strong></td>
                                            <td>{{ $selectedIngreso->metodo_pago->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                @php
                                                    $destinoColor = match ($destino) {
                                                        'RPGO' => 'success',
                                                        'ABN' => 'warning',
                                                        'ABN-US' => 'info',
                                                        default => 'secondary',
                                                    };
                                                    $destinoText = match ($destino) {
                                                        'RPGO' => 'Aplicado en Pago',
                                                        'ABN' => 'Convertido a Abono',
                                                        'ABN-US' => 'Abono Usado',
                                                        default => 'No Destinado',
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $destinoColor }}">{{ $destinoText }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-dollar-sign"></i> Información Financiera</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Monto Original:</strong></td>
                                            <td>{{ number_format($selectedIngreso->ingreso_ammount ?? 0, 2) }}
                                                {{ $selectedIngreso->banco->currency->symbol ?? 'Bs' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tasa de Cambio:</strong></td>
                                            <td>{{ number_format($selectedIngreso->exchange_ammount_rate ?? 0, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Monto en USD:</strong></td>
                                            <td class="text-success font-weight-bold">
                                                ${{ number_format($selectedIngreso->exchange_ammount ?? 0, 2) }}</td>
                                        </tr>
                                        @if ($selectedIngreso->status_late_payment == 'true')
                                            <tr>
                                                <td><strong>Pago Extemporáneo:</strong></td>
                                                <td>
                                                    <span class="badge badge-warning">Sí</span>
                                                    <br>
                                                    <small>${{ number_format($selectedIngreso->exchange_ammount_late_payment ?? 0, 2) }}</small>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($selectedIngreso->invoice_number)
                                            <tr>
                                                <td><strong>Número de Recibo:</strong></td>
                                                <td>
                                                    <span
                                                        class="badge badge-primary">#{{ $selectedIngreso->invoice_number }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Personas -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fas fa-users"></i> Información de Personas</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Representante:</strong></td>
                                            <td>{{ $selectedIngreso->representant->name ?? 'N/A' }}</td>
                                        </tr>
                                        @if ($selectedIngreso->estudiant)
                                            <tr>
                                                <td><strong>Estudiante:</strong></td>
                                                <td>{{ $selectedIngreso->estudiant->fullname ?? 'N/A' }}</td>
                                            </tr>
                                        @endif
                                        @if ($selectedIngreso->person_bill_name)
                                            <tr>
                                                <td><strong>Titular de la Transferencia:</strong></td>
                                                <td>
                                                    {{ $selectedIngreso->person_bill_name }}
                                                    @if ($selectedIngreso->person_bill_ci)
                                                        <br><small>CI: {{ $selectedIngreso->person_bill_ci }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            @if ($selectedIngreso->terminal_pos || $selectedIngreso->approval_pos || $selectedIngreso->sequence_pos)
                                <div class="card">
                                    <div class="card-header">
                                        <h6><i class="fas fa-credit-card"></i> Información POS</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            @if ($selectedIngreso->terminal_pos)
                                                <tr>
                                                    <td><strong>Terminal:</strong></td>
                                                    <td>{{ $selectedIngreso->terminal_pos }}</td>
                                                </tr>
                                            @endif
                                            @if ($selectedIngreso->approval_pos)
                                                <tr>
                                                    <td><strong>Aprobación:</strong></td>
                                                    <td>{{ $selectedIngreso->approval_pos }}</td>
                                                </tr>
                                            @endif
                                            @if ($selectedIngreso->sequence_pos)
                                                <tr>
                                                    <td><strong>Secuencia:</strong></td>
                                                    <td>{{ $selectedIngreso->sequence_pos }}</td>
                                                </tr>
                                            @endif
                                            @if ($selectedIngreso->trace_pos)
                                                <tr>
                                                    <td><strong>Trace:</strong></td>
                                                    <td>{{ $selectedIngreso->trace_pos }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Observaciones y Comentarios -->
                    @if ($selectedIngreso->ingreso_observations || $selectedIngreso->comment)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6><i class="fas fa-comments"></i> Observaciones y Comentarios</h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($selectedIngreso->ingreso_observations)
                                            <div class="mb-3">
                                                <strong>Observaciones del Ingreso:</strong>
                                                <p class="text-muted">{{ $selectedIngreso->ingreso_observations }}</p>
                                            </div>
                                        @endif
                                        @if ($selectedIngreso->comment)
                                            <div>
                                                <strong>Comentario:</strong>
                                                <p class="text-muted">{{ $selectedIngreso->comment }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Información de Usuario -->
                    @if ($selectedIngreso->user)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6><i class="fas fa-user"></i> Información del Usuario</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Procesado por:</strong>
                                            {{ $selectedIngreso->user->username ?? 'N/A' }}</p>
                                        <p><strong>Fecha de creación:</strong>
                                            {{ $selectedIngreso->created_at->format('d/m/Y H:i:s') }}</p>
                                        @if ($selectedIngreso->updated_at != $selectedIngreso->created_at)
                                            <p><strong>Última actualización:</strong>
                                                {{ $selectedIngreso->updated_at->format('d/m/Y H:i:s') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" wire:click="closeIngresoModal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
