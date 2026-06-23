<div class="tab-pane fade" id="special-reports" role="tabpanel">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Caso de Uso: Administración de Informes Especiales</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5>Descripción</h5>
                    <p class="text-muted">
                        Este caso de uso permite a los docentes gestionar eventos o situaciones particulares
                        que requieren atención especial, creando planes específicos con actividades
                        diferenciadas para abordar necesidades educativas particulares o circunstancias
                        excepcionales en el aula.
                    </p>

                    <div class="mt-3">
                        <h6><i class="fas fa-list-ul mr-2"></i>Funcionalidades Principales:</h6>
                        <ul class="text-muted">
                            <li>Crear planes especiales con justificación detallada</li>
                            <li>Definir actividades específicas por área de aprendizaje</li>
                            <li>Establecer objetivos y aprendizajes esperados particulares</li>
                            <li>Documentar líneas de investigación y énfasis curriculares</li>
                            <li>Generar reportes especializados para seguimiento</li>
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
                                <li>Eispecialk</li>
                                <li>Eispecialact</li>
                                <li>Pevaluacion</li>
                                <li>Grado, Seccion</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-cogs mr-2"></i>Sistema Involucrado</h6>
                            <p class="mb-0">Sistema de Planes Especiales</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagrama Principal -->
            <div class="diagram-container">
                <h5 class="text-center mb-3"><i class="fas fa-sitemap mr-2"></i>Diagrama de Casos de Uso</h5>
                <div class="mermaid">
                    graph TD
                        A["👤 Docente"] --> B["Crear Plan Especial"]
                        A --> C["Editar Plan Especial"]
                        A --> D["Eliminar Plan"]
                        A --> E["Consultar Planes"]
                        A --> F["Generar Reporte"]

                        B --> G["Definir Justificación"]
                        B --> H["Establecer Período"]
                        B --> I["Agregar Actividades Especiales"]
                        B --> J["Documentar Observaciones"]

                        I --> K["Seleccionar Área de Aprendizaje"]
                        I --> L["Definir Componente"]
                        I --> M["Establecer Objetivo"]
                        I --> N["Especificar Aprendizaje Esperado"]
                        I --> O["Agregar Indicadores"]
                        I --> P["Incluir Línea de Investigación"]

                        C --> Q["Modificar Justificación"]
                        C --> R["Actualizar Actividades"]
                        C --> S["Cambiar Fechas"]

                        E --> T["Filtrar por Período"]
                        E --> U["Buscar por Grado"]
                        E --> V["Ver por Profesor"]

                        W["🚨 Sistema de Planes Especiales"] --> X["Validar Justificación"]
                        W --> Y["Ordenar Actividades"]
                        W --> Z["Generar Documentación"]

                        style A fill:#e1f5fe
                        style W fill:#fff3e0
                        style B fill:#ffebee
                        style I fill:#fff8e1
                        style G fill:#f3e5f5
                </div>
            </div>

            <!-- Tipos de Situaciones Especiales -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-lightbulb mr-2"></i>Situaciones que Requieren Planes Especiales</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-danger"><i class="fas fa-user-injured mr-2"></i>Necesidades Educativas Especiales</h6>
                                    <ul class="small text-muted">
                                        <li>Estudiantes con discapacidades</li>
                                        <li>Dificultades de aprendizaje</li>
                                        <li>Trastornos del desarrollo</li>
                                        <li>Adaptaciones curriculares</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning"><i class="fas fa-calendar-times mr-2"></i>Eventos Extraordinarios</h6>
                                    <ul class="small text-muted">
                                        <li>Emergencias sanitarias</li>
                                        <li>Situaciones climáticas</li>
                                        <li>Eventos comunitarios</li>
                                        <li>Celebraciones especiales</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-info"><i class="fas fa-star mr-2"></i>Programas Especializados</h6>
                                    <ul class="small text-muted">
                                        <li>Talleres temáticos</li>
                                        <li>Proyectos de investigación</li>
                                        <li>Actividades de refuerzo</li>
                                        <li>Programas de enriquecimiento</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estructura del Plan Especial -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-file-medical mr-2"></i>Plan Especial (Eispecialk)</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Estructura principal que define el marco general del plan especial.
                            </p>
                            <ul class="mb-0 small">
                                <li><strong>Profesor:</strong> Docente responsable del plan</li>
                                <li><strong>Grado y Sección:</strong> Grupo estudiantil objetivo</li>
                                <li><strong>Fechas:</strong> Período de inicio y culminación</li>
                                <li><strong>Tiempo de Ejecución:</strong> Duración en semanas</li>
                                <li><strong>Justificación:</strong> Razón y necesidad del plan</li>
                                <li><strong>Observaciones:</strong> Comentarios adicionales</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-tasks mr-2"></i>Actividades Especiales (Eispecialact)</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Actividades específicas diseñadas para abordar la situación especial.
                            </p>
                            <ul class="mb-0 small">
                                <li><strong>Área de Aprendizaje:</strong> Campo curricular específico</li>
                                <li><strong>Componente:</strong> Elemento del área de aprendizaje</li>
                                <li><strong>Objetivo:</strong> Meta específica a alcanzar</li>
                                <li><strong>Aprendizaje Esperado:</strong> Logro anticipado</li>
                                <li><strong>Indicadores:</strong> Criterios de evaluación</li>
                                <li><strong>Línea de Investigación:</strong> Enfoque metodológico</li>
                                <li><strong>Énfasis Curriculares:</strong> Aspectos prioritarios</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proceso de Creación -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-route mr-2"></i>Proceso de Creación de Plan Especial</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-search fa-2x text-primary mb-2"></i>
                                            <h6>1. Identificación</h6>
                                            <p class="small text-muted mb-0">
                                                Detectar la necesidad o situación especial
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-edit fa-2x text-info mb-2"></i>
                                            <h6>2. Justificación</h6>
                                            <p class="small text-muted mb-0">
                                                Documentar razones y fundamentos
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-calendar fa-2x text-warning mb-2"></i>
                                            <h6>3. Planificación</h6>
                                            <p class="small text-muted mb-0">
                                                Definir tiempos y recursos necesarios
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-plus fa-2x text-success mb-2"></i>
                                            <h6>4. Actividades</h6>
                                            <p class="small text-muted mb-0">
                                                Diseñar actividades específicas
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-play fa-2x text-danger mb-2"></i>
                                            <h6>5. Ejecución</h6>
                                            <p class="small text-muted mb-0">
                                                Implementar el plan especial
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-chart-line fa-2x text-secondary mb-2"></i>
                                            <h6>6. Evaluación</h6>
                                            <p class="small text-muted mb-0">
                                                Valorar resultados y efectividad
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Características Especiales -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-star mr-2"></i>Características Distintivas</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li><strong>Flexibilidad:</strong> Adaptación a situaciones imprevistas</li>
                                <li><strong>Personalización:</strong> Enfoque en necesidades específicas</li>
                                <li><strong>Temporalidad:</strong> Duración limitada y definida</li>
                                <li><strong>Justificación:</strong> Fundamentación pedagógica sólida</li>
                                <li><strong>Seguimiento:</strong> Monitoreo continuo de resultados</li>
                                <li><strong>Documentación:</strong> Registro detallado del proceso</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-shield-alt mr-2"></i>Validaciones del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li><strong>Justificación Obligatoria:</strong> Campo requerido y detallado</li>
                                <li><strong>Fechas Coherentes:</strong> Inicio anterior a finalización</li>
                                <li><strong>Tiempo Realista:</strong> Duración apropiada para objetivos</li>
                                <li><strong>Actividades Mínimas:</strong> Al menos una actividad por área</li>
                                <li><strong>Permisos Docente:</strong> Autorización para la sección</li>
                                <li><strong>Orden Lógico:</strong> Secuencia coherente de actividades</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ejemplos de Aplicación -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-lightbulb mr-2"></i>Ejemplos de Aplicación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-danger"><i class="fas fa-medkit mr-2"></i>Plan de Emergencia Sanitaria</h6>
                                            <p class="small text-muted">
                                                <strong>Justificación:</strong> Brote de enfermedad contagiosa en el aula<br>
                                                <strong>Actividades:</strong> Educación en higiene, actividades a distancia<br>
                                                <strong>Duración:</strong> 2-3 semanas
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-warning"><i class="fas fa-child mr-2"></i>Apoyo a Estudiante con NEE</h6>
                                            <p class="small text-muted">
                                                <strong>Justificación:</strong> Estudiante con dificultades de aprendizaje<br>
                                                <strong>Actividades:</strong> Adaptaciones curriculares, apoyo individualizado<br>
                                                <strong>Duración:</strong> Todo el período escolar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-success"><i class="fas fa-seedling mr-2"></i>Proyecto Ambiental</h6>
                                            <p class="small text-muted">
                                                <strong>Justificación:</strong> Concienciación ambiental por evento climático<br>
                                                <strong>Actividades:</strong> Huerto escolar, reciclaje, cuidado del agua<br>
                                                <strong>Duración:</strong> 4-6 semanas
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    <h6 class="text-primary"><i class="fas fa-calendar-week mr-2"></i>Planificación Semanal</h6>
                                    <p class="small text-muted mb-3">
                                        Los planes especiales pueden modificar o complementar las
                                        planificaciones semanales regulares según las necesidades.
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fas fa-clipboard-check mr-2"></i>Sistema de Evaluación</h6>
                                    <p class="small text-muted mb-3">
                                        Las actividades especiales requieren evaluaciones adaptadas
                                        a los objetivos específicos del plan.
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-info"><i class="fas fa-file-alt mr-2"></i>Informes Pedagógicos</h6>
                                    <p class="small text-muted mb-3">
                                        Los resultados de planes especiales se documentan en los
                                        informes finales de los estudiantes.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métodos del Sistema -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-code mr-2"></i>Métodos Principales del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-danger">Eispecialk (Plan Especial)</h6>
                                    <ul class="small text-muted">
                                        <li><code>activities()</code> - Relación con actividades especiales</li>
                                        <li><code>getOrderedActivities()</code> - Actividades ordenadas</li>
                                        <li><code>getPevaluacions()</code> - Evaluaciones relacionadas</li>
                                        <li><code>getPevaluacionsList()</code> - Lista de evaluaciones</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-info">Eispecialact (Actividades)</h6>
                                    <ul class="small text-muted">
                                        <li><code>eispecialk()</code> - Relación con plan especial</li>
                                        <li><code>pevaluacion()</code> - Relación con área de aprendizaje</li>
                                        <li>Campos: componente, objetivo, aprendizaje esperado</li>
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
