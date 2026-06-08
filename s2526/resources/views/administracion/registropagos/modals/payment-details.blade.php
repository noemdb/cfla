<div class="modal fade" id="modal_payment_details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Detalles del Registro de Pago #{{ $registroPago->id }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Estudiante:</label>
                            <p>{{ $registroPago->estudiant->fullname }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Cédula Estudiante:</label>
                            <p>{{ $registroPago->estudiant->ci_estudiant }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Cédula Representante:</label>
                            <p>{{ $registroPago->estudiant->representant->ci_representant }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Concepto:</label>
                            <p>{{ $registroPago->cuentaxpagar->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Monto Pagado:</label>
                            <p class="font-weight-bold text-success">
                                ${{ number_format($registroPago->pagos->sum('exchange_ammount'), 2) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Crédito Generado:</label>
                            <p class="font-weight-bold text-info">
                                ${{ number_format($registroPago->creditoafavor->exchange_ammount ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($registroPago->cancelled_at)
                    <hr>
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold text-danger mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Información de Anulación
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Fecha de Anulación:</label>
                                    <p>{{ $registroPago->cancelled_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @if($registroPago->approval_date)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha de Aprobación:</label>
                                        <p>{{ $registroPago->approval_date->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
