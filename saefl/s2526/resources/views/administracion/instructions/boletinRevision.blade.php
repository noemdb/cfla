@extends('layouts.app')

@section('content')
    <div class="container-fluid bg-light" style="min-height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 p-0">

                <!-- Header Sticky -->
                <header class="sticky-top bg-white border-bottom shadow-sm" style="z-index: 1020;">
                    <div class="d-flex align-items-center justify-content-between px-4 py-3">
                        <a href="#" onclick="window.history.back(); return false;"
                            class="text-decoration-none d-flex align-items-center text-primary-custom">
                            <i class="fas fa-chevron-left mr-2"></i>
                            <span class="font-weight-bold">Atrás</span>
                        </a>
                        <h5 class="mb-0 font-weight-bold text-dark">Guía de Gestión</h5>
                        <a href="#" data-dismiss="modal" class="text-primary-custom text-decoration-none">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </header>

                <main class="p-4">
                    <!-- Introducción -->
                    <section class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-square text-primary-custom fa-2x mr-3"></i>
                            <h4 class="mb-0 font-weight-bold text-dark">Introducción</h4>
                        </div>
                        <p class="text-muted lead" style="font-size: 1rem; line-height: 1.6;">
                            Esta guía proporciona instrucciones detalladas sobre cómo gestionar y registrar las
                            <strong>notas de revisión</strong>, recuperaciones y equivalencias de los estudiantes en el
                            sistema académico, asegurando la integridad del historial académico.
                        </p>
                    </section>

                    <!-- Flujo de Trabajo -->
                    <section class="mb-5">
                        <h4 class="font-weight-bold text-dark mb-4">Flujo de Trabajo</h4>
                        <div class="row">
                            <div class="col-4 pr-2">
                                <div class="card border-0 shadow-sm h-100 text-center py-3 px-2">
                                    <div class="mb-2">
                                        <i class="fas fa-search-plus text-primary-custom fa-2x"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-dark small mb-1">1. Selección</h6>
                                    <p class="text-muted extra-small mb-0">Filtre por Grado y Sección.</p>
                                </div>
                            </div>
                            <div class="col-4 px-2">
                                <div class="card border-0 shadow-sm h-100 text-center py-3 px-2">
                                    <div class="mb-2">
                                        <i class="fas fa-edit text-primary-custom fa-2x"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-dark small mb-1">2. Edición</h6>
                                    <p class="text-muted extra-small mb-0">Registre la nota y tipo.</p>
                                </div>
                            </div>
                            <div class="col-4 pl-2">
                                <div class="card border-0 shadow-sm h-100 text-center py-3 px-2">
                                    <div class="mb-2">
                                        <i class="fas fa-save text-primary-custom fa-2x"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-dark small mb-1">3. Guardado</h6>
                                    <p class="text-muted extra-small mb-0">Actualice el historial.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Identificación y Acciones -->
                    <section class="mb-5">
                        <h4 class="font-weight-bold text-dark mb-4">Identificación y Acciones</h4>

                        <div class="media mb-4">
                            <div class="icon-box bg-white shadow-sm text-primary-custom mr-3 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0 font-weight-bold text-dark">Materias Aplazadas</h6>
                                <p class="text-muted small mb-0">Las celdas en <span
                                        class="text-danger font-weight-bold">rojo</span> indican materias reprobadas que
                                    requieren recuperación.</p>
                            </div>
                        </div>

                        <div class="media mb-4">
                            <div class="icon-box bg-white shadow-sm text-primary-custom mr-3 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0 font-weight-bold text-dark">Contador de Revisiones</h6>
                                <p class="text-muted small mb-0">La columna "Revisiones" muestra cuántas recuperaciones
                                    tiene el estudiante.</p>
                            </div>
                        </div>

                        <div class="media mb-4">
                            <div class="icon-box bg-white shadow-sm text-primary-custom mr-3 rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0 font-weight-bold text-dark">Certificación</h6>
                                <p class="text-muted small mb-0">Genere el PDF histórico solo si el estudiante tiene
                                    revisiones registradas.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Modos de Visualización -->
                    <section class="mb-5">
                        <h4 class="font-weight-bold text-dark mb-4">Modos de Visualización</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100 p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-list-ul text-primary-custom mr-2"></i>
                                        <h6 class="mb-0 font-weight-bold text-dark">Vista de Lista</h6>
                                    </div>
                                    <p class="text-muted small mb-0">Tabla general con promedios y estatus de todas las
                                        materias.</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 shadow-sm h-100 p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-edit text-primary-custom mr-2"></i>
                                        <h6 class="mb-0 font-weight-bold text-dark">Formulario de Edición</h6>
                                    </div>
                                    <p class="text-muted small mb-0">Interfaz detallada para registrar notas, tipo
                                        (Revisión/Equivalencia) y observaciones.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Preguntas Frecuentes -->
                    <section class="mb-5">
                        <h4 class="font-weight-bold text-dark mb-4">Preguntas Frecuentes</h4>
                        <div class="accordion" id="faqAccordion">

                            <!-- FAQ 1 -->
                            <div class="card border-0 mb-2 shadow-sm rounded overflow-hidden">
                                <div class="card-header bg-white border-0 p-0" id="headingOne">
                                    <h2 class="mb-0">
                                        <button
                                            class="btn btn-link btn-block text-left text-dark font-weight-bold p-3 d-flex justify-content-between align-items-center text-decoration-none"
                                            type="button" data-toggle="collapse" data-target="#collapseOne"
                                            aria-expanded="true" aria-controls="collapseOne">
                                            <span>¿Por qué el botón "Editar" está deshabilitado?</span>
                                            <i class="fas fa-chevron-down text-primary-custom small"></i>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                    data-parent="#faqAccordion">
                                    <div class="card-body pt-0 pb-3 px-3 text-muted small">
                                        El sistema lo deshabilita si el estudiante no tiene materias aplazadas (0 rojas) y
                                        no posee revisiones previas, para evitar registros innecesarios.
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 2 -->
                            <div class="card border-0 mb-2 shadow-sm rounded overflow-hidden">
                                <div class="card-header bg-white border-0 p-0" id="headingTwo">
                                    <h2 class="mb-0">
                                        <button
                                            class="btn btn-link btn-block text-left text-dark font-weight-bold p-3 d-flex justify-content-between align-items-center text-decoration-none collapsed"
                                            type="button" data-toggle="collapse" data-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            <span>¿Cómo corrijo un error en una revisión?</span>
                                            <i class="fas fa-chevron-down text-primary-custom small"></i>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                    data-parent="#faqAccordion">
                                    <div class="card-body pt-0 pb-3 px-3 text-muted small">
                                        En el formulario del estudiante, busque la tabla inferior "Revisiones registradas" y
                                        haga clic en el botón de edición del registro correspondiente.
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ 3 -->
                            <div class="card border-0 mb-2 shadow-sm rounded overflow-hidden">
                                <div class="card-header bg-white border-0 p-0" id="headingThree">
                                    <h2 class="mb-0">
                                        <button
                                            class="btn btn-link btn-block text-left text-dark font-weight-bold p-3 d-flex justify-content-between align-items-center text-decoration-none collapsed"
                                            type="button" data-toggle="collapse" data-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            <span>¿Cuándo puedo generar la Certificación?</span>
                                            <i class="fas fa-chevron-down text-primary-custom small"></i>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                    data-parent="#faqAccordion">
                                    <div class="card-body pt-0 pb-3 px-3 text-muted small">
                                        El botón se habilita automáticamente cuando el estudiante tiene al menos una
                                        revisión registrada en el sistema.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                </main>
            </div>
        </div>
    </div>
@endsection

@section('stylesheet')
    @parent
    <style>
        :root {
            --primary-custom: #1c4517;
            --bg-light: #f3f4f6;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg-light);
        }

        .text-primary-custom {
            color: var(--primary-custom) !important;
        }

        .bg-light {
            background-color: var(--bg-light) !important;
        }

        .extra-small {
            font-size: 0.75rem;
        }

        .card {
            border-radius: 0.5rem;
            transition: transform 0.2s ease-in-out;
        }

        /* Hover effect for cards */
        .card:hover {
            transform: translateY(-2px);
        }

        /* Accordion styling */
        .btn-link:focus {
            text-decoration: none;
            box-shadow: none;
        }

        .btn-link[aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
            transition: transform 0.3s;
        }

        .btn-link[aria-expanded="false"] .fa-chevron-down {
            transform: rotate(0deg);
            transition: transform 0.3s;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }
    </style>
@endsection
