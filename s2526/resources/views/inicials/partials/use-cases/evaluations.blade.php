<div class="tab-pane fade" id="evaluations" role="tabpanel">
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="fas fa-clipboard-check mr-2"></i>Caso de Uso: Gestión de Evaluaciones</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5>Descripción</h5>
                    <p class="text-muted">
                        Este caso de uso permite a los docentes registrar actividades de evaluación y realizar
                        seguimiento del desempeño estudiantil, incluyendo planes de evaluación, actividades
                        evaluativas específicas y control de asistencia.
                    </p>

                    <div class="mt-3">
                        <h6><i class="fas fa-list-ul mr-2"></i>Funcionalidades Principales:</h6>
                        <ul class="text-muted">
                            <li>Crear y gestionar planes de evaluación por período</li>
                            <li>Registrar actividades evaluativas específicas</li>
                            <li>Documentar aprendizajes alcanzados e indicadores</li>
                            <li>Controlar asistencia y participación estudiantil</li>
                            <li>Generar reportes de seguimiento y evaluación</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-user mr-2"></i>Actor Principal</h6>
                            <p class="mb-0">Docente</p>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-database mr-2"></i>Modelos Relacionados</h6>
                            <ul class="mb-0 small">
                                <li>Eievaluationk</li>
                                <li>Eievaluationp</li>
                                <li>Pevaluacion</li>
                                <li>Lapso</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-cogs mr-2"></i>Sistema Involucrado</h6>
                            <p class="mb-0">Sistema de Evaluación</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagrama Principal -->
            <div class="diagram-container">
                <h5 class="text-center mb-3"><i class="fas fa-sitemap mr-2"></i>Diagrama de Casos de Uso</h5>
                <div class="mermaid">
                    graph TD
                        A["👤 Docente"] --> B["Crear Plan de Evaluación"]
                        A --> C["Registrar Actividad Evaluativa"]
                        A --> D["Editar Evaluación"]
                        A --> E["Consultar Evaluaciones"]
                        A --> F["Generar Reporte"]

                        B --> G["Seleccionar Período/Lapso"]
                        B --> H["Definir Fechas"]
                        B --> I["Agregar Observaciones"]
                        B --> J["Configurar Asistencia"]

                        C --> K["Registrar Fecha de Evaluación"]
                        C --> L["Documentar Nombres de Niños"]
                        C --> M["Definir Aprendizaje Alcanzado"]
                        C --> N["Especificar Indicadores"]
                        C --> O["Seleccionar Instrumento"]

                        D --> P["Modificar Observaciones"]
                        D --> Q["Actualizar Recomendaciones"]
                        D --> R["Cambiar Orden de Actividades"]

                        E --> S["Filtrar por Área"]
                        E --> T["Buscar por Estudiante"]
                        E --> U["Ver por Período"]

                        V["📋 Sistema de Evaluación"] --> W["Validar Completitud"]
                        V --> X["Calcular Estadísticas"]
                        V --> Y["Generar Alertas"]

                        style A fill:#e1f5fe
                        style V fill:#fff3e0
                        style B fill:#fff8e1
                        style C fill:#e8f5e8
                        style D fill:#f3e5f5
                </div>
            </div>

            <!-- Tipos de Evaluación -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Plan de Evaluación (Eievaluationk)</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Estructura general que define el marco temporal y organizativo de la evaluación.
                            </p>
                            <ul class="mb-0 small">
                                <li><strong>Profesor:</strong> Docente responsable</li>
                                <li><strong>Grado y Sección:</strong> Grupo estudiantil</li>
                                <li><strong>Lapso/Momento:</strong> Período de evaluación</li>
                                <li><strong>Fechas:</strong> Inicio y finalización</li>
                                <li><strong>Observaciones:</strong> Comentarios del docente</li>
                                <li><strong>Recomendaciones:</strong> Sugerencias del coordinador</li>
                                <li><strong>Asistencia:</strong> Control de participación</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-tasks mr-2"></i>Actividades Evaluativas (Eievaluationp)</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Registros específicos de cada actividad de evaluación realizada.
                            </p>
                            <ul class="mb-0 small">
                                <li><strong>Fecha:</strong> Cuándo se realizó la evaluación</li>
                                <li><strong>Nombres de Niños:</strong> Estudiantes evaluados</li>
                                <li><strong>Aprendizaje Alcanzado:</strong> Logros observados</li>
                                <li><strong>Componente:</strong> Área curricular específica</li>
                                <li><strong>Indicadores:</strong> Criterios de evaluación</li>
                                <li><strong>Instrumento:</strong> Herramienta utilizada</li>
                                <li><strong>Observaciones:</strong> Comentarios adicionales</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flujo de Evaluación -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-route mr-2"></i>Flujo del Proceso de Evaluación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-play-circle fa-2x text-success mb-2"></i>
                                            <h6>1. Planificación</h6>
                                            <p class="small text-muted mb-0">
                                                Crear plan de evaluación definiendo período, fechas y objetivos
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                                            <h6>2. Registro</h6>
                                            <p class="small text-muted mb-0">
                                                Documentar actividades evaluativas específicas con detalles
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-search fa-2x text-warning mb-2"></i>
                                            <h6>3. Seguimiento</h6>
                                            <p class="small text-muted mb-0">
                                                Monitorear progreso y ajustar estrategias según resultados
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-chart-line fa-2x text-danger mb-2"></i>
                                            <h6>4. Reporte</h6>
                                            <p class="small text-muted mb-0">
                                                Generar informes y documentación para análisis
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instrumentos de Evaluación -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-tools mr-2"></i>Instrumentos de Evaluación</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li><strong>Observación Directa:</strong> Registro de comportamientos y actitudes</li>
                                <li><strong>Lista de Cotejo:</strong> Verificación de logros específicos</li>
                                <li><strong>Escala de Estimación:</strong> Valoración gradual del desempeño</li>
                                <li><strong>Registro Anecdótico:</strong> Situaciones significativas</li>
                                <li><strong>Portafolio:</strong> Recopilación de trabajos del estudiante</li>
                                <li><strong>Entrevista:</strong> Diálogo estructurado con el estudiante</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Validaciones del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li><strong>Fechas Coherentes:</strong> Fecha inicial anterior a la final</li>
                                <li><strong>Período Válido:</strong> Lapso activo y disponible</li>
                                <li><strong>Área de Aprendizaje:</strong> Vinculación con evaluación válida</li>
                                <li><strong>Completitud:</strong> Campos obligatorios diligenciados</li>
                                <li><strong>Permisos:</strong> Docente autorizado para la sección</li>
                                <li><strong>Orden Lógico:</strong> Secuencia de actividades coherente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integración con otros módulos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-dark">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-link mr-2"></i>Integración con Otros Módulos</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary"><i class="fas fa-project-diagram mr-2"></i>Proyectos de Aula</h6>
                                    <p class="small text-muted mb-3">
                                        Las evaluaciones se vinculan con los proyectos de aula para medir
                                        el logro de objetivos específicos del proyecto.
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fas fa-calendar-week mr-2"></i>Planificación Semanal</h6>
                                    <p class="small text-muted mb-3">
                                        Las actividades evaluativas se relacionan con las planificaciones
                                        semanales para asegurar coherencia curricular.
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-info"><i class="fas fa-file-alt mr-2"></i>Informes Pedagógicos</h6>
                                    <p class="small text-muted mb-3">
                                        Los resultados de evaluación alimentan los informes finales
                                        sobre el progreso de cada estudiante.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métodos del Modelo -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-code mr-2"></i>Métodos Principales del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-info">Eievaluationk (Plan de Evaluación)</h6>
                                    <ul class="small text-muted">
                                        <li><code>getPevaluacions()</code> - Obtiene evaluaciones por profesor/lapso</li>
                                        <li><code>getPositionsForArea()</code> - Actividades por área específica</li>
                                        <li><code>getOrderedEvaluationps()</code> - Actividades ordenadas</li>
                                        <li><code>getPevaluacionsList()</code> - Lista de evaluaciones disponibles</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">Eievaluationp (Actividades)</h6>
                                    <ul class="small text-muted">
                                        <li><code>eievaluationk()</code> - Relación con plan de evaluación</li>
                                        <li><code>pevaluacion()</code> - Relación con área de aprendizaje</li>
                                        <li>Campos: fecha, nombres, aprendizaje, indicadores</li>
                                        <li>Ordenamiento por campo 'order' y fecha de creación</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
