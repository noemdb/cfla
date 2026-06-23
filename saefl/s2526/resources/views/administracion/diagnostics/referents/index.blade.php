@extends('administracion.layouts.dashboard.app')

@section('title')
    SAEFL - Diagnóstico Educativo, Referentes
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header p-0 m-0">

                <!-- =========================================== -->
                <!-- ALERT INFORMATIVO COLLAPSIBLE -->
                <!-- =========================================== -->
                <!-- =========================================== -->
                <!-- ALERT INFORMATIVO COLLAPSIBLE (MEJORADO) -->
                <!-- =========================================== -->
                <div class="card border-0 border-left-info p-1 mb-2 bg-white rounded" id="referentInfoAlert"
                    style="border-left: 5px solid #17a2b8 !important;">
                    <div class="card-body p-1 alert-secondary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <div class="mr-3 text-info">
                                    <i class="fas fa-book-reader fa-3x"></i>
                                </div>
                                <div>
                                    <h4 class="text-dark font-weight-bold mb-2">Gestión de Referentes Normativos</h4>
                                    <p class="text-muted mb-3 lead" style="font-size: 1rem;">
                                        Administre los <strong>documentos oficiales vigentes</strong> que fundamentan el
                                        diagnóstico educativo.
                                        Esta sección garantiza la integridad curricular del sistema bajo estricto control de
                                        versiones.
                                    </p>

                                    <button
                                        class="btn btn-outline-info btn-sm rounded-pill px-3 shadow-none font-weight-bold"
                                        type="button" data-toggle="collapse" data-target="#referentInfoContent"
                                        aria-expanded="false" aria-controls="referentInfoContent">
                                        <i class="fas fa-info-circle mr-1"></i> Ver detalles normativos y reglas
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido colapsable -->
                        <div class="collapse mt-4" id="referentInfoContent">
                            <div class="card card-body border-0 bg-light rounded shadow-sm">
                                <div class="row">
                                    <!-- Columna 1: Modelo de Datos -->
                                    <div class="col-md-4 border-right border-secondary opacity-50">
                                        <h6 class="text-uppercase text-info font-weight-bold small mb-3">
                                            <i class="fas fa-sitemap mr-1"></i> Modelo Jerárquico
                                        </h6>
                                        <ul class="list-unstyled text-muted small">
                                            <li class="mb-3">
                                                <span class="badge badge-info mb-1 d-block text-left"
                                                    style="width: fit-content;">1. Referente Normativo</span>
                                                Es la entidad raíz (ej. "Reforma Curricular 2017"). Define el alcance legal
                                                y pedagógico. Se vincula directamente a un <strong>Plan de Estudio</strong>.
                                            </li>
                                            <li class="mb-3">
                                                <span class="badge badge-info mb-1 d-block text-left"
                                                    style="width: fit-content;">2. Competencias</span>
                                                Desglosan el referente en habilidades específicas por área (ej. "Biología",
                                                "Matemática"). Pueden ser transversales o específicas de un grado.
                                            </li>
                                            <li class="mb-0">
                                                <span class="badge badge-info mb-1 d-block text-left"
                                                    style="width: fit-content;">3. Indicadores de Logro</span>
                                                La unidad mínima de evaluación. Son observables y medibles, categorizados
                                                por niveles de desempeño (1-4).
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Columna 2: Reglas de Negocio -->
                                    <div class="col-md-4 border-right border-secondary opacity-50">
                                        <h6 class="text-uppercase text-info font-weight-bold small mb-3">
                                            <i class="fas fa-gavel mr-1"></i> Reglas de Integridad
                                        </h6>
                                        <ul class="list-group list-group-flush bg-transparent small">
                                            <li class="list-group-item bg-transparent pl-0 py-2 border-0">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                <strong>Unicidad por Plan:</strong> Solo puede existir <u>un</u> referente
                                                activo simultáneamente para un mismo Plan de Estudio (ej. "Ciencias 2023").
                                            </li>
                                            <li class="list-group-item bg-transparent pl-0 py-2 border-0">
                                                <i class="fas fa-lock text-warning mr-2"></i>
                                                <strong>Inmutabilidad Histórica:</strong> Una vez que un referente ha sido
                                                utilizado en diagnósticos cerrados, no debe modificarse. Se debe crear una
                                                nueva versión.
                                            </li>
                                            <li class="list-group-item bg-transparent pl-0 py-2 border-0">
                                                <i class="fas fa-history text-info mr-2"></i>
                                                <strong>Versionado:</strong> Utilice el campo <em>versión</em> (ej. v1.2)
                                                para rastrear cambios menores sin romper la trazabilidad histórica.
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Columna 3: Flujo de Trabajo -->
                                    <div class="col-md-4">
                                        <h6 class="text-uppercase text-info font-weight-bold small mb-3">
                                            <i class="fas fa-exchange-alt mr-1"></i> Flujo de Actualización
                                        </h6>
                                        <div class="timeline-simple small">
                                            <div class="d-flex mb-3">
                                                <div class="mr-3 text-center" style="width: 20px;">
                                                    <span class="badge badge-primary rounded-circle p-1">1</span>
                                                </div>
                                                <div>
                                                    <strong>Desactivar Anterior:</strong>
                                                    <p class="text-muted mb-0">Ubique el referente vigente y cambie su
                                                        estado a "Inactivo".</p>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="mr-3 text-center" style="width: 20px;">
                                                    <span class="badge badge-success rounded-circle p-1">2</span>
                                                </div>
                                                <div>
                                                    <strong>Crear Nuevo:</strong>
                                                    <p class="text-muted mb-0">Registre el nuevo referente con el mismo Plan
                                                        de Estudio y una versión superior.</p>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="mr-3 text-center" style="width: 20px;">
                                                    <span class="badge badge-warning rounded-circle p-1">3</span>
                                                </div>
                                                <div>
                                                    <strong>Migrar (Opcional):</strong>
                                                    <p class="text-muted mb-0">Use herramientas de importación (si
                                                        disponibles) para clonar competencias base.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-secondary opacity-25 my-3">

                                <div class="alert alert-warning border-left-warning shadow-sm mb-0 rounded-lg"
                                    role="alert"
                                    style="border-left: 4px solid #ffc107 !important; background-color: #fff8e1;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-shield-alt fa-2x text-warning mr-3"></i>
                                        <div>
                                            <h6 class="alert-heading font-weight-bold text-dark mb-1">Zona de Peligro
                                                Administrativo</h6>
                                            <p class="small text-dark mb-0">
                                                La eliminación de referentes es <strong>destructiva</strong>. Si elimina un
                                                referente, perderá recursivamente todas las competencias e indicadores
                                                asociados. Si existen diagnósticos estudiantiles vinculados, estos quedarán
                                                huérfanos. <u>Prefiera siempre la desactivación sobre la eliminación.</u>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-1">
                <livewire:administracion.diagnostics.referents-main />
            </div>

        </div>
    </main>
@endsection

@section('sweetalert')
    @parent
    <script>
        window.addEventListener('swal', function(e) {
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm', function(e) {
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.value) {
                    window.livewire.emit('remove', e.detail.id);
                }
            });
        });

        window.addEventListener('swal:question', function(e) {
            Swal.fire({
                title: e.detail.message,
                text: e.detail.text,
                icon: e.detail.type,
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.value) {
                    window.livewire.emit(e.detail.method, e.detail.id);
                }
            });
        });
    </script>
@endsection
