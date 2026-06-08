<div class="text-left">
    <p class="mb-3"><strong>Se detectó un cambio en el plan de pago del estudiante:</strong></p>
    
    <div class="alert alert-warning mb-3">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-exchange-alt"></i> Plan Actual:</h6>
                <p class="mb-1"><strong>{{ $planActualNombre }}</strong></p>
                <small class="text-muted">ID: {{ $planActualId }}</small>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-arrow-right"></i> Nuevo Plan:</h6>
                <p class="mb-1"><strong>{{ $planNuevoNombre }}</strong></p>
                <small class="text-muted">ID: {{ $planNuevoId }}</small>
            </div>
        </div>
    </div>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Nota:</strong> Esta acción cambiará la configuración administrativa del estudiante 
        y afectará su inscripción académica. ¿Desea continuar?
    </div>
</div>