<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12 ml-4">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 font-weight-bold text-dark">Guía de Listado de Deudores por Cuota</h4>
            </div>
        </div>

        <!-- Introducción -->
        <section class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-file-invoice-dollar text-primary-custom fa-2x mr-3"></i>
                <h5 class="mb-0 font-weight-bold">Introducción</h5>
            </div>
            <p class="text-muted lead" style="font-size: 1rem;">
                El módulo de <strong>Deudores por Cuota</strong> es una herramienta administrativa diseñada para
                identificar y gestionar la morosidad de forma específica.
                Permite generar listados de representantes con pagos pendientes para un <strong>mes o cuota
                    específica</strong>, facilitando el seguimiento de cobranza puntual.
            </p>
            <div class="alert alert-light border-left-primary shadow-sm">
                <i class="fas fa-info-circle text-primary-custom mr-2"></i>
                <strong>Importante:</strong> Para obtener resultados, es obligatorio seleccionar la <strong>Cuota
                    (Mensualidad)</strong> que desea auditar. El sistema calculará la deuda basándose en el rango de
                fechas de dicho mes.
            </div>
        </section>

        <!-- Indicadores Visuales -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Indicadores de Resumen</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm h-100">
                        <div class="icon-box bg-danger-light text-danger mr-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Total de Deuda ($)</h6>
                            <p class="small text-muted mb-0">Muestra la sumatoria total de la deuda pendiente
                                exclusivamente para la cuota seleccionada.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center p-3 border rounded bg-white shadow-sm h-100">
                        <div class="icon-box bg-info-light text-info mr-3">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-1">Porcentaje de Morosidad</h6>
                            <p class="small text-muted mb-0">Indica qué porcentaje de la población consultada
                                (Grado/Sección)
                                tiene deudas pendientes para ese mes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Casos de Uso -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Casos de Uso Frecuentes</h5>

            <div class="accordion" id="useCasesAccordion">
                <!-- Caso 1 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="case1">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#useCase1">
                                <i class="fas fa-calendar-alt text-primary-custom mr-2"></i>
                                Caso 1: Auditoría de un Mes Específico
                            </button>
                        </h6>
                    </div>
                    <div id="useCase1" class="collapse show" data-parent="#useCasesAccordion">
                        <div class="card-body pt-0 text-muted small">
                            <ol class="pl-3 mb-0">
                                <li>Diríjase al campo <strong>Cuotas</strong>.</li>
                                <li>Seleccione el mes que desea verificar (ej. "Septiembre", "Octubre").</li>
                                <li>Deje los campos de Grado y Sección vacíos para una consulta general.</li>
                                <li>Haga clic en <strong>Buscar</strong>.</li>
                                <li>El sistema listará todos los representantes que deben ese mes específico.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Caso 2 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="case2">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#useCase2">
                                <i class="fas fa-chalkboard-teacher text-primary-custom mr-2"></i>
                                Caso 2: Seguimiento por Salón
                            </button>
                        </h6>
                    </div>
                    <div id="useCase2" class="collapse" data-parent="#useCasesAccordion">
                        <div class="card-body pt-0 text-muted small">
                            <p>Para verificar la solvencia de un grupo específico:</p>
                            <ol class="pl-3 mb-0">
                                <li>Seleccione el <strong>Grado</strong> y espere a que cargue la lista de secciones.
                                </li>
                                <li>Seleccione la <strong>Sección</strong> deseada.</li>
                                <li>Seleccione obligatoriamente la <strong>Cuota</strong> a consultar.</li>
                                <li>Haga clic en <strong>Buscar</strong>.</li>
                                <li>Obtendrá el listado filtrado solo para ese salón.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Detalles del Formulario -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Detalles del Formulario de Búsqueda</h5>
            <div class="card card-custom border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled text-muted small mb-0">
                                <li class="mb-3">
                                    <strong class="d-block text-dark"><i
                                            class="fas fa-graduation-cap mr-1 text-primary-custom"></i> Grado</strong>
                                    Filtra los resultados por nivel académico. Al seleccionarlo, se habilitará el campo
                                    de
                                    Sección.
                                </li>
                                <li class="mb-3">
                                    <strong class="d-block text-dark"><i
                                            class="fas fa-users mr-1 text-primary-custom"></i> Sección</strong>
                                    Permite acotar la búsqueda a una sección específica del grado seleccionado.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled text-muted small mb-0">
                                <li class="mb-3">
                                    <strong class="d-block text-dark"><i
                                            class="fas fa-check-circle mr-1 text-primary-custom"></i>
                                        Formalizado</strong>
                                    <span class="badge badge-light border mr-1">SI</span> Estudiantes inscritos
                                    formalmente.<br>
                                    <span class="badge badge-light border">NO</span> Estudiantes en proceso o no
                                    formalizados.
                                </li>
                                <li class="mb-3">
                                    <strong class="d-block text-dark"><i
                                            class="far fa-calendar-check mr-1 text-primary-custom"></i> Cuotas
                                        (Requerido)</strong>
                                    Seleccione el concepto de pago o mes. Es el parámetro principal para calcular la
                                    deuda.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="mb-5">
            <h5 class="font-weight-bold mb-4">Preguntas Frecuentes</h5>
            <div class="accordion" id="faqAccordion">
                <!-- FAQ 1 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="faq1">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#answer1">
                                <i class="fas fa-question-circle text-primary-custom mr-2"></i>
                                ¿Por qué no aparecen resultados al buscar?
                            </button>
                        </h6>
                    </div>
                    <div id="answer1" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body pt-0 text-muted small">
                            Asegúrese de haber seleccionado una <strong>Cuota</strong>. Si no selecciona un mes, el
                            sistema
                            no tiene un rango de fechas para calcular la deuda y no mostrará resultados. También es
                            posible
                            que no existan deudas para los filtros seleccionados.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="card border-0 mb-2 shadow-sm">
                    <div class="card-header bg-white border-0 p-3" id="faq2">
                        <h6 class="mb-0">
                            <button
                                class="btn btn-link btn-block text-left text-dark font-weight-bold p-0 text-decoration-none"
                                type="button" data-toggle="collapse" data-target="#answer2">
                                <i class="fas fa-question-circle text-primary-custom mr-2"></i>
                                ¿Qué significa el porcentaje de morosidad?
                            </button>
                        </h6>
                    </div>
                    <div id="answer2" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body pt-0 text-muted small">
                            Es una relación entre la cantidad de representantes encontrados con deuda y el total de
                            representantes en el grupo consultado. Un porcentaje alto indica un alto índice de impago
                            para
                            ese mes.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@section('stylesheet')
    @parent
    <style>
        :root {
            --primary-custom: #1c4517;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
        }

        body {
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .text-primary-custom {
            color: var(--primary-custom) !important;
        }

        .card-custom {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .border-left-primary {
            border-left: 4px solid var(--primary-custom) !important;
        }

        .bg-danger-light {
            background-color: #fee2e2;
        }

        .bg-info-light {
            background-color: #e0f2fe;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .accordion .card {
            overflow: hidden;
            border-radius: 0.5rem !important;
        }

        .accordion .card-header button:focus {
            text-decoration: none;
            box-shadow: none;
        }

        /* Scrollbar styling for modal if needed */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endsection
