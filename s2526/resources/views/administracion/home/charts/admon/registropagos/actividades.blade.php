<div class="pt-2 px-2">
    <div class=" alert alert-secondary p-1 text-center font-weight-bold">
        ¿Qué cantidad de registros de pagos realiza la Dirección de Admnistración diariamente?
    </div>
    <div class="px-2">
        @include('administracion.home.charts.admon.registropagos.partials.actividades')
    </div>
    <div class="text-muted">
        En la gráfica se puede ver el total de registro de pagos realizados diariamente por la Dirección de Administración
    </div>
</div>

<div class="pt-2 px-2">
    <div class=" alert alert-secondary p-1 text-center font-weight-bold">
        ¿Qué cantidad de registros de pagos realiza la Dirección de Admnistración diariamente por mes?
    </div>
    <div class="px-2">
        @include('administracion.home.charts.admon.registropagos.partials.activitiesxmonth')
    </div>
    <div class="text-muted">
        En la gráfica se puede ver el total de registro de pagos realizados diariamente por la Dirección de Administración
    </div>
</div>


