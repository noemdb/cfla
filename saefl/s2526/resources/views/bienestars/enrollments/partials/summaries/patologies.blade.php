
<h4 class="alert mb-1 pb-1">Enfermedades y condiciones especiales (Distribuciones porcentuales)</h4>

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 px-1 my-1">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title text-left pb-0 mb-0">Enfermedades</h5>
                    <div class="text-muted font-weight-bold small"> ¿Cuales son las enfermedades graves más frecuentes? </div>
                    <p class="card-text">
                        @include('bienestars.enrollments.partials.summaries.diseases')
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 px-1 my-1">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title text-left pb-0 mb-0">Condiciones</h5>
                    <div class="text-muted font-weight-bold small"> ¿Condiciones especiales que presentan los escolares con más frecuentes? </div>
                    <p class="card-text">
                        @include('bienestars.enrollments.partials.summaries.conditions')
                    </p>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 px-1 my-1">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-body">
                    <div class="text-muted font-weight-bold small"> ¿Cuantos escolares estan recibiendo atención por algún especialista? </div>
                    <p class="card-text">
                        @include('bienestars.enrollments.partials.summaries.specialist')
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
