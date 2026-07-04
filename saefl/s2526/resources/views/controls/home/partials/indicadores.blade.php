
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
                @includeIf('controls.home.partials.labels.estudiantil')
            </div>
        </div>

        <hr class=" py-1">

        <h5 class=" font-weight-bold">PLANES DE EVALUACIÓN</h5>
        <div class="row p-1">
            <div class="col-sm-12">
                @include('controls.charts.controls.evaluacions.actividades')
            </div>
        </div>

        <hr class=" py-1">

        <h5 class=" font-weight-bold">INSCRIPCIONES ESTUDIANTILES</h5>
        <div class="row p-1">
            <div class="col-xl-6">
                @include('controls.charts.controls.estudiants.estudiants_municipios')
            </div>
            <div class="col-xl-6">
                @include('controls.charts.controls.estudiants.estudiants_municipios_pestudio')
            </div>
        </div>

        <hr class=" py-1">
        <div class="row p-1">
            <div class="col-md-12 col-lg-6">
                @include('controls.charts.controls.inscripciones.inscritoxgenero')
            </div>
            <div class="col-md-12 col-lg-6">
                @include('controls.charts.controls.inscripciones.genderxplan')
            </div>
        </div>
        <hr class=" py-1">

    </div>
</div>
