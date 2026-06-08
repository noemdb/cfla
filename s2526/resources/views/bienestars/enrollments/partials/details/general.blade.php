<h4 class="alert mb-1 pb-1">Detalles Generales (Distribuciones porcentuales)</h4>
<div class="container-fluid">

    <div class="row mb-2">

        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 px-1 my-1">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-left pb-0 mb-0">Transporte</h5>
                    <div class="text-muted font-weight-bold small"> ¿Qué medio de tranporte utiliza el escolar para llegar a la escuela? </div>
                    <p class="card-text">
                        @include('bienestars.student_records.partials.details.transportation')
                    </p>
                </div>
            </div>

            <hr>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-left pb-0 mb-0">Esquema de vacunación</h5>
                    <div class="text-muted font-weight-bold small"> ¿Cuál es porcentaje de cumplimiento del esquema de vacunación del escolar? </div>
                    <p class="card-text">
                        @include('bienestars.student_records.partials.details.vaccination')
                    </p>
                </div>
            </div>

        </div>

        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 px-1 my-1">

            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title text-left pb-0 mb-0">Potencial deportivo/cultural</h5>
                    <div class="text-muted font-weight-bold small"> ¿Cuáles son las tendencia de las potencialidades deportivas y culturales? </div>
                    <p class="card-text">
                        @include('bienestars.student_records.partials.details.potencial')
                    </p>
                </div>
            </div>

        </div>

    </div>
</div>

