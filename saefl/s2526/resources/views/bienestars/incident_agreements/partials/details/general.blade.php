<h4 class="alert mb-1 pb-1">Detalles Generales (Distribuciones porcentuales)</h4>
{{-- <div class="card-deck">
    <div class="card">
        <div class="card-body">
        <h5 class="card-title text-left pb-0 mb-0">Tipo de Incidencia</h5>
        <div class="text-muted font-weight-bold small"> ¿Qué tipos de incidencias son las mas frecuentes? </div>
        <p class="card-text">
            @include('bienestars.incidents.partials.details.types')
        </p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
        <h5 class="card-title text-left pb-0 mb-0">Motivo</h5>
        <div class="text-muted font-weight-bold small"> ¿Qué motivos son los mas frecuentes? </div>
        <p class="card-text">
            @include('bienestars.incidents.partials.details.reazons')
        </p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
        <h5 class="card-title text-left pb-0 mb-0">Agresividad presentada</h5>
        <div class="text-muted font-weight-bold small"> ¿Cuál es la distribución de las incidencias que presentaron agresividad? </div>
        <p class="card-text">
            @include('bienestars.incidents.partials.details.agresividad')
        </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title text-left pb-0 mb-0">Docentes con mas incidencias</h5>
            <div class="text-muted font-weight-bold small"> ¿Que docentes estan relacionados a las incidencias? </div>
            <p class="card-text">
                @include('bienestars.incidents.partials.details.profesors')
            </p>
        </div>
    </div>
</div> --}}

<div class="container-fluid">

    <div class="row mb-2">

        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 px-1 my-1">
            <div class="card h-100 mb-1">
                <div class="card-body">
                <h5 class="card-title text-left pb-0 mb-0">Tipo de Incidencia</h5>
                <div class="text-muted font-weight-bold small"> ¿Qué tipos de incidencias son las mas frecuentes? </div>
                <p class="card-text">
                    @include('bienestars.incidents.partials.details.types')
                </p>
                {{-- <p class="card-text text-right"><small class="text-muted">Last updated 3 mins ago</small></p> --}}
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 px-1 my-1">
            <div class="card h-100 mb-1">
                <div class="card-body">
                <h5 class="card-title text-left pb-0 mb-0">Motivo</h5>
                <div class="text-muted font-weight-bold small"> ¿Qué motivos son los mas frecuentes? </div>
                <p class="card-text">
                    @include('bienestars.incidents.partials.details.reazons')
                </p>
                {{-- <p class="card-text text-right"><small class="text-muted">Last updated 3 mins ago</small></p> --}}
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 px-1 my-1">
            <div class="card h-100 mb-1">
                <div class="card-body">
                <h5 class="card-title text-left pb-0 mb-0">Conducta disruptiva o desafiante</h5>
                <div class="text-muted font-weight-bold small"> ¿Cuál es la distribución de las incidencias que presentaron agresividad? </div>
                <p class="card-text">
                    @include('bienestars.incidents.partials.details.agresividad')
                </p>
                {{-- <p class="card-text text-right"><small class="text-muted">Last updated 3 mins ago</small></p> --}}
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 px-1 my-1">
            <div class="card h-100 mb-1">
                <div class="card-body">
                    <h5 class="card-title text-left pb-0 mb-0">Docentes con mas incidencias</h5>
                    <div class="text-muted font-weight-bold small"> ¿Que docentes estan más relacionados al registro de incidencias? </div>
                    <p class="card-text">
                        @include('bienestars.incidents.partials.details.profesors')
                    </p>
                    {{-- <p class="card-text text-right"><small class="text-muted">Last updated 3 mins ago</small></p> --}}
                </div>
            </div>
        </div>

    </div>

    {{-- <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 px-1 my-1">
            <div class="card h-100 mb-1">
                <div class="card-body">
                    <h5 class="card-title text-left pb-0 mb-0">Estudiantes con mas incidencias</h5>
                    <div class="text-muted font-weight-bold small"> ¿Que docentes estan más relacionados al registro de incidencias? </div>
                    <p class="card-text">
                        @include('bienestars.incidents.partials.details.profesors')
                    </p>
                </div>
            </div>
        </div>
    </div> --}}


</div>
