<!-- Modal Controlado por Livewire -->
<div class="modal fade show d-block" tabindex="-1" role="dialog" aria-labelledby="guiaUsuarioModalLabel" aria-modal="true"
    style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document"
        style="max-height: 90vh; margin: 2.5vh auto; width: 95%; max-width: 1200px;">
        <div class="modal-content" style="max-height: 90vh; display: flex; flex-direction: column;">
            <!-- Header del Modal -->
            <div class="modal-header bg-primary text-white py-2" style="flex-shrink: 0;">
                <h5 class="modal-title" id="guiaUsuarioModalLabel">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    Guía del Usuario - Sistema de Gestión de Evaluaciones
                </h5>
                <button type="button" class="close text-white" wire:click="toggleGuiaModal" aria-label="Cerrar"
                    style="opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body del Modal SOLO SCROLL VERTICAL -->
            <div class="modal-body p-0"
                style="overflow-y: auto; overflow-x: hidden; flex: 1; max-height: calc(90vh - 120px);">
                <!-- Navegación Rápida -->
                <div class="card mb-2 border-0">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-map-signs mr-2"></i>
                            Navegación Rápida
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row text-center">
                            <div class="col-md-3 mb-1">
                                <i class="fas fa-filter mr-1"></i>Filtros
                            </div>
                            <div class="col-md-3 mb-1">
                                <i class="fas fa-table mr-1"></i>Tabla
                            </div>
                            <div class="col-md-3 mb-1">
                                <i class="fas fa-cogs mr-1"></i>Acciones
                            </div>
                            <div class="col-md-3 mb-1">
                                <i class="fas fa-info-circle mr-1"></i>Estados
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Filtros -->
                <div id="modalFiltros" class="card mb-2">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-filter mr-2"></i>
                            Filtros de Búsqueda
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-1 mb-2">
                                    <i class="fas fa-sliders-h mr-1 text-primary"></i>
                                    Filtros Académicos
                                </h6>

                                <div class="media mb-2">
                                    <span class="badge badge-primary mr-2 mt-1">1</span>
                                    <div class="media-body">
                                        <strong>Grado</strong>
                                        <p class="mb-1 small text-muted">Selecciona el grado académico para filtrar las
                                            evaluaciones.</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-primary mr-2 mt-1">2</span>
                                    <div class="media-body">
                                        <strong>Sección</strong>
                                        <p class="mb-1 small text-muted">Elige la sección específica del grado
                                            seleccionado.</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-primary mr-2 mt-1">3</span>
                                    <div class="media-body">
                                        <strong>Momento (Lapso)</strong>
                                        <p class="mb-1 small text-muted">Selecciona el período académico o lapso.</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-primary mr-2 mt-1">4</span>
                                    <div class="media-body">
                                        <strong>Asignatura</strong>
                                        <p class="mb-1 small text-muted">Filtra por materia específica del pensum.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="border-bottom pb-1 mb-2">
                                    <i class="fas fa-user-cog mr-1 text-primary"></i>
                                    Filtros Adicionales
                                </h6>

                                <div class="media mb-2">
                                    <span class="badge badge-primary mr-2 mt-1">5</span>
                                    <div class="media-body">
                                        <strong>Profesor</strong>
                                        <p class="mb-1 small text-muted">Busca evaluaciones por docente específico.</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-primary mr-2 mt-1">6</span>
                                    <div class="media-body">
                                        <strong>Rango de Fechas</strong>
                                        <p class="mb-1 small text-muted">
                                            <i class="fas fa-calendar-alt mr-1"></i>Fecha Inicial y Final para el
                                            período de evaluación.
                                        </p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-primary mr-2 mt-1">7</span>
                                    <div class="media-body">
                                        <strong>Estado</strong>
                                        <p class="mb-1 small text-muted">Filtra por: Ejecutada ✓ o Pendiente !</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-success mr-2 mt-1">8</span>
                                    <div class="media-body">
                                        <strong>Buscar</strong>
                                        <p class="mb-1 small text-muted">
                                            <i class="fas fa-search mr-1"></i>Ejecuta la búsqueda con los filtros
                                            aplicados.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de la Tabla - SIN SCROLL HORIZONTAL -->
                <div id="modalTabla" class="card mb-2">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-table mr-2"></i>
                            Interpretación de la Tabla de Resultados
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive" style="overflow-x: hidden;">
                            <table class="table table-sm table-bordered mb-0" style="width: 100%; min-width: auto;">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="align-middle">Columna</th>
                                        <th class="align-middle">Descripción</th>
                                        <th class="align-middle">Contenido</th>
                                        <th class="align-middle">Indicadores</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center"><strong>N</strong></td>
                                        <td>Número de registro</td>
                                        <td>Orden secuencial con paginación</td>
                                        <td class="text-center"><span class="badge badge-secondary">1, 2, 3...</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Descripción</strong></td>
                                        <td>Detalle de la evaluación</td>
                                        <td>Texto abreviado (hover para ver completo)</td>
                                        <td class="text-center"><i class="fas fa-mouse-pointer text-info"></i></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Profesor</strong></td>
                                        <td>Docente responsable</td>
                                        <td>Nombre completo abreviado</td>
                                        <td>
                                            <span class="badge badge-success">Activo</span>
                                            <span class="badge badge-secondary">Inactivo</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Asignatura</strong></td>
                                        <td>Materia académica</td>
                                        <td>Código y nombre completo</td>
                                        <td class="text-center"><i class="fas fa-book text-primary"></i></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Grado/Sección/Lapso</strong></td>
                                        <td>Contexto académico</td>
                                        <td>Información combinada</td>
                                        <td class="text-center"><i class="fas fa-layer-group text-warning"></i></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Fecha</strong></td>
                                        <td>Fecha de la evaluación</td>
                                        <td>Formato legible</td>
                                        <td class="text-center"><i class="fas fa-calendar text-info"></i></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Notas</strong></td>
                                        <td>Cantidad de calificaciones</td>
                                        <td>Número total registrado</td>
                                        <td>
                                            <span class="badge badge-danger">0 = Sin notas</span>
                                            <span class="badge badge-success">>0 = Con notas</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Promedio</strong></td>
                                        <td>Promedio de calificaciones</td>
                                        <td>Valor numérico con 2 decimales</td>
                                        <td class="text-center"><i class="fas fa-calculator text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Estado</strong></td>
                                        <td>Estado de ejecución</td>
                                        <td>Pendiente o Ejecutada</td>
                                        <td>
                                            <span class="badge badge-warning">Pendiente</span>
                                            <span class="badge badge-success">Ejecutada</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sección de Estados y Colores -->
                <div id="modalEstados" class="card mb-2">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-palette mr-2"></i>
                            Sistema de Estados y Colores
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-1 mb-2">Estados de Evaluación</h6>

                                <div class="media mb-2">
                                    <span class="badge badge-warning mr-2 mt-1">!</span>
                                    <div class="media-body">
                                        <strong>Pendiente</strong>
                                        <p class="mb-1 small text-muted">Evaluación programada pero no ejecutada.</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-success mr-2 mt-1">✓</span>
                                    <div class="media-body">
                                        <strong>Ejecutada</strong>
                                        <p class="mb-1 small text-muted">Evaluación completada y procesada.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="border-bottom pb-1 mb-2">Indicadores Visuales</h6>

                                <div class="media mb-2">
                                    <span class="badge badge-danger mr-2">0</span>
                                    <div class="media-body">
                                        <strong>Sin Notas Registradas</strong>
                                        <p class="mb-1 small text-muted">Fila en color rojo - requiere atención.</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-success mr-2">✓</span>
                                    <div class="media-body">
                                        <strong>Con Notas</strong>
                                        <p class="mb-1 small text-muted">Fila en color normal - proceso completado.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Acciones -->
                <div id="modalAcciones" class="card mb-2">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs mr-2"></i>
                            Acciones Disponibles
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="border-bottom pb-1 mb-2">Gestión de Estados</h6>

                                <div class="media mb-3">
                                    <button class="btn btn-warning btn-sm mr-3" disabled>
                                        <i class="fas fa-exclamation"></i>
                                    </button>
                                    <div class="media-body">
                                        <strong>Marcar como Pendiente</strong>
                                        <p class="mb-1 small text-muted">Cambia el estado de Ejecutada a Pendiente.</p>
                                        <span class="badge badge-light">Acción reversible</span>
                                    </div>
                                </div>

                                <div class="media mb-3">
                                    <button class="btn btn-success btn-sm mr-3" disabled>
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <div class="media-body">
                                        <strong>Marcar como Ejecutada</strong>
                                        <p class="mb-1 small text-muted">Confirma la ejecución de la evaluación.</p>
                                        <span class="badge badge-light">Acción reversible</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="border-bottom pb-1 mb-2">Funcionalidades</h6>

                                <div class="media mb-2">
                                    <span class="badge badge-info mr-2 mt-1"><i class="fas fa-sync"></i></span>
                                    <div class="media-body">
                                        <strong>Búsqueda en Tiempo Real</strong>
                                        <p class="mb-1 small text-muted">Los filtros se actualizan automáticamente.</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-info mr-2 mt-1"><i class="fas fa-file-alt"></i></span>
                                    <div class="media-body">
                                        <strong>Paginación</strong>
                                        <p class="mb-1 small text-muted">Navegación entre páginas de resultados.</p>
                                    </div>
                                </div>

                                <div class="media mb-2">
                                    <span class="badge badge-info mr-2 mt-1"><i class="fas fa-bell"></i></span>
                                    <div class="media-body">
                                        <strong>Notificaciones</strong>
                                        <p class="mb-1 small text-muted">Confirmación visual de acciones realizadas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consejos Rápidos -->
                <div class="card mb-0">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Consejos Rápidos
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="media mb-2">
                                    <i class="fas fa-mouse-pointer text-primary mr-2 mt-1"></i>
                                    <div class="media-body">
                                        <small class="text-muted">Usa el hover sobre textos truncados para ver
                                            información completa</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="media mb-2">
                                    <i class="fas fa-tablet-alt text-primary mr-2 mt-1"></i>
                                    <div class="media-body">
                                        <small class="text-muted">La tabla es responsive - se adapta a diferentes
                                            dispositivos</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="media mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning mr-2 mt-1"></i>
                                    <div class="media-body">
                                        <small class="text-muted">Las filas en rojo indican evaluaciones sin notas
                                            registradas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer py-2" style="flex-shrink: 0;">
                <button type="button" class="btn btn-secondary btn-sm" wire:click="toggleGuiaModal"
                    aria-label="Cerrar ventana">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Fondo del Modal -->
<div class="modal-backdrop fade show" wire:click="toggleGuiaModal"></div>

@section('scripts')
    @parent
    <script>
        document.addEventListener('livewire:load', function() {
            // Cerrar modal con tecla ESC
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && @this.showGuiaModal) {
                    @this.toggleGuiaModal();
                }
            });

            // Prevenir que el click en el modal content cierre el modal
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal-content') ||
                    event.target.closest('.modal-content')) {
                    event.stopPropagation();
                }
            });

            // Prevenir scroll del body cuando el modal está abierto
            if (@this.showGuiaModal) {
                document.body.style.overflow = 'hidden';
            }

            // Manejar el scroll cuando se abre/cierra el modal
            Livewire.hook('message.processed', (message, component) => {
                if (component.serverMemo.data.showGuiaModal) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });

            // Forzar sin scroll horizontal
            function preventHorizontalScroll() {
                document.querySelectorAll('.modal-body, .modal-content').forEach(element => {
                    element.addEventListener('wheel', function(e) {
                        if (e.deltaX !== 0) {
                            e.preventDefault();
                        }
                    });
                });
            }

            preventHorizontalScroll();
        });
    </script>
@endsection

@section('stylesheet')
    @parent
    <style>
        /* ELIMINAR SCROLL HORIZONTAL COMPLETAMENTE */
        .modal-body {
            overflow-x: hidden !important;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c1c1c1 #f1f1f1;
        }

        .modal-content {
            overflow-x: hidden !important;
        }

        .modal-dialog {
            overflow-x: hidden !important;
        }

        /* Eliminar scroll horizontal en tablas */
        .table-responsive {
            overflow-x: hidden !important;
        }

        .table {
            width: 100% !important;
            min-width: auto !important;
            max-width: 100% !important;
        }

        /* Scroll vertical personalizado */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Mejorar la apariencia del modal */
        .modal-content {
            border: none;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Asegurar que el modal esté centrado y sin overflow */
        .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
            max-width: 95%;
        }

        /* Forzar que todo el contenido se ajuste al ancho */
        .card,
        .table,
        .row,
        .col-md-6,
        .col-md-4 {
            max-width: 100% !important;
        }

        /* Responsive para móviles - sin scroll horizontal */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0.5rem;
                max-height: calc(100vh - 1rem);
                width: calc(100% - 1rem) !important;
            }

            .modal-content {
                max-height: calc(100vh - 1rem);
                width: 100% !important;
            }

            .modal-body {
                max-height: calc(100vh - 140px);
            }

            /* Ajustar tablas para móviles */
            .table {
                font-size: 0.8rem;
            }

            .table th,
            .table td {
                padding: 0.3rem;
            }
        }

        /* Prevenir cualquier scroll horizontal */
        body.modal-open {
            overflow-x: hidden !important;
        }

        * {
            max-width: 100%;
        }
    </style>
@endsection
