
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
                @includeIf('bienestars.home.partials.labels.estudiantil')
            </div>
        </div>

        <hr>

        <h5 class=" font-weight-bold">INSCRIPCIONES ESTUDIANTILES</h5>
        <div class="row p-1">
            <div class="col-xl-6">
                @include('bienestars.charts.controls.estudiants.estudiants_municipios')
            </div>
            <div class="col-xl-6">
                    @include('bienestars.charts.controls.inscripciones.inscritoxgenero')
            </div>
        </div>

        <hr class=" py-1">
        <div class="row p-1">
            <div class="col-12">                
                @include('bienestars.charts.controls.estudiants.estudiants_municipios_pestudio')
            </div>
            <div class="col-md-12 col-lg-6">
                {{-- @include('bienestars.charts.controls.inscripciones.genderxplan') --}}
            </div>
        </div>
        <hr class=" py-1">

        <div class="row p-1">
            <div class="col-12">
                @include('bienestars.charts.controls.evaluacions.actividades')
            </div>
        </div>
        <div class="row p-1">
            <div class="col-12">
                @include('bienestars.charts.controls.area_conocimientos.area_conocimientos')
                {{-- /home/nuser/code/s2223/resources/views/bienestars/charts/controls/area_conocimientos/area_conocimientos.blade.php --}}
            </div>
        </div>

    </div>
</div>
