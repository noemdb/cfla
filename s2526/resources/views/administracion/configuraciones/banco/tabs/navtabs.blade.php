<nav class="pt-1 mt-1">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link pt-2 active" id="nav-tab01-tab" data-toggle="tab" href="#nav-tab01" role="tab" aria-controls="nav-tab01" aria-selected="true" title="Datos de la Transacción"><i class="fas fa-tasks text-info"></i> Listado</a>
        <a class="nav-item nav-link pt-2" id="nav-tab04-tab" data-toggle="tab" href="#nav-tab04" role="tab" aria-controls="nav-tab04" aria-selected="false" title="Descuentos a aplicar"><i class="fas fa-chart-bar "></i> Gráficas</a>
    </div>
</nav>

<div class="tab-content pt-1" id="nav-tabContent">
    <div class="tab-pane fade show active pl-2 pr-2" id="nav-tab01" role="tabpanel" aria-labelledby="nav-tab01-tab">
        @include('administracion.configuraciones.banco.table.libro')
        {{-- @include('administracion.datatables.exportBootstrap') --}}
    </div>

    <div class="tab-pane fade" id="nav-tab04" role="tabpanel" aria-labelledby="nav-tab04-tab">
        {{-- @include('administracion.configuraciones.banco.table.libro') --}}
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    @include('administracion.configuraciones.banco.chart.ingresoxmonth')
                </div>
                <div class="col-md-12 col-lg-6">
                    @include('administracion.configuraciones.banco.chart.ingresoxmetodo')
                </div>
            </div>
        </div>
    </div>
</div>
