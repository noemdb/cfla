<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12 ml-4">

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 font-weight-bold text-dark">Guía de Indicadores Administrativos</h4>
            </div>
        </div>

        <!-- Introducción -->
        <section class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-coins text-primary-custom fa-2x mr-3"></i>
                <h5 class="mb-0 font-weight-bold">Introducción</h5>
            </div>

            <p class="text-muted lead" style="font-size: 1rem;">
                Los <strong>Indicadores Administrativos</strong> permiten visualizar de forma clara el comportamiento financiero de la institución,
                especialmente en relación con los ingresos, movimientos bancarios, fluctuaciones del tipo de cambio y la actividad diaria de los representantes
                al reportar pagos.
            </p>

            <div class="alert alert-light border-left-primary shadow-sm">
                <i class="fas fa-info-circle text-primary-custom mr-2"></i>
                <strong>Importante:</strong> La información presentada se actualiza constantemente con base en los registros realizados en el sistema,
                permitiendo una toma de decisiones confiable y oportuna.
            </div>
        </section>

        <!-- Indicadores Visuales -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Indicadores Visuales Clave</h5>

            <div class="row">
                <!-- Ingresos Bancarios -->
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm h-100">
                        <div class="icon-box bg-success-light text-success mr-3">
                            <i class="fas fa-university"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Ingresos Bancarios Mensuales</h6>
                            <p class="small text-muted mb-0">Total de montos en divisas registrados por los representantes durante cada mes del año.</p>
                        </div>
                    </div>
                </div>

                <!-- Métodos de Pago -->
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm h-100">
                        <div class="icon-box bg-info-light text-info mr-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Ingresos por Método de Pago</h6>
                            <p class="small text-muted mb-0">Distribución comparativa según el tipo de pago usado por los representantes (transferencia, pago móvil, depósito, etc.).</p>
                        </div>
                    </div>
                </div>

                <!-- Tipo de Cambio -->
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm h-100">
                        <div class="icon-box bg-warning-light text-warning mr-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Fluctuación Cambiaria</h6>
                            <p class="small text-muted mb-0">Variaciones del tipo de cambio BCV que afectan la conversión de divisas y la planificación financiera.</p>
                        </div>
                    </div>
                </div>

                <!-- Actividad de Reportes -->
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm h-100">
                        <div class="icon-box bg-danger-light text-danger mr-3">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Reportes de Pago por Día</h6>
                            <p class="small text-muted mb-0">Cantidad diaria de reportes ingresados al sistema, indicador clave del flujo operativo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Casos de Uso -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Casos de Uso Frecuentes</h5>

            <div class="accordion" id="useCasesAccordionAdmon">

                <!-- Caso 1 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="admonCase1">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#admonUseCase1">
                                <i class="fas fa-search-dollar text-primary-custom mr-2"></i>
                                Caso 1: Identificar meses con mayor o menor ingreso
                            </button>
                        </h6>
                    </div>

                    <div id="admonUseCase1" class="collapse show" data-parent="#useCasesAccordionAdmon">
                        <div class="card-body pt-0 text-muted small">
                            El gráfico mensual muestra picos y caídas en los ingresos. Esto permite:
                            <ul class="mt-2">
                                <li>Detectar meses de alta morosidad.</li>
                                <li>Medir el impacto de ajustes de cuota.</li>
                                <li>Comparar tendencias entre años consecutivos.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Caso 2 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="admonCase2">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#admonUseCase2">
                                <i class="fas fa-wallet text-primary-custom mr-2"></i>
                                Caso 2: Determinar métodos de pago más utilizados
                            </button>
                        </h6>
                    </div>

                    <div id="admonUseCase2" class="collapse" data-parent="#useCasesAccordionAdmon">
                        <div class="card-body pt-0 text-muted small">
                            Permite visualizar:
                            <ul class="mt-2">
                                <li>Las preferencias de los representantes.</li>
                                <li>Qué métodos requieren más soporte técnico o verificación.</li>
                                <li>Si un banco específico concentra demasiadas transacciones.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Caso 3 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="admonCase3">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#admonUseCase3">
                                <i class="fas fa-chart-line text-primary-custom mr-2"></i>
                                Caso 3: Evaluar impacto del tipo de cambio BCV
                            </button>
                        </h6>
                    </div>

                    <div id="admonUseCase3" class="collapse" data-parent="#useCasesAccordionAdmon">
                        <div class="card-body pt-0 text-muted small">
                            La fluctuación cambiaria afecta directamente:
                            <ul class="mt-2">
                                <li>La capacidad de pago de los representantes.</li>
                                <li>La recepción de divisas en días de alta volatilidad.</li>
                                <li>La planificación del presupuesto anual.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Caso 4 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="admonCase4">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#admonUseCase4">
                                <i class="fas fa-clock text-primary-custom mr-2"></i>
                                Caso 4: Análisis del flujo diario de operaciones
                            </button>
                        </h6>
                    </div>

                    <div id="admonUseCase4" class="collapse" data-parent="#useCasesAccordionAdmon">
                        <div class="card-body pt-0 text-muted small">
                            Permite descubrir:
                            <ul class="mt-2">
                                <li>Días con mayor demanda del sistema.</li>
                                <li>Posibles retrasos en la verificación de reportes.</li>
                                <li>Patrones de uso: fines de semana, inicio de mes, etc.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Caso 5 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="admonCase5">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#admonUseCase5">
                                <i class="fas fa-lightbulb text-primary-custom mr-2"></i>
                                Caso 5: Planificación financiera basada en tendencias
                            </button>
                        </h6>
                    </div>

                    <div id="admonUseCase5" class="collapse" data-parent="#useCasesAccordionAdmon">
                        <div class="card-body pt-0 text-muted small">
                            Los indicadores permiten estimar:
                            <ul class="mt-2">
                                <li>Ingresos proyectados para el próximo trimestre.</li>
                                <li>Carga operativa del personal administrativo.</li>
                                <li>Estacionalidad de pagos para ajustar campañas informativas.</li>
                            </ul>
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
                    <strong class="text-dark"><i class="fas fa-info-circle text-primary-custom mr-1"></i> Actividades relacionadas a pagos</strong>
                    Miden el comportamiento del usuario y ayudan a optimizar la atención administrativa.
                </li>

                <li class="mb-3">
                    <strong class="text-dark"><i class="fas fa-exchange-alt text-primary-custom mr-1"></i> Comparativas intermensuales</strong>
                    Permiten detectar mejoras, retrocesos y estabilidad en la recaudación.
                </li>

                <li class="mb-3">
                    <strong class="text-dark"><i class="fas fa-balance-scale text-primary-custom mr-1"></i> Distribución de ingresos por banco</strong>
                    Útil para identificar entidades saturadas o con mayor flujo transaccional.
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
        .bg-success-light { background-color: #d1fae5; }
        .bg-info-light { background-color: #e0f2fe; }
        .bg-warning-light { background-color: #fef3c7; }
        .bg-danger-light { background-color: #fee2e2; }

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
