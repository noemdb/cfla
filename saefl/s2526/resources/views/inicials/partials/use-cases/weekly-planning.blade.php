<div class="tab-pane fade" id="weekly-planning" role="tabpanel">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-calendar-week mr-2"></i>Caso de Uso: Gestión de Planificación Semanal</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5>Descripción</h5>
                    <p class="text-muted">
                        Este caso de uso permite a los docentes de educación inicial crear, gestionar y mantener
                        planificaciones semanales detalladas que incluyen estrategias pedagógicas diarias,
                        vinculación con proyectos de aula, y resúmenes por áreas de aprendizaje. El sistema
                        facilita la organización temporal de actividades educativas siguiendo la rutina diaria
                        específica de educación inicial, desde el recibimiento hasta la despedida.
                    </p>

                    <div class="mt-3">
                        <h6><i class="fas fa-list-ul mr-2"></i>Funcionalidades Principales:</h6>
                        <ul class="text-muted">
                            <li>Crear planificaciones semanales con período definido</li>
                            <li>Definir estrategias pedagógicas por día y momento de rutina</li>
                            <li>Vincular planificaciones con proyectos de aula existentes</li>
                            <li>Agregar resúmenes por áreas de aprendizaje específicas</li>
                            <li>Gestionar diagnóstico inicial y observaciones generales</li>
                            <li>Exportar planificaciones en formato PDF oficial</li>
                            <li>Consultar y filtrar planificaciones por múltiples criterios</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-users mr-2"></i>Actores del Sistema</h6>
                            <ul class="mb-0 small">
                                <li><strong>Docente:</strong> Creador y gestor principal</li>
                                <li><strong>Coordinador:</strong> Supervisor y revisor</li>
                                <li><strong>Sistema:</strong> Validador y organizador</li>
                                <li><strong>Base de Datos:</strong> Almacén persistente</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-database mr-2"></i>Modelos Involucrados</h6>
                            <ul class="mb-0 small">
                                <li><strong>Eiplanningwk:</strong> Planificación principal</li>
                                <li><strong>Eiplanningwstrategy:</strong> Estrategias diarias</li>
                                <li><strong>Eiplanningwsummary:</strong> Resúmenes por área</li>
                                <li><strong>Eiprojectk:</strong> Proyecto vinculado</li>
                                <li><strong>Pevaluacion:</strong> Áreas de aprendizaje</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-cogs mr-2"></i>Sistema Involucrado</h6>
                            <p class="mb-0">Sistema de Planificación Semanal</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagrama Principal Extendido -->
            <div class="diagram-container">
                <h5 class="text-center mb-3"><i class="fas fa-sitemap mr-2"></i>Diagrama Completo de Casos de Uso</h5>
                <div class="mermaid">
                    graph TD
                    A["👤 Docente"] --> B["Crear Planificación Semanal"]
                    A --> C["Editar Planificación Existente"]
                    A --> D["Eliminar Planificación"]
                    A --> E["Consultar Planificaciones"]
                    A --> F["Exportar a PDF"]
                    A --> G["Duplicar Planificación"]
                    A --> H["Validar Completitud"]

                    B --> I["Definir Información General"]
                    B --> J["Seleccionar Proyecto Vinculado"]
                    B --> K["Agregar Estrategias Diarias"]
                    B --> L["Incluir Resúmenes por Área"]
                    B --> M["Guardar y Validar"]

                    I --> N["Seleccionar Grado y Sección"]
                    I --> O["Establecer Fechas de Período"]
                    I --> P["Definir Tiempo de Ejecución"]
                    I --> Q["Agregar Diagnóstico Inicial"]

                    K --> R["Seleccionar Momento de Rutina"]
                    K --> S["Definir Estrategia por Día"]
                    K --> T["Establecer Orden de Actividades"]

                    L --> U["Seleccionar Área de Aprendizaje"]
                    L --> V["Definir Componente Curricular"]
                    L --> W["Establecer Objetivo Específico"]
                    L --> X["Agregar Aprendizaje Esperado"]
                    L --> Y["Incluir Indicadores de Evaluación"]

                    C --> Z["Modificar Información General"]
                    C --> AA["Actualizar Estrategias"]
                    C --> BB["Cambiar Resúmenes"]
                    C --> CC["Ajustar Vinculación con Proyecto"]

                    E --> DD["Filtrar por Profesor"]
                    E --> EE["Buscar por Período"]
                    E --> FF["Filtrar por Grado/Sección"]
                    E --> GG["Ver Historial Completo"]

                    HH["📊 Sistema de Planificación"] --> II["Validar Datos Obligatorios"]
                    HH --> JJ["Verificar Coherencia Temporal"]
                    HH --> KK["Gestionar Relaciones"]
                    HH --> LL["Generar Reportes"]
                    HH --> MM["Mantener Orden Lógico"]

                    style A fill:#e1f5fe
                    style HH fill:#fff3e0
                    style B fill:#e8f5e8
                    style K fill:#fff8e1
                    style L fill:#f3e5f5
                    style E fill:#ffebee
                </div>
            </div>

            <!-- Estructura Detallada de la Planificación -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-sitemap mr-2"></i>Estructura Completa de la
                                Planificación Semanal</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card border-primary mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Eiplanningwk
                                                (Principal)</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="small text-muted mb-0">
                                                <li><strong>profesor_id:</strong> Docente responsable</li>
                                                <li><strong>grado_id:</strong> Grupo de edad</li>
                                                <li><strong>seccion_id:</strong> Sección específica</li>
                                                <li><strong>eiprojectk_id:</strong> Proyecto vinculado</li>
                                                <li><strong>finicial:</strong> Fecha de inicio</li>
                                                <li><strong>ffinal:</strong> Fecha de culminación</li>
                                                <li><strong>tiempo_ejecucion:</strong> Duración en semanas</li>
                                                <li><strong>diagnostico:</strong> Evaluación inicial</li>
                                                <li><strong>observacion:</strong> Comentarios generales</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-warning mb-3">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i
                                                    class="fas fa-calendar-day mr-2"></i>Eiplanningwstrategy
                                                (Estrategias)</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="small text-muted mb-0">
                                                <li><strong>eiplanningwk_id:</strong> Relación con planificación</li>
                                                <li><strong>momento_rutina_diaria:</strong> Momento específico</li>
                                                <li><strong>lunes:</strong> Estrategia del lunes</li>
                                                <li><strong>martes:</strong> Estrategia del martes</li>
                                                <li><strong>miercoles:</strong> Estrategia del miércoles</li>
                                                <li><strong>jueves:</strong> Estrategia del jueves</li>
                                                <li><strong>viernes:</strong> Estrategia del viernes</li>
                                                <li><strong>order:</strong> Orden de presentación</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-info mb-3">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-book mr-2"></i>Eiplanningwsummary
                                                (Resúmenes)</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="small text-muted mb-0">
                                                <li><strong>eiplanningwk_id:</strong> Relación con planificación</li>
                                                <li><strong>pevaluacion_id:</strong> Área de aprendizaje</li>
                                                <li><strong>componente:</strong> Componente curricular</li>
                                                <li><strong>objetivo:</strong> Objetivo específico</li>
                                                <li><strong>aprendizaje_esperado:</strong> Meta de aprendizaje</li>
                                                <li><strong>indicadores:</strong> Criterios de evaluación</li>
                                                <li><strong>linea_investigacion:</strong> Enfoque metodológico</li>
                                                <li><strong>enfasis_curriculares:</strong> Aspectos prioritarios</li>
                                                <li><strong>order:</strong> Orden de presentación</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Momentos de la Rutina Diaria -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-clock mr-2"></i>Momentos de la Rutina Diaria en
                                Educación Inicial</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                La rutina diaria en educación inicial está estructurada en momentos específicos
                                que favorecen el desarrollo integral de los niños y niñas.
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-warning">Momentos Iniciales</h6>
                                    <ul class="small text-muted mb-3">
                                        <li><strong>Recibimiento:</strong> Bienvenida y adaptación al ambiente</li>
                                        <li><strong>Momento Cívico:</strong> Actividades patrias y valores</li>
                                        <li><strong>Aseo-Desayuno-Aseo:</strong> Hábitos de higiene y alimentación</li>
                                    </ul>

                                    <h6 class="text-info">Períodos de Trabajo</h6>
                                    <ul class="small text-muted mb-3">
                                        <li><strong>Planificación:</strong> Niños eligen y planifican actividades</li>
                                        <li><strong>Trabajo Libre:</strong> Ejecución de actividades elegidas</li>
                                        <li><strong>Orden y Limpieza:</strong> Organización del espacio</li>
                                        <li><strong>Intercambio y Recuento:</strong> Socialización de experiencias</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">Períodos Dirigidos</h6>
                                    <ul class="small text-muted mb-3">
                                        <li><strong>Trabajos en Pequeños Grupos:</strong> Actividades focalizadas</li>
                                        <li><strong>Actividades Colectivas:</strong> Experiencias grupales</li>
                                    </ul>

                                    <h6 class="text-danger">Cierre</h6>
                                    <ul class="small text-muted mb-3">
                                        <li><strong>Despedida:</strong> Cierre y preparación para el hogar</li>
                                    </ul>

                                    <div class="alert alert-info">
                                        <small>
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Nota:</strong> Cada momento tiene una duración aproximada y
                                            puede adaptarse según las necesidades del grupo.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proceso Detallado de Creación -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-route mr-2"></i>Proceso Detallado de Creación de
                                Planificación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-play-circle fa-2x text-primary mb-2"></i>
                                            <h6>1. Inicio</h6>
                                            <p class="small text-muted mb-0">
                                                Acceso al módulo de planificación semanal
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-info-circle fa-2x text-info mb-2"></i>
                                            <h6>2. Información</h6>
                                            <p class="small text-muted mb-0">
                                                Definir datos generales y período
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-link fa-2x text-success mb-2"></i>
                                            <h6>3. Vinculación</h6>
                                            <p class="small text-muted mb-0">
                                                Asociar con proyecto de aula
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-calendar-day fa-2x text-warning mb-2"></i>
                                            <h6>4. Estrategias</h6>
                                            <p class="small text-muted mb-0">
                                                Definir actividades por día
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-book fa-2x text-danger mb-2"></i>
                                            <h6>5. Resúmenes</h6>
                                            <p class="small text-muted mb-0">
                                                Agregar áreas de aprendizaje
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-save fa-2x text-secondary mb-2"></i>
                                            <h6>6. Finalización</h6>
                                            <p class="small text-muted mb-0">
                                                Validar y guardar planificación
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detalles de cada paso -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Paso 2: Información General</h6>
                                    <ul class="small text-muted mb-3">
                                        <li>Selección de grado y sección asignada</li>
                                        <li>Definición de fechas de inicio y culminación</li>
                                        <li>Cálculo automático del tiempo de ejecución</li>
                                        <li>Redacción del diagnóstico inicial del grupo</li>
                                        <li>Observaciones generales sobre la planificación</li>
                                    </ul>

                                    <h6 class="text-success">Paso 3: Vinculación con Proyecto</h6>
                                    <ul class="small text-muted mb-3">
                                        <li>Selección de proyecto de aula activo</li>
                                        <li>Verificación de coherencia temporal</li>
                                        <li>Alineación de objetivos y metas</li>
                                        <li>Integración de líneas de investigación</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-warning">Paso 4: Estrategias Diarias</h6>
                                    <ul class="small text-muted mb-3">
                                        <li>Selección del momento de rutina diaria</li>
                                        <li>Definición de estrategia para cada día</li>
                                        <li>Consideración de recursos necesarios</li>
                                        <li>Adaptación a necesidades del grupo</li>
                                        <li>Establecimiento de orden lógico</li>
                                    </ul>

                                    <h6 class="text-danger">Paso 5: Resúmenes por Área</h6>
                                    <ul class="small text-muted mb-3">
                                        <li>Selección de áreas de aprendizaje</li>
                                        <li>Definición de componentes curriculares</li>
                                        <li>Establecimiento de objetivos específicos</li>
                                        <li>Descripción de aprendizajes esperados</li>
                                        <li>Inclusión de indicadores de evaluación</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validaciones del Sistema -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-shield-alt mr-2"></i>Validaciones Obligatorias</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-danger">Validaciones de Datos</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Fechas Coherentes:</strong> Fecha inicial anterior a la final</li>
                                <li><strong>Período Válido:</strong> Duración mínima de 1 semana</li>
                                <li><strong>Grado y Sección:</strong> Asignación válida del docente</li>
                                <li><strong>Diagnóstico:</strong> Campo obligatorio con mínimo 50 caracteres</li>
                                <li><strong>Proyecto Vinculado:</strong> Proyecto activo y compatible</li>
                            </ul>

                            <h6 class="text-warning">Validaciones de Estrategias</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Momento de Rutina:</strong> Selección de lista predefinida</li>
                                <li><strong>Estrategias por Día:</strong> Al menos 3 días con contenido</li>
                                <li><strong>Coherencia Pedagógica:</strong> Alineación con objetivos</li>
                                <li><strong>Orden Lógico:</strong> Secuencia apropiada de actividades</li>
                            </ul>

                            <h6 class="text-info">Validaciones de Resúmenes</h6>
                            <ul class="small text-muted mb-0">
                                <li><strong>Área de Aprendizaje:</strong> Selección válida y activa</li>
                                <li><strong>Componente:</strong> Relacionado con el área seleccionada</li>
                                <li><strong>Objetivo:</strong> Redacción clara y medible</li>
                                <li><strong>Indicadores:</strong> Criterios específicos y observables</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-cogs mr-2"></i>Funcionalidades Avanzadas</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-success">Gestión Inteligente</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Duplicación:</strong> Copiar planificación como plantilla</li>
                                <li><strong>Autocompletado:</strong> Sugerencias basadas en historial</li>
                                <li><strong>Validación en Tiempo Real:</strong> Verificación automática</li>
                                <li><strong>Guardado Automático:</strong> Prevención de pérdida de datos</li>
                            </ul>

                            <h6 class="text-info">Consultas y Filtros</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Por Profesor:</strong> Planificaciones del docente</li>
                                <li><strong>Por Período:</strong> Rango de fechas específico</li>
                                <li><strong>Por Grado/Sección:</strong> Grupo estudiantil</li>
                                <li><strong>Por Proyecto:</strong> Vinculadas a proyecto específico</li>
                            </ul>

                            <h6 class="text-warning">Exportación y Reportes</h6>
                            <ul class="small text-muted mb-0">
                                <li><strong>PDF Oficial:</strong> Formato institucional</li>
                                <li><strong>Configuración Personalizada:</strong> Opciones de formato</li>
                                <li><strong>Historial de Versiones:</strong> Seguimiento de cambios</li>
                                <li><strong>Estadísticas:</strong> Análisis de planificaciones</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Métodos del Sistema -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-code mr-2"></i>Métodos Principales del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-info">Eiplanningwk (Principal)</h6>
                                    <pre class="small text-muted mb-3"><code>// Relaciones
eiplanningwstrategies()
eiplanningwsummaries()
eiprojectk()
profesor()
grado()
seccion()

// Métodos de utilidad
getPevaluacions($profesor_id, $lapso_id)
getPevaluacionsList($profesor_id, $lapso_id)
getOrderedStrategies()
getOrderedSummaries()
getPeducativoAttribute()
getManagerAttribute()</code></pre>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success">Eiplanningwstrategy (Estrategias)</h6>
                                    <pre class="small text-muted mb-3"><code>// Constantes
LIST_MOMENT = [
    'Recibimiento',
    'Momento Cívico',
    'Aseo-Desayuno-Aseo',
    'Periodo: Planificación',
    'Periodo: Trabajo Libre',
    'Periodo: Orden y limpieza',
    'Periodo: Intercambio y Recuento',
    'Periodo: Trabajos en Pequeños Grupos',
    'Periodo: Actividades Colectivas',
    'Periodo: Despedida'
]

// Relación
eiplanningwk()</code></pre>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning">Eiplanningwsummary (Resúmenes)</h6>
                                    <pre class="small text-muted mb-3"><code>// Relaciones
eiplanningwk()
pevaluacion()

// Campos principales
componente
objetivo
aprendizaje_esperado
indicadores
linea_investigacion
enfasis_curriculares
order

// Ordenamiento automático
orderByRaw('CASE WHEN `order` IS NOT NULL THEN 0 ELSE 1 END')
orderBy('order')
orderBy('created_at')</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Integración con Otros Módulos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-dark">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-link mr-2"></i>Integración con Otros Módulos del
                                Sistema</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-primary"><i
                                                    class="fas fa-project-diagram mr-2"></i>Proyectos de Aula</h6>
                                            <p class="small text-muted mb-2">
                                                <strong>Relación:</strong> Cada planificación semanal puede vincularse
                                                a un proyecto de aula específico.
                                            </p>
                                            <ul class="small text-muted mb-0">
                                                <li>Coherencia de objetivos y metas</li>
                                                <li>Alineación temporal de actividades</li>
                                                <li>Integración de líneas de investigación</li>
                                                <li>Seguimiento de avance del proyecto</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-success"><i
                                                    class="fas fa-clipboard-check mr-2"></i>Sistema de Evaluación</h6>
                                            <p class="small text-muted mb-2">
                                                <strong>Relación:</strong> Las áreas de aprendizaje se conectan
                                                con las evaluaciones específicas.
                                            </p>
                                            <ul class="small text-muted mb-0">
                                                <li>Vinculación con Pevaluacion</li>
                                                <li>Indicadores de evaluación específicos</li>
                                                <li>Seguimiento de logros por área</li>
                                                <li>Retroalimentación para planificación</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-warning"><i class="fas fa-file-alt mr-2"></i>Informes
                                                Pedagógicos</h6>
                                            <p class="small text-muted mb-2">
                                                <strong>Relación:</strong> Las planificaciones alimentan los
                                                informes finales de estudiantes.
                                            </p>
                                            <ul class="small text-muted mb-0">
                                                <li>Resumen de planificación ejecutada</li>
                                                <li>Evidencias de actividades realizadas</li>
                                                <li>Logros alcanzados por área</li>
                                                <li>Observaciones del proceso</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Casos de Uso Específicos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-lightbulb mr-2"></i>Casos de Uso Específicos y
                                Ejemplos</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-primary"><i
                                                    class="fas fa-seedling mr-2"></i>Planificación: "Mi Huerto Escolar"
                                            </h6>
                                            <p class="small text-muted mb-2">
                                                <strong>Período:</strong> 1 semana (Lunes 15 - Viernes 19 Mayo)<br>
                                                <strong>Proyecto:</strong> Cuidando nuestro ambiente
                                            </p>
                                            <h6 class="small">Estrategias por Momento:</h6>
                                            <ul class="small text-muted mb-0">
                                                <li><strong>Recibimiento:</strong> Observación de plantas</li>
                                                <li><strong>Planificación:</strong> Elegir semillas a sembrar</li>
                                                <li><strong>Trabajo Libre:</strong> Preparar semilleros</li>
                                                <li><strong>Actividades Colectivas:</strong> Riego y cuidado</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-success"><i
                                                    class="fas fa-book-open mr-2"></i>Planificación: "Cuentos y
                                                Fantasías"</h6>
                                            <p class="small text-muted mb-2">
                                                <strong>Período:</strong> 1 semana (Lunes 22 - Viernes 26 Mayo)<br>
                                                <strong>Proyecto:</strong> Mundo de la literatura infantil
                                            </p>
                                            <h6 class="small">Áreas de Aprendizaje:</h6>
                                            <ul class="small text-muted mb-0">
                                                <li><strong>Comunicación y Lenguaje:</strong> Expresión oral</li>
                                                <li><strong>Formación Personal:</strong> Valores en cuentos</li>
                                                <li><strong>Relación con Ambiente:</strong> Secuencias temporales</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="text-warning"><i class="fas fa-palette mr-2"></i>Planificación:
                                                "Colores y Formas"</h6>
                                            <p class="small text-muted mb-2">
                                                <strong>Período:</strong> 1 semana (Lunes 29 Mayo - Viernes 2 Junio)<br>
                                                <strong>Proyecto:</strong> Explorando el arte y la creatividad
                                            </p>
                                            <h6 class="small">Diagnóstico Inicial:</h6>
                                            <p class="small text-muted mb-0">
                                                Los niños muestran interés por actividades artísticas.
                                                Necesitan fortalecer reconocimiento de formas geométricas
                                                y expresión creativa a través del arte.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mejores Prácticas -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-star mr-2"></i>Mejores Prácticas para Planificación
                                Semanal</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary"><i class="fas fa-user-graduate mr-2"></i>Para Docentes
                                    </h6>
                                    <ul class="small text-muted">
                                        <li>Realizar diagnóstico inicial detallado del grupo</li>
                                        <li>Vincular actividades con intereses de los niños</li>
                                        <li>Planificar considerando recursos disponibles</li>
                                        <li>Incluir actividades para diferentes estilos de aprendizaje</li>
                                        <li>Mantener flexibilidad para adaptaciones</li>
                                        <li>Documentar observaciones durante la ejecución</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fas fa-cogs mr-2"></i>Para el Sistema</h6>
                                    <ul class="small text-muted">
                                        <li>Validar coherencia entre fechas y actividades</li>
                                        <li>Sugerir actividades basadas en historial</li>
                                        <li>Mantener backup automático de planificaciones</li>
                                        <li>Generar alertas para fechas límite</li>
                                        <li>Facilitar duplicación de planificaciones exitosas</li>
                                        <li>Proporcionar plantillas predefinidas</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning"><i class="fas fa-users mr-2"></i>Para Coordinadores</h6>
                                    <ul class="small text-muted">
                                        <li>Revisar coherencia pedagógica de planificaciones</li>
                                        <li>Proporcionar retroalimentación constructiva</li>
                                        <li>Verificar alineación con proyecto institucional</li>
                                        <li>Monitorear cumplimiento de objetivos</li>
                                        <li>Facilitar recursos y materiales necesarios</li>
                                        <li>Promover intercambio de experiencias exitosas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Reemplazar la sección de data técnica con esta versión práctica -->

            <!-- Data de Ejemplo para Formularios -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-edit mr-2"></i>Ejemplo de Datos para Formularios -
                                "Explorando los Colores"</h6>
                        </div>
                        <div class="card-body">

                            <!-- Formulario Principal -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-primary mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>1. Información
                                                General de la Planificación</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold">Profesor:</label>
                                                <p class="small text-muted mb-1">María Elena Rodríguez (Seleccionado
                                                    automáticamente)</p>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold">Grado:</label>
                                                <p class="small text-muted mb-1">Maternal (3-4 años)</p>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold">Sección:</label>
                                                <p class="small text-muted mb-1">A</p>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold">Proyecto de Aula
                                                    Vinculado:</label>
                                                <p class="small text-muted mb-1">Mi Mundo de Colores y Formas</p>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold">Fecha de Inicio:</label>
                                                <p class="small text-muted mb-1">11/03/2024</p>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold">Fecha de Culminación:</label>
                                                <p class="small text-muted mb-1">15/03/2024</p>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small font-weight-bold">Tiempo de Ejecución:</label>
                                                <p class="small text-muted mb-1">1 semana (Calculado automáticamente)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-info mb-3">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-clipboard mr-2"></i>2. Diagnóstico y
                                                Observaciones</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="small font-weight-bold">Diagnóstico Inicial:</label>
                                                <div class="border p-2 bg-light">
                                                    <p class="small text-muted mb-0">
                                                        Los niños y niñas del grupo maternal muestran gran curiosidad
                                                        por los colores del entorno. Durante las actividades libres se
                                                        observa que prefieren materiales coloridos y disfrutan mezclando
                                                        témperas. Es necesario fortalecer el reconocimiento de colores
                                                        primarios y secundarios, así como desarrollar la expresión
                                                        artística y la motricidad fina a través de actividades
                                                        creativas. El grupo responde positivamente a canciones y juegos
                                                        relacionados con colores.
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label class="small font-weight-bold">Observaciones Generales:</label>
                                                <div class="border p-2 bg-light">
                                                    <p class="small text-muted mb-0">
                                                        Se recomienda tener disponibles materiales de arte variados:
                                                        témperas, pinceles, papel bond, cartulinas de colores, crayones
                                                        y marcadores. Considerar espacios amplios para actividades de
                                                        pintura y expresión corporal.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estrategias por Momento de Rutina -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-warning mb-3">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-calendar-week mr-2"></i>3. Estrategias
                                                por Momento de Rutina Diaria</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="small text-muted mb-3">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Para cada momento de rutina, definir las estrategias específicas para
                                                cada día de la semana.
                                            </p>

                                            <!-- Momento 1: Recibimiento -->
                                            <div class="card border-light mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0 text-primary">Momento: Recibimiento</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Lunes:</label>
                                                            <p class="small text-muted">Saludo con canción "Los colores
                                                                del arcoíris". Observación libre de láminas coloridas.
                                                            </p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Martes:</label>
                                                            <p class="small text-muted">Recibimiento con música
                                                                instrumental. Exploración de objetos de diferentes
                                                                colores.</p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Miércoles:</label>
                                                            <p class="small text-muted">Saludo grupal. Juego "Encuentra
                                                                el color" con objetos del aula.</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small font-weight-bold">Jueves:</label>
                                                            <p class="small text-muted">Bienvenida con títeres de
                                                                colores. Conversación sobre colores favoritos.</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small font-weight-bold">Viernes:</label>
                                                            <p class="small text-muted">Recibimiento especial. Muestra
                                                                de trabajos realizados durante la semana.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Momento 2: Planificación -->
                                            <div class="card border-light mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0 text-success">Momento: Periodo - Planificación</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Lunes:</label>
                                                            <p class="small text-muted">Los niños eligen área de arte
                                                                para pintar con colores primarios. Planificación de
                                                                actividad libre.</p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Martes:</label>
                                                            <p class="small text-muted">Selección de materiales para
                                                                crear mezclas de colores. Planificación individual de
                                                                proyecto.</p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Miércoles:</label>
                                                            <p class="small text-muted">Elección de espacios para
                                                                actividades con témperas. Organización de grupos
                                                                pequeños.</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small font-weight-bold">Jueves:</label>
                                                            <p class="small text-muted">Planificación de actividad de
                                                                estampado con diferentes colores. Selección de
                                                                materiales.</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small font-weight-bold">Viernes:</label>
                                                            <p class="small text-muted">Planificación de exposición de
                                                                trabajos. Organización de muestra artística grupal.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Momento 3: Trabajo Libre -->
                                            <div class="card border-light mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0 text-info">Momento: Periodo - Trabajo Libre</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Lunes:</label>
                                                            <p class="small text-muted">Pintura libre con colores
                                                                primarios (rojo, azul, amarillo). Exploración de
                                                                texturas y formas.</p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Martes:</label>
                                                            <p class="small text-muted">Experimentación con mezclas:
                                                                rojo+amarillo=naranja, azul+amarillo=verde,
                                                                rojo+azul=morado.</p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="small font-weight-bold">Miércoles:</label>
                                                            <p class="small text-muted">Creación de obras artísticas
                                                                usando técnica de dactilopintura con múltiples colores.
                                                            </p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small font-weight-bold">Jueves:</label>
                                                            <p class="small text-muted">Actividad de estampado con
                                                                esponjas, sellos y rodillos usando diversos colores.</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="small font-weight-bold">Viernes:</label>
                                                            <p class="small text-muted">Trabajo libre de consolidación.
                                                                Finalización de proyectos artísticos individuales.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-info">
                                                <small>
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    <strong>Nota:</strong> Se continúa de la misma manera para los 7
                                                    momentos restantes:
                                                    Momento Cívico, Aseo-Desayuno-Aseo, Orden y limpieza, Intercambio y
                                                    Recuento,
                                                    Trabajos en Pequeños Grupos, Actividades Colectivas, y Despedida.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resúmenes por Área de Aprendizaje -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-danger mb-3">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0"><i class="fas fa-book-open mr-2"></i>4. Resúmenes por
                                                Área de Aprendizaje</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="small text-muted mb-3">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Para cada área de aprendizaje, completar los siguientes campos
                                                específicos.
                                            </p>

                                            <!-- Área 1: Formación Personal y Social -->
                                            <div class="card border-light mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0 text-primary">Área de Aprendizaje: Formación
                                                        Personal y Social</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-2">
                                                                <label
                                                                    class="small font-weight-bold">Componente:</label>
                                                                <p class="small text-muted mb-1">Identidad y Género</p>
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold">Objetivo:</label>
                                                                <div class="border p-2 bg-light">
                                                                    <p class="small text-muted mb-0">
                                                                        Fortalecer la identidad personal a través de la
                                                                        expresión artística y el reconocimiento de
                                                                        preferencias individuales en cuanto a colores y
                                                                        formas.
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold">Aprendizaje
                                                                    Esperado:</label>
                                                                <div class="border p-2 bg-light">
                                                                    <p class="small text-muted mb-0">
                                                                        Que el niño y la niña expresen sus preferencias,
                                                                        gustos e intereses hacia diferentes colores,
                                                                        desarrollando su identidad personal y autoestima
                                                                        a través de actividades artísticas
                                                                        significativas.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold">Indicadores de
                                                                    Evaluación:</label>
                                                                <div class="border p-2 bg-light">
                                                                    <ul class="small text-muted mb-0">
                                                                        <li>Expresa verbalmente sus colores favoritos y
                                                                            explica por qué le gustan</li>
                                                                        <li>Muestra seguridad al elegir materiales y
                                                                            colores para sus creaciones</li>
                                                                        <li>Demuestra satisfacción y orgullo por sus
                                                                            trabajos artísticos</li>
                                                                        <li>Respeta las preferencias de colores de sus
                                                                            compañeros</li>
                                                                        <li>Participa activamente en actividades
                                                                            grupales relacionadas con colores</li>
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold">Línea de
                                                                    Investigación:</label>
                                                                <p class="small text-muted mb-1">Desarrollo de la
                                                                    identidad personal a través del arte y la expresión
                                                                    creativa</p>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="small font-weight-bold">Énfasis
                                                                    Curriculares:</label>
                                                                <p class="small text-muted mb-0">Autoestima, expresión
                                                                    personal, respeto por la diversidad, creatividad
                                                                    individual</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Área 2: Comunicación y Lenguaje -->
                                            <div class="card border-light mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0 text-success">Área de Aprendizaje: Comunicación y
                                                        Lenguaje</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-2">
                                                                <label
                                                                    class="small font-weight-bold">Componente:</label>
                                                                <p class="small text-muted mb-1">Lenguaje Oral</p>
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold">Objetivo:</label>
                                                                <div class="border p-2 bg-light">
                                                                    <p class="small text-muted mb-0">
                                                                        Enriquecer el vocabulario relacionado con
                                                                        colores, formas y técnicas artísticas,
                                                                        promoviendo la expresión oral fluida y la
                                                                        comunicación efectiva.
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold">Aprendizaje
                                                                    Esperado:</label>
                                                                <div class="border p-2 bg-light">
                                                                    <p class="small text-muted mb-0">
                                                                        Que el niño y la niña amplíen su vocabulario
                                                                        cromático y artístico, expresándose oralmente
                                                                        con claridad sobre sus experiencias, emociones y
                                                                        descubrimientos relacionados con los colores.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold">Indicadores de
                                                                    Evaluación:</label>
                                                                <div class="border p-2 bg-light">
                                                                    <ul class="small text-muted mb-0">
                                                                        <li>Nombra correctamente colores primarios y
                                                                            algunos secundarios</li>
                                                                        <li>Describe oralmente sus creaciones artísticas
                                                                            usando vocabulario apropiado</li>
                                                                        <li>Expresa emociones y sensaciones relacionadas
                                                                            con diferentes colores</li>
                                                                        <li>Participa en conversaciones grupales sobre
                                                                            experiencias artísticas</li>
                                                                        <li>Utiliza nuevo vocabulario: mezclar, pintar,
                                                                            estampar, primario, secundario</li>
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-2">
                                                                <label class="small font-weight-bold">Línea de
                                                                    Investigación:</label>
                                                                <p class="small text-muted mb-1">Desarrollo del
                                                                    lenguaje oral a través de experiencias artísticas
                                                                    significativas</p>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="small font-weight-bold">Énfasis
                                                                    Curriculares:</label>
                                                                <p class="small text-muted mb-0">Vocabulario
                                                                    especializado, expresión oral, comunicación
                                                                    efectiva, descripción de procesos</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-success">
                                                <small>
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    <strong>Nota:</strong> Se continúa de la misma manera para las áreas
                                                    restantes:
                                                    "Relación con el Ambiente" (Componente: Procesos Matemáticos) y
                                                    "Educación Estética" (Componente: Expresión Plástica).
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen de Campos Requeridos -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-dark">
                                        <div class="card-header bg-dark text-white">
                                            <h6 class="mb-0"><i class="fas fa-list-check mr-2"></i>Resumen de Campos
                                                a Completar en los Formularios</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h6 class="text-primary">Información General</h6>
                                                    <ul class="small text-muted">
                                                        <li>✅ Grado (Dropdown)</li>
                                                        <li>✅ Sección (Dropdown)</li>
                                                        <li>✅ Proyecto de Aula (Dropdown)</li>
                                                        <li>✅ Fecha de Inicio (Date)</li>
                                                        <li>✅ Fecha de Culminación (Date)</li>
                                                        <li>✅ Diagnóstico (Textarea)</li>
                                                        <li>✅ Observaciones (Textarea)</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="text-success">Estrategias Diarias</h6>
                                                    <ul class="small text-muted">
                                                        <li>✅ Momento de Rutina (Dropdown)</li>
                                                        <li>✅ Estrategia Lunes (Textarea)</li>
                                                        <li>✅ Estrategia Martes (Textarea)</li>
                                                        <li>✅ Estrategia Miércoles (Textarea)</li>
                                                        <li>✅ Estrategia Jueves (Textarea)</li>
                                                        <li>✅ Estrategia Viernes (Textarea)</li>
                                                        <li>✅ Orden (Number)</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="text-warning">Resúmenes por Área</h6>
                                                    <ul class="small text-muted">
                                                        <li>✅ Área de Aprendizaje (Dropdown)</li>
                                                        <li>✅ Componente (Text)</li>
                                                        <li>✅ Objetivo (Textarea)</li>
                                                        <li>✅ Aprendizaje Esperado (Textarea)</li>
                                                        <li>✅ Indicadores (Textarea)</li>
                                                        <li>✅ Línea de Investigación (Text)</li>
                                                        <li>✅ Énfasis Curriculares (Text)</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="text-info">Validaciones</h6>
                                                    <ul class="small text-muted">
                                                        <li>📅 Fecha inicio < Fecha final</li>
                                                        <li>📝 Diagnóstico mín. 50 caracteres</li>
                                                        <li>🎯 Al menos 3 días con estrategias</li>
                                                        <li>📚 Mínimo 2 áreas de aprendizaje</li>
                                                        <li>🔗 Proyecto activo y compatible</li>
                                                        <li>👨‍🏫 Grado/sección asignados al docente</li>
                                                        <li>✅ Todos los campos obligatorios</li>
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
            </div>



        </div>
    </div>
</div>
