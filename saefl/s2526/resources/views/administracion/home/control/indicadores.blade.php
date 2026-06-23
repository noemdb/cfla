<h4 class="text-center font-weight-bold text-uppercase">
    Control de Estudios
</h4>

<div class="card card-primary mt-2 border-0">

    <div class="card-body p-1 m-1">

        <h5 class=" font-weight-bold">RENDIMIENTO ESTUDIANTIL</h5>

        <nav>
            <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                <a class="nav-item nav-link show active text-left" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
                    POR PLANES DE ESTUDIO
                </a>
                <a class="nav-item nav-link text-left" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
                    POR AREAS DE CONOCIMIENTOS
                </a>
            </div>
        </nav>
        <div class="tab-content border border-top-0" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <div class="p-3">
                    @include('administracion.home.control.partials.pestudios')
                </div>
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="p-3">
                    @include('administracion.home.control.partials.area_conocimientos')
                </div>
            </div>
        </div>

    </div>
</div>

<hr class="py-1">

<h5 class=" font-weight-bold">PLANES DE EVALUACIÓN</h5>
<div class="row p-1">
    <div class="col-sm-12">
        @include('administracion.home.charts.controls.evaluacions.actividades')
    </div>
</div>
<hr class="py-1">

<hr class="py-1">

<h5 class=" font-weight-bold">INSCRIPCIONES ESTUDIANTILES</h5>
<div class="row p-1">
    <div class="col-xl-6">
        @include('administracion.home.charts.controls.estudiants.estudiants_municipios')
    </div>
    <div class="col-xl-6">
        @include('administracion.home.charts.controls.estudiants.estudiants_municipios_pestudio')
    </div>
</div>

