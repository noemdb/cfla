<div class="modal fade" id="modal_cancellation_form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Anular Pago
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span class="font-weight-bold">Confirmar Anulación de Pago</span>
                    </div>
                    <p class="mb-0">
                        ¿Está seguro que desea anular el pago del estudiante 
                        <strong>{{ $registroPago->estudiant->fullname }}</strong>
                        por concepto de <strong>{{ $registroPago->cuentaxpagar->name }}</strong>?
                    </p>
                </div>

                <form id="form-cancel-payment" data-id="{{ $registroPago->id }}">
                    @csrf
                    <div class="form-group">
                        <label for="cancellation_reason" class="font-weight-bold">
                            Motivo de la anulación: <span class="text-danger">*</span>
                        </label>
                        <textarea id="cancellation_reason" 
                                  name="cancellation_reason" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Ingrese el motivo de la anulación..."
                                  required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-cancel">
                    <i class="fas fa-times mr-1"></i>
                    Anular Pago
                </button>
            </div>
        </div>
    </div>
</div>
