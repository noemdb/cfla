<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12 ml-4">

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 font-weight-bold text-dark">Guía de Indicadores Académicos</h4>
            </div>
        </div>

        <!-- Introducción -->
        <section class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-graduation-cap text-primary-custom fa-2x mr-3"></i>
                <h5 class="mb-0 font-weight-bold">Introducción</h5>
            </div>
            <p class="text-muted lead" style="font-size: 1rem;">
                Esta sección agrupa gráficos e indicadores clave que permiten evaluar el desempeño académico de la institución en tiempo real.
                Aquí se visualizan los promedios estudiantiles, niveles de cumplimiento de planes de evaluación, distribución por áreas del conocimiento,
                e información demográfica sobre las inscripciones activas.
            </p>
            <div class="alert alert-light border-left-primary shadow-sm">
                <i class="fas fa-info-circle text-primary-custom mr-2"></i>
                <strong>Importante:</strong> Los datos reflejados se actualizan automáticamente con base en los registros cargados por el personal docente y administrativo.
            </div>
        </section>

        <!-- Indicadores Visuales -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Indicadores Visuales Clave</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm h-100">
                        <div class="icon-box bg-danger-light text-danger mr-3">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Promedios por Área</h6>
                            <p class="small text-muted mb-0">Comparativa del rendimiento académico promedio por áreas de conocimiento y lapso académico.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm h-100">
                        <div class="icon-box bg-warning-light text-warning mr-3">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Evaluaciones Registradas</h6>
                            <p class="small text-muted mb-0">Cantidad y frecuencia de actividades evaluativas cargadas por el personal docente.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Casos de Uso -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Casos de Uso Frecuentes</h5>

            <div class="accordion" id="useCasesAccordionControl">

                <!-- Caso 1 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="controlCase1">
                        <h6 class="mb-0">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                    type="button" data-toggle="collapse" data-target="#controlUseCase1">
                                <i class="fas fa-search text-primary-custom mr-2"></i>
                                Caso 1: Evaluar rendimiento por área y lapso
                            </button>
                        </h6>
                    </div>
                    <div id="controlUseCase1" class="collapse show" data-parent="#useCasesAccordionControl">
                        <div class="card-body pt-0 text-muted small">
                            Analice si ciertas áreas muestran sistemáticamente promedios bajos. Seleccione un lapso para ver la variación temporal.
                        </div>
                    </div>
                </div>

                <!-- Caso 2 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="controlCase2">
                        <h6 class="mb-0">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                    type="button" data-toggle="collapse" data-target="#controlUseCase2">
                                <i class="fas fa-calendar-check text-primary-custom mr-2"></i>
                                Caso 2: Verificar cumplimiento de evaluaciones
                            </button>
                        </h6>
                    </div>
                    <div id="controlUseCase2" class="collapse" data-parent="#useCasesAccordionControl">
                        <div class="card-body pt-0 text-muted small">
                            Verifique que las evaluaciones se están cargando conforme al calendario. El gráfico de líneas permite detectar días con poca actividad.
                        </div>
                    </div>
                </div>

                <!-- Caso 3 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="controlCase3">
                        <h6 class="mb-0">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                    type="button" data-toggle="collapse" data-target="#controlUseCase3">
                                <i class="fas fa-map-marked-alt text-primary-custom mr-2"></i>
                                Caso 3: Analizar distribución geográfica de estudiantes
                            </button>
                        </h6>
                    </div>
                    <div id="controlUseCase3" class="collapse" data-parent="#useCasesAccordionControl">
                        <div class="card-body pt-0 text-muted small">
                            Utilice la gráfica de municipios para visualizar qué zonas aportan más estudiantes a la institución.
                        </div>
                    </div>
                </div>

                <!-- Caso 4 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="controlCase4">
                        <h6 class="mb-0">
                            <button class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                    type="button" data-toggle="collapse" data-target="#controlUseCase4">
                                <i class="fas fa-venus-mars text-primary-custom mr-2"></i>
                                Caso 4: Balance de género por grado
                            </button>
                        </h6>
                    </div>
                    <div id="controlUseCase4" class="collapse" data-parent="#useCasesAccordionControl">
                        <div class="card-body pt-0 text-muted small">
                            El gráfico de género permite verificar si existe equidad en la matrícula entre varones y mujeres, por cada plan o nivel.
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Indicadores Complementarios -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Indicadores Complementarios</h5>
            <ul class="list-unstyled text-muted small mb-0">
                <li class="mb-3">
                    <strong class="text-dark"><i class="fas fa-map-marker-alt text-primary-custom mr-1"></i> Estudiantes por Municipio</strong>
                    Revisión demográfica útil para estrategias de captación y cobertura territorial.
                </li>
                <li class="mb-3">
                    <strong class="text-dark"><i class="fas fa-chart-column text-primary-custom mr-1"></i> Inscripciones por Grado y Género</strong>
                    Permite visualizar patrones de matrícula, deserción o concentración por niveles.
                </li>
            </ul>
        </section>

    </div>
</div>

@section('stylesheet')
    @parent
    <style>
        :root {
            --primary-custom: #1c4517;
        }

        .text-primary-custom { color: var(--primary-custom) !important; }
        .bg-danger-light { background-color: #fee2e2; }
        .bg-warning-light { background-color: #fef3c7; }

        .icon-box {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .accordion .card { border-radius: 0.5rem; }
        .accordion .card-header button:focus { box-shadow: none; }
    </style>
@endsection
