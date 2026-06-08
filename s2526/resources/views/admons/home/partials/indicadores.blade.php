
<div class="card mt-2">
    <div class="card-header pb-0 mb-0 alert-secondary">
        <h3>
            <i class="{{ $icon_menus['chartline'] }} fa-1x text-danger"></i>
            Indicadores Principales
        </h3>
    </div>
    <div class="card-boby">

        <div class="row p-1">
            <div class="col-sm-12">
                @includeIf('directors.home.partials.labels.estudiantil')
            </div>
        </div>

        <hr class=" py-1">

        <div class="m-1 border rounded">
            <div class=" font-weight-bolder alert-secondary px-2 py-2">
                <i class="{{$icon_menus['banco'] ?? ''}}" aria-hidden="true"></i>
                Movimientos Bancarios: Ingresos registrados - Representantes deudores
            </div>
            <div class="row px-1">
                <div class="col-sm-12 col-xl-10 offset-xl-1">
                    @include('directors.charts.admon.bancos.all_ingresoxmonth')
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 col-xl-10 offset-xl-1">
                    @include('directors.charts.admon.bancos.deuda_representante_concepto')
                </div>
            </div>
        </div>

        <hr class=" py-1">

        <h5 class=" font-weight-bold">PLANES DE EVALUACIÓN</h5>
        <div class="row p-1">
            <div class="col-sm-12">
                @include('directors.charts.controls.evaluacions.actividades')
            </div>
        </div>

        <hr class=" py-1">

        <h5 class=" font-weight-bold">INSCRIPCIONES ESTUDIANTILES</h5>
        <div class="row p-1">
            <div class="col-xl-6">
                @include('directors.charts.controls.estudiants.estudiants_municipios')
            </div>
            <div class="col-xl-6">
                @include('directors.charts.controls.estudiants.estudiants_municipios_pestudio')
            </div>
        </div>

        <hr class=" py-1">
        <div class="row p-1">
            <div class="col-md-12 col-lg-6">
                @include('directors.charts.controls.inscripciones.inscritoxgenero')
            </div>
            <div class="col-md-12 col-lg-6">
                @include('directors.charts.controls.inscripciones.genderxplan')
            </div>
        </div>
        <hr class=" py-1">

    </div>
</div>
