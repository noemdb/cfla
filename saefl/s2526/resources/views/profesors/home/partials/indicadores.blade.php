
<div class="card mt-2">
    <div class="card-header pb-0 mb-0 alert-secondary">
        <h3>
            <i class="{{ $icon_menus['chartline'] }} fa-1x text-danger"></i>
            Indicadores
        </h3>
    </div>
    <div class="card-boby">

        <div class="row p-2">
            <div class="col-sm-12">
                @includeif('profesors.home.partials.boxes.pevaluacion')
            </div>
        </div>

        <hr>

        <div class="row p-2">
            <div class="col-sm-12">
                @include('profesors.evaluacions.chart.actividades')
            </div>
        </div>
    </div>
</div>
