
<div class="card-body p-1 m-1">
    <nav>
        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
            <a class="nav-item nav-link show active text-left" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
                Total ingresos por Método de Pago - Reportes de pagos diarios (Representantes)
            </a>
            <a class="nav-item nav-link text-left" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
                Fluctuación Cambiaria BCV
            </a>
        </div>
    </nav>
    <div class="tab-content border border-top-0" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-6">
                        @include('academicos.charts.admon.bancos.ingresoxmetodo')
                    </div>
                    <div class="col-xl-6">
                        @include('academicos.charts.admon.payments.countxday')
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" >
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-10 offset-xl-1" data-toggle="modal">
                        @include('academicos.charts.admon.exchange_rates.fluctuations')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row p-1">
    <div class="col-xl-10 offset-xl-1" data-toggle="modal">
        @include('academicos.charts.admon.registropagos.actividades')
    </div>
</div>

{{-- @include('academicos.charts.admon.exchange_rates.fluctuations') --}}

{{-- <div class="card-body p-1 m-1">

    <div class="card">
        <div class="card-header alert-primary font-weight-bolder" role="alert">
            Movimientos Bancarios: Ingresos Por Método de Pago - Representantes deudores
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        @include('academicos.charts.admon.bancos.ingresoxmetodo')
                    </div>
                    <div class="col-sm-6">
                        @include('academicos.charts.admon.payments.countxday')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header alert-primary font-weight-bolder" role="alert">
            Fluctuación Cambiaria BCV
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @include('academicos.charts.admon.exchange_rates.fluctuations')
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> --}}
