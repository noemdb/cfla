<h4 class="text-center font-weight-bold  text-uppercase">
    Administración
</h4>

<div class="row p-1">
    <div class="col-sm-12">
        @include('administracion.home.charts.admon.bancos.all_ingresoxmonth')
    </div>
</div>

<hr class="py-1">

<div class="row p-1">
    <div class="col-xl-6">
        @include('administracion.home.charts.admon.bancos.ingresoxmetodo')
    </div>
    <div class="col-xl-6">
        @include('administracion.home.charts.admon.payments.countxday')
    </div>
</div>

<div class="row p-1">
    <div class="col-sm-12">
        @include('administracion.home.charts.admon.exchange_rates.fluctuations')
    </div>
</div>

<div class="row p-1">
    <div class="col-sm-12">
        @include('administracion.home.charts.admon.registropagos.actividades')
    </div>
</div>


<hr class="py-1">
