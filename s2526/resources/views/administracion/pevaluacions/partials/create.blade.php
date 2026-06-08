<div class="col-sm-3">
    <h5 class="card-title">Resumen</h5>
    <div class="dropdown-divider mb-0"></div>
    @include('administracion.pevaluacions.partials.info')
</div>

<div class="col-sm-9">
    <div class="card-header alert-secondary p-1">
        <h6>Datos del Nuevo Plan de Evalauación</h6>
    </div>
    @include('administracion.pevaluacions.form.create')
</div>