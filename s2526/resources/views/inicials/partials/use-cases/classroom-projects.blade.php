<div class="tab-pane fade" id="classroom-projects" role="tabpanel">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-project-diagram mr-2"></i>Caso de Uso: Gestión de Proyectos de Aula</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5>Descripción</h5>
                    <p class="text-muted">
                        Este caso de uso permite a los docentes registrar y gestionar proyectos pedagógicos
                        desarrollados durante el año escolar, incluyendo diagnóstico inicial, áreas de aprendizaje,
                        componentes curriculares y revisiones del proyecto.
                    </p>

                    <div class="mt-3">
                        <h6><i class="fas fa-list-ul mr-2"></i>Funcionalidades Principales:</h6>
                        <ul class="text-muted">
                            <li>Crear y configurar nuevos proyectos de aula</li>
                            <li>Definir diagnóstico inicial y tiempo de ejecución</li>
                            <li>Agregar áreas de aprendizaje con sus componentes</li>
                            <li>Registrar revisiones y seguimiento del proyecto</li>
                            <li>Generar informes y documentación en PDF</li>
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
                                <li>Eiprojectk</li>
                                <li>Eiprojectsummary</li>
                                <li>Eiprojectreview</li>
                                <li>Pevaluacion</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-cogs mr-2"></i>Sistema Involucrado</h6>
                            <p class="mb-0">Sistema de Proyectos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagrama Principal -->
            <div class="diagram-container">
                <h5 class="text-center mb-3"><i class="fas fa-sitemap mr-2"></i>Diagrama de Casos de Uso</h5>
                <div class="mermaid">
                    graph TD
                    A["👤 Docente"] --> B["Crear Proyecto de Aula"]
                    A --> C["Editar Proyecto"]
                    A --> D["Eliminar Proyecto"]
                    A --> E["Consultar Proyectos"]
                    A --> F["Generar Informe PDF"]

                    B --> G["Definir Diagnóstico Inicial"]
                    B --> H["Establecer Tiempo de Ejecución"]
                    B --> I["Agregar Áreas de Aprendizaje"]
                    B --> J["Registrar Revisión del Proyecto"]

                    C --> K["Modificar Componentes"]
                    C --> L["Actualizar Objetivos"]
                    C --> M["Cambiar Indicadores"]

                    I --> N["Seleccionar Área de Aprendizaje"]
                    I --> O["Definir Componente"]
                    I --> P["Establecer Aprendizaje Esperado"]
                    I --> Q["Agregar Línea de Investigación"]

                    J --> R["Registrar Temas de Interés"]
                    J --> S["Documentar Qué Saben"]
                    J --> T["Definir Qué Desean Aprender"]

                    U["🎯 Sistema de Proyectos"] --> V["Validar Estructura"]
                    U --> W["Vincular con Planificaciones"]
                    U --> X["Generar Documentación"]

                    style A fill:#e1f5fe
                    style U fill:#fff3e0
                    style B fill:#e8f5e8
                    style I fill:#fff8e1
                    style J fill:#f3e5f5
                </div>
            </div>

            <!-- Detalles Adicionales -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-clipboard-list mr-2"></i>Flujo de Creación</h6>
                        </div>
                        <div class="card-body">
                            <ol class="mb-0 small">
                                <li><strong>Selección de Contexto:</strong> Grado, sección y período</li>
                                <li><strong>Diagnóstico Inicial:</strong> Evaluación de necesidades</li>
                                <li><strong>Planificación:</strong> Tiempo de ejecución y objetivos</li>
                                <li><strong>Áreas de Aprendizaje:</strong> Componentes curriculares</li>
                                <li><strong>Revisión:</strong> Seguimiento y evaluación</li>
                                <li><strong>Documentación:</strong> Generación de informes</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Validaciones del Sistema
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li><strong>Estructura del Proyecto:</strong> Campos obligatorios completos</li>
                                <li><strong>Fechas Coherentes:</strong> Inicio anterior a finalización</li>
                                <li><strong>Áreas de Aprendizaje:</strong> Al menos un área asignada</li>
                                <li><strong>Vinculación:</strong> Relación con planificaciones semanales</li>
                                <li><strong>Permisos:</strong> Docente autorizado para la sección</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Componentes del Proyecto -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-puzzle-piece mr-2"></i>Componentes del Proyecto de Aula
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-info"><i class="fas fa-search mr-2"></i>Revisión del Proyecto</h6>
                                    <ul class="small text-muted">
                                        <li>Posibles temas de interés</li>
                                        <li>Elección del tema y nombre</li>
                                        <li>Qué saben los estudiantes</li>
                                        <li>Qué desean aprender</li>
                                        <li>Qué necesitamos</li>
                                        <li>Quiénes nos pueden apoyar</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fas fa-book mr-2"></i>Áreas de Aprendizaje</h6>
                                    <ul class="small text-muted">
                                        <li>Componente del área</li>
                                        <li>Objetivo específico</li>
                                        <li>Aprendizaje esperado</li>
                                        <li>Indicadores de evaluación</li>
                                        <li>Línea de investigación</li>
                                        <li>Énfasis curriculares</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning"><i class="fas fa-cog mr-2"></i>Gestión del Proyecto</h6>
                                    <ul class="small text-muted">
                                        <li>Diagnóstico inicial</li>
                                        <li>Tiempo de ejecución</li>
                                        <li>Observaciones generales</li>
                                        <li>Vinculación con planificaciones</li>
                                        <li>Seguimiento y evaluación</li>
                                        <li>Documentación final</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relaciones con otros módulos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-link mr-2"></i>Integración con Otros Módulos</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary"><i class="fas fa-calendar-week mr-2"></i>Planificación
                                        Semanal</h6>
                                    <p class="small text-muted mb-3">
                                        Los proyectos de aula se vinculan directamente con las planificaciones
                                        semanales,
                                        permitiendo que las actividades diarias estén alineadas con los objetivos del
                                        proyecto.
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success"><i class="fas fa-clipboard-check mr-2"></i>Sistema de
                                        Evaluación</h6>
                                    <p class="small text-muted mb-3">
                                        Las áreas de aprendizaje del proyecto se relacionan con las evaluaciones,
                                        facilitando el seguimiento del progreso estudiantil.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="classroom-projects" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-project-diagram mr-2"></i>Caso de Uso: Gestión de Proyectos
                            de Aula</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5>Descripción</h5>
                                <p class="text-muted">
                                    Este caso de uso permite a los docentes de educación inicial crear, desarrollar y
                                    gestionar
                                    proyectos de aula integrales que sirven como eje articulador del proceso educativo.
                                    Los
                                    proyectos de aula son estrategias pedagógicas que parten de los intereses y
                                    necesidades
                                    de los niños, promoviendo aprendizajes significativos a través de la investigación,
                                    exploración y construcción colectiva del conocimiento durante períodos extendidos.
                                </p>

                                <div class="mt-3">
                                    <h6><i class="fas fa-list-ul mr-2"></i>Funcionalidades Principales:</h6>
                                    <ul class="text-muted">
                                        <li>Crear proyectos de aula con duración de 3-4 semanas</li>
                                        <li>Definir objetivos generales y específicos del proyecto</li>
                                        <li>Establecer líneas de investigación y ejes temáticos</li>
                                        <li>Planificar actividades por fases de desarrollo</li>
                                        <li>Gestionar recursos y materiales necesarios</li>
                                        <li>Documentar procesos y evidencias de aprendizaje</li>
                                        <li>Evaluar logros y impacto del proyecto</li>
                                        <li>Generar informes de seguimiento y cierre</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6><i class="fas fa-users mr-2"></i>Actores del Sistema</h6>
                                        <ul class="mb-0 small">
                                            <li><strong>Docente:</strong> Facilitador y planificador</li>
                                            <li><strong>Niños:</strong> Protagonistas del aprendizaje</li>
                                            <li><strong>Familias:</strong> Colaboradores activos</li>
                                            <li><strong>Coordinador:</strong> Supervisor pedagógico</li>
                                            <li><strong>Comunidad:</strong> Fuente de recursos</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6><i class="fas fa-database mr-2"></i>Modelo Principal</h6>
                                        <ul class="mb-0 small">
                                            <li><strong>Eiprojectk:</strong> Proyecto de aula</li>
                                            <li><strong>Relaciones:</strong> Profesor, Grado, Sección</li>
                                            <li><strong>Vinculaciones:</strong> Planificaciones semanales</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-cogs mr-2"></i>Sistema Involucrado</h6>
                                        <p class="mb-0">Sistema de Proyectos Pedagógicos</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagrama Principal Extendido -->
                        <div class="diagram-container">
                            <h5 class="text-center mb-3"><i class="fas fa-sitemap mr-2"></i>Diagrama Completo de Casos
                                de Uso</h5>
                            <div class="mermaid">
                                graph TD
                                A["👤 Docente"] --> B["Crear Proyecto de Aula"]
                                A --> C["Editar Proyecto Existente"]
                                A --> D["Eliminar Proyecto"]
                                A --> E["Consultar Proyectos"]
                                A --> F["Duplicar Proyecto"]
                                A --> G["Generar Informe de Proyecto"]
                                A --> H["Evaluar Impacto del Proyecto"]

                                B --> I["Definir Información General"]
                                B --> J["Establecer Objetivos"]
                                B --> K["Planificar Fases de Desarrollo"]
                                B --> L["Definir Recursos Necesarios"]
                                B --> M["Establecer Criterios de Evaluación"]
                                B --> N["Guardar y Activar Proyecto"]

                                I --> O["Seleccionar Grado y Sección"]
                                I --> P["Definir Título del Proyecto"]
                                I --> Q["Establecer Período de Ejecución"]
                                I --> R["Describir Justificación"]
                                I --> S["Definir Línea de Investigación"]

                                J --> T["Formular Objetivo General"]
                                J --> U["Establecer Objetivos Específicos"]
                                J --> V["Vincular con Áreas de Aprendizaje"]
                                J --> W["Definir Competencias a Desarrollar"]

                                K --> X["Fase de Inicio - Diagnóstico"]
                                K --> Y["Fase de Desarrollo - Investigación"]
                                K --> Z["Fase de Cierre - Socialización"]
                                K --> AA["Cronograma de Actividades"]

                                L --> BB["Recursos Humanos"]
                                L --> CC["Recursos Materiales"]
                                L --> DD["Recursos Tecnológicos"]
                                L --> EE["Recursos del Entorno"]

                                E --> FF["Filtrar por Profesor"]
                                E --> GG["Buscar por Período"]
                                E --> HH["Filtrar por Estado"]
                                E --> II["Ver Proyectos Activos"]
                                E --> JJ["Consultar Historial"]

                                KK["👥 Niños y Familias"] --> LL["Participar en Diagnóstico"]
                                KK --> MM["Aportar Ideas y Sugerencias"]
                                KK --> NN["Colaborar en Actividades"]
                                KK --> OO["Proporcionar Recursos"]

                                PP["📊 Sistema de Proyectos"] --> QQ["Validar Coherencia Temporal"]
                                PP --> RR["Verificar Recursos Disponibles"]
                                PP --> SS["Generar Alertas de Seguimiento"]
                                PP --> TT["Calcular Indicadores de Progreso"]
                                PP --> UU["Mantener Historial de Cambios"]

                                style A fill:#e1f5fe
                                style KK fill:#f3e5f5
                                style PP fill:#fff3e0
                                style B fill:#e8f5e8
                                style K fill:#fff8e1
                                style E fill:#ffebee
                            </div>
                        </div>

                        <!-- Data de Ejemplo para Formularios -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-edit mr-2"></i>Ejemplo de Datos para
                                            Formularios - "Los Animales de Mi Comunidad"</h6>
                                    </div>
                                    <div class="card-body">

                                        <!-- Formulario Principal -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card border-primary mb-3">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>1.
                                                            Información General del Proyecto</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold">Profesor:</label>
                                                            <p class="small text-muted mb-1">Carmen Luisa Pérez
                                                                (Seleccionado automáticamente)</p>
                                                        </div>

                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold">Grado:</label>
                                                            <p class="small text-muted mb-1">Preescolar (4-5 años)</p>
                                                        </div>

                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold">Sección:</label>
                                                            <p class="small text-muted mb-1">B</p>
                                                        </div>

                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold">Título del
                                                                Proyecto:</label>
                                                            <p class="small text-muted mb-1">Los Animales de Mi
                                                                Comunidad</p>
                                                        </div>

                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold">Fecha de
                                                                Inicio:</label>
                                                            <p class="small text-muted mb-1">01/04/2024</p>
                                                        </div>

                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold">Fecha de
                                                                Culminación:</label>
                                                            <p class="small text-muted mb-1">26/04/2024</p>
                                                        </div>

                                                        <div class="form-group mb-2">
                                                            <label class="small font-weight-bold">Duración:</label>
                                                            <p class="small text-muted mb-1">4 semanas (Calculado
                                                                automáticamente)</p>
                                                        </div>

                                                        <div class="form-group mb-0">
                                                            <label class="small font-weight-bold">Estado:</label>
                                                            <p class="small text-muted mb-0">Activo</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card border-info mb-3">
                                                    <div class="card-header bg-info text-white">
                                                        <h6 class="mb-0"><i class="fas fa-clipboard mr-2"></i>2.
                                                            Justificación y Línea de Investigación</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group mb-3">
                                                            <label class="small font-weight-bold">Justificación del
                                                                Proyecto:</label>
                                                            <div class="border p-2 bg-light">
                                                                <p class="small text-muted mb-0">
                                                                    Los niños y niñas del grupo preescolar han mostrado
                                                                    gran interés por los animales que observan en su
                                                                    entorno cercano: perros, gatos, pájaros, mariposas e
                                                                    insectos del jardín de la escuela. Durante las
                                                                    actividades libres frecuentemente hacen preguntas
                                                                    sobre dónde viven, qué comen y cómo se cuidan estos
                                                                    animales. Este proyecto surge de la necesidad de
                                                                    sistematizar estos conocimientos, promover el
                                                                    respeto por los seres vivos y desarrollar actitudes
                                                                    de cuidado hacia el ambiente natural de su
                                                                    comunidad.
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label class="small font-weight-bold">Línea de
                                                                Investigación:</label>
                                                            <p class="small text-muted mb-1">Conocimiento y cuidado del
                                                                ambiente natural comunitario</p>
                                                        </div>

                                                        <div class="form-group mb-0">
                                                            <label class="small font-weight-bold">Palabras
                                                                Clave:</label>
                                                            <p class="small text-muted mb-0">Animales, comunidad,
                                                                cuidado, ambiente, respeto, observación, investigación
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Objetivos del Proyecto -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card border-success mb-3">
                                                    <div class="card-header bg-success text-white">
                                                        <h6 class="mb-0"><i class="fas fa-bullseye mr-2"></i>3.
                                                            Objetivo General</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="border p-3 bg-light">
                                                            <p class="small text-muted mb-0">
                                                                <strong>Promover el conocimiento, respeto y cuidado de
                                                                    los animales presentes en la comunidad,
                                                                    desarrollando actitudes de responsabilidad ambiental
                                                                    y fortaleciendo habilidades de
                                                                    observación, investigación y expresión oral a través
                                                                    de experiencias directas y
                                                                    significativas con el entorno natural
                                                                    cercano.</strong>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card border-warning mb-3">
                                                    <div class="card-header bg-warning text-dark">
                                                        <h6 class="mb-0"><i class="fas fa-list-ol mr-2"></i>4.
                                                            Objetivos Específicos</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="border p-2 bg-light">
                                                            <ol class="small text-muted mb-0">
                                                                <li class="mb-2">Identificar y clasificar los
                                                                    animales presentes en la comunidad escolar y del
                                                                    hogar según sus características observables.</li>
                                                                <li class="mb-2">Investigar y describir las
                                                                    necesidades básicas de los animales: alimentación,
                                                                    refugio, cuidados y hábitat natural.</li>
                                                                <li class="mb-2">Desarrollar actitudes de respeto,
                                                                    cuidado y protección hacia los animales del entorno
                                                                    comunitario.</li>
                                                                <li class="mb-2">Fortalecer habilidades de
                                                                    observación, registro y comunicación oral a través
                                                                    de la investigación sobre animales.</li>
                                                                <li class="mb-0">Promover la participación familiar
                                                                    en actividades de cuidado y protección de animales
                                                                    domésticos y del entorno.</li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fases del Proyecto -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card border-danger mb-3">
                                                    <div class="card-header bg-danger text-white">
                                                        <h6 class="mb-0"><i class="fas fa-tasks mr-2"></i>5. Fases
                                                            de Desarrollo del Proyecto</h6>
                                                    </div>
                                                    <div class="card-body">

                                                        <!-- Fase 1: Inicio y Diagnóstico -->
                                                        <div class="card border-light mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0 text-primary">Fase 1: Inicio y
                                                                    Diagnóstico (Semana 1: 01-05 Abril)</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="small font-weight-bold">Actividades
                                                                            Principales:</label>
                                                                        <ul class="small text-muted mb-2">
                                                                            <li>Conversatorio inicial: "¿Qué animales
                                                                                conocemos?"</li>
                                                                            <li>Recorrido por los espacios de la escuela
                                                                                para observar animales</li>
                                                                            <li>Registro gráfico de animales observados
                                                                                (dibujos libres)</li>
                                                                            <li>Lluvia de ideas sobre cuidados que
                                                                                necesitan los animales</li>
                                                                            <li>Carta a las familias solicitando
                                                                                colaboración</li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="small font-weight-bold">Recursos
                                                                            Necesarios:</label>
                                                                        <ul class="small text-muted mb-2">
                                                                            <li>Hojas blancas y materiales de dibujo
                                                                            </li>
                                                                            <li>Cámara fotográfica para registro</li>
                                                                            <li>Cartelera para exposición de trabajos
                                                                            </li>
                                                                            <li>Formato de carta para familias</li>
                                                                        </ul>

                                                                        <label class="small font-weight-bold">Productos
                                                                            Esperados:</label>
                                                                        <ul class="small text-muted mb-0">
                                                                            <li>Diagnóstico inicial de conocimientos
                                                                            </li>
                                                                            <li>Registro fotográfico de animales del
                                                                                entorno</li>
                                                                            <li>Primeras producciones gráficas de los
                                                                                niños</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Fase 2: Desarrollo e Investigación -->
                                                        <div class="card border-light mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0 text-success">Fase 2: Desarrollo e
                                                                    Investigación (Semanas 2-3: 08-19 Abril)</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="small font-weight-bold">Actividades
                                                                            Principales:</label>
                                                                        <ul class="small text-muted mb-2">
                                                                            <li>Investigación con familias sobre
                                                                                mascotas del hogar</li>
                                                                            <li>Visita de especialista veterinario o
                                                                                cuidador de animales</li>
                                                                            <li>Creación de fichas informativas sobre
                                                                                animales</li>
                                                                            <li>Construcción de hábitats con materiales
                                                                                reciclados</li>
                                                                            <li>Dramatizaciones sobre cuidado de
                                                                                animales</li>
                                                                            <li>Elaboración de normas para el cuidado de
                                                                                animales</li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="small font-weight-bold">Recursos
                                                                            Necesarios:</label>
                                                                        <ul class="small text-muted mb-2">
                                                                            <li>Materiales reciclados (cajas, botellas,
                                                                                cartón)</li>
                                                                            <li>Libros y láminas sobre animales</li>
                                                                            <li>Disfraces y materiales para
                                                                                dramatización</li>
                                                                            <li>Cartulinas para fichas informativas</li>
                                                                            <li>Invitado especialista</li>
                                                                        </ul>

                                                                        <label class="small font-weight-bold">Productos
                                                                            Esperados:</label>
                                                                        <ul class="small text-muted mb-0">
                                                                            <li>Fichas informativas de animales</li>
                                                                            <li>Maquetas de hábitats</li>
                                                                            <li>Registro de la visita del especialista
                                                                            </li>
                                                                            <li>Normas de cuidado elaboradas por los
                                                                                niños</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Fase 3: Cierre y Socialización -->
                                                        <div class="card border-light mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0 text-warning">Fase 3: Cierre y
                                                                    Socialización (Semana 4: 22-26 Abril)</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="small font-weight-bold">Actividades
                                                                            Principales:</label>
                                                                        <ul class="small text-muted mb-2">
                                                                            <li>Organización de exposición "Nuestros
                                                                                Amigos Animales"</li>
                                                                            <li>Presentación de trabajos a otros grupos
                                                                            </li>
                                                                            <li>Actividad con familias: "Feria de
                                                                                Mascotas Responsables"</li>
                                                                            <li>Evaluación grupal del proyecto realizado
                                                                            </li>
                                                                            <li>Compromiso grupal para el cuidado de
                                                                                animales</li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="small font-weight-bold">Recursos
                                                                            Necesarios:</label>
                                                                        <ul class="small text-muted mb-2">
                                                                            <li>Espacio para exposición</li>
                                                                            <li>Mesas y soportes para exhibición</li>
                                                                            <li>Invitaciones para familias</li>
                                                                            <li>Refrigerio para actividad de cierre</li>
                                                                        </ul>

                                                                        <label class="small font-weight-bold">Productos
                                                                            Esperados:</label>
                                                                        <ul class="small text-muted mb-0">
                                                                            <li>Exposición organizada de trabajos</li>
                                                                            <li>Presentaciones orales de los niños</li>
                                                                            <li>Compromiso grupal documentado</li>
                                                                            <li>Evaluación final del proyecto</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Áreas de Aprendizaje Involucradas -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card border-secondary mb-3">
                                                    <div class="card-header bg-secondary text-white">
                                                        <h6 class="mb-0"><i class="fas fa-book-open mr-2"></i>6.
                                                            Áreas de Aprendizaje Involucradas</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="card border-light mb-2">
                                                                    <div class="card-body">
                                                                        <h6 class="text-primary small">Relación con el
                                                                            Ambiente</h6>
                                                                        <ul class="small text-muted mb-0">
                                                                            <li><strong>Conocimiento de la
                                                                                    Naturaleza:</strong> Características
                                                                                de animales</li>
                                                                            <li><strong>Procesos Matemáticos:</strong>
                                                                                Clasificación y conteo</li>
                                                                            <li><strong>Conocimiento Social:</strong>
                                                                                Animales en la comunidad</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <div class="card border-light mb-2">
                                                                    <div class="card-body">
                                                                        <h6 class="text-success small">Comunicación y
                                                                            Lenguaje</h6>
                                                                        <ul class="small text-muted mb-0">
                                                                            <li><strong>Lenguaje Oral:</strong>
                                                                                Descripciones y narraciones</li>
                                                                            <li><strong>Lenguaje Escrito:</strong>
                                                                                Registro de observaciones</li>
                                                                            <li><strong>Expresión Corporal:</strong>
                                                                                Dramatizaciones</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="card border-light mb-2">
                                                                    <div class="card-body">
                                                                        <h6 class="text-warning small">Formación
                                                                            Personal y Social</h6>
                                                                        <ul class="small text-muted mb-0">
                                                                            <li><strong>Cuidado y Seguridad:</strong>
                                                                                Normas de convivencia con animales</li>
                                                                            <li><strong>Autoestima:</strong> Valoración
                                                                                de aportes individuales</li>
                                                                            <li><strong>Expresión de
                                                                                    Sentimientos:</strong> Amor por los
                                                                                animales</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <div class="card border-light mb-2">
                                                                    <div class="card-body">
                                                                        <h6 class="text-info small">Educación Estética
                                                                        </h6>
                                                                        <ul class="small text-muted mb-0">
                                                                            <li><strong>Expresión Plástica:</strong>
                                                                                Dibujos y maquetas</li>
                                                                            <li><strong>Expresión Musical:</strong>
                                                                                Canciones sobre animales</li>
                                                                            <li><strong>Imitación y Juego:</strong>
                                                                                Representación de animales</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Recursos y Materiales -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card border-info mb-3">
                                                    <div class="card-header bg-info text-white">
                                                        <h6 class="mb-0"><i class="fas fa-toolbox mr-2"></i>7.
                                                            Recursos Necesarios</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <h6 class="small text-info">Recursos Humanos:</h6>
                                                        <ul class="small text-muted mb-2">
                                                            <li>Docente de aula (facilitador principal)</li>
                                                            <li>Familias de los estudiantes</li>
                                                            <li>Especialista veterinario o cuidador</li>
                                                            <li>Personal de apoyo de la institución</li>
                                                        </ul>

                                                        <h6 class="small text-success">Recursos Materiales:</h6>
                                                        <ul class="small text-muted mb-2">
                                                            <li>Materiales de arte: papel, colores, témperas</li>
                                                            <li>Materiales reciclados para construcciones</li>
                                                            <li>Libros y láminas sobre animales</li>
                                                            <li>Cámara fotográfica</li>
                                                            <li>Cartulinas y marcadores</li>
                                                        </ul>

                                                        <h6 class="small text-warning">Recursos del Entorno:</h6>
                                                        <ul class="small text-muted mb-0">
                                                            <li>Espacios verdes de la escuela</li>
                                                            <li>Animales presentes en la institución</li>
                                                            <li>Mascotas de las familias</li>
                                                            <li>Veterinaria o tienda de mascotas cercana</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card border-success mb-3">
                                                    <div class="card-header bg-success text-white">
                                                        <h6 class="mb-0"><i class="fas fa-chart-line mr-2"></i>8.
                                                            Evaluación y Seguimiento</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <h6 class="small text-primary">Criterios de Evaluación:</h6>
                                                        <ul class="small text-muted mb-2">
                                                            <li>Participación activa en actividades</li>
                                                            <li>Demostración de conocimientos sobre animales</li>
                                                            <li>Actitudes de cuidado y respeto</li>
                                                            <li>Habilidades de observación y registro</li>
                                                            <li>Expresión oral de experiencias</li>
                                                        </ul>

                                                        <h6 class="small text-success">Instrumentos de Evaluación:</h6>
                                                        <ul class="small text-muted mb-2">
                                                            <li>Observación directa y registro anecdótico</li>
                                                            <li>Portafolio de trabajos de los niños</li>
                                                            <li>Fotografías de procesos y productos</li>
                                                            <li>Lista de cotejo de logros</li>
                                                            <li>Autoevaluación grupal</li>
                                                        </ul>

                                                        <h6 class="small text-warning">Indicadores de Logro:</h6>
                                                        <ul class="small text-muted mb-0">
                                                            <li>Identifica al menos 5 animales de su entorno</li>
                                                            <li>Describe cuidados básicos de animales</li>
                                                            <li>Demuestra actitudes de respeto hacia animales</li>
                                                            <li>Participa en actividades de investigación</li>
                                                            <li>Expresa oralmente sus aprendizajes</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cronograma Semanal -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card border-dark">
                                                    <div class="card-header bg-dark text-white">
                                                        <h6 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>9.
                                                            Cronograma General del Proyecto</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th>Semana</th>
                                                                        <th>Fechas</th>
                                                                        <th>Fase</th>
                                                                        <th>Actividades Principales</th>
                                                                        <th>Productos</th>
                                                                        <th>Responsables</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="small">
                                                                    <tr>
                                                                        <td><strong>1</strong></td>
                                                                        <td>01-05 Abril</td>
                                                                        <td class="text-primary">Inicio y Diagnóstico
                                                                        </td>
                                                                        <td>Conversatorio inicial, recorrido, registro
                                                                            gráfico</td>
                                                                        <td>Diagnóstico, fotos, dibujos</td>
                                                                        <td>Docente, Niños</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>2</strong></td>
                                                                        <td>08-12 Abril</td>
                                                                        <td class="text-success">Desarrollo</td>
                                                                        <td>Investigación familiar, visita especialista
                                                                        </td>
                                                                        <td>Fichas informativas, registro visita</td>
                                                                        <td>Docente, Familias, Especialista</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>3</strong></td>
                                                                        <td>15-19 Abril</td>
                                                                        <td class="text-success">Desarrollo</td>
                                                                        <td>Construcción hábitats, dramatizaciones</td>
                                                                        <td>Maquetas, normas de cuidado</td>
                                                                        <td>Docente, Niños</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>4</strong></td>
                                                                        <td>22-26 Abril</td>
                                                                        <td class="text-warning">Cierre</td>
                                                                        <td>Exposición, feria con familias</td>
                                                                        <td>Exposición, compromiso grupal</td>
                                                                        <td>Docente, Niños, Familias</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Resumen de Campos para Formularios -->
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="card border-primary">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0"><i
                                                                class="fas fa-list-check mr-2"></i>Resumen de Campos a
                                                            Completar en los Formularios</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <h6 class="text-primary">Información General</h6>
                                                                <ul class="small text-muted">
                                                                    <li>✅ Título del Proyecto (Text)</li>
                                                                    <li>✅ Grado (Dropdown)</li>
                                                                    <li>✅ Sección (Dropdown)</li>
                                                                    <li>✅ Fecha de Inicio (Date)</li>
                                                                    <li>✅ Fecha de Culminación (Date)</li>
                                                                    <li>✅ Justificación (Textarea)</li>
                                                                    <li>✅ Línea de Investigación (Text)</li>
                                                                    <li>✅ Palabras Clave (Text)</li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-success">Objetivos</h6>
                                                                <ul class="small text-muted">
                                                                    <li>✅ Objetivo General (Textarea)</li>
                                                                    <li>✅ Objetivos Específicos (Textarea)</li>
                                                                    <li>✅ Competencias a Desarrollar (Textarea)</li>
                                                                    <li>✅ Áreas de Aprendizaje (Multiple Select)</li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-warning">Planificación</h6>
                                                                <ul class="small text-muted">
                                                                    <li>✅ Fases del Proyecto (Repeatable)</li>
                                                                    <li>✅ Actividades por Fase (Textarea)</li>
                                                                    <li>✅ Recursos Necesarios (Textarea)</li>
                                                                    <li>✅ Productos Esperados (Textarea)</li>
                                                                    <li>✅ Cronograma (Table)</li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <h6 class="text-info">Evaluación</h6>
                                                                <ul class="small text-muted">
                                                                    <li>✅ Criterios de Evaluación (Textarea)</li>
                                                                    <li>✅ Instrumentos (Textarea)</li>
                                                                    <li>✅ Indicadores de Logro (Textarea)</li>
                                                                    <li>✅ Estado del Proyecto (Select)</li>
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="alert alert-success mt-3">
                                                            <h6><i class="fas fa-check-circle mr-2"></i>Validaciones
                                                                del Sistema</h6>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <ul class="small mb-0">
                                                                        <li>✅ Título único por docente y período</li>
                                                                        <li>✅ Duración mínima de 3 semanas</li>
                                                                        <li>✅ Duración máxima de 6 semanas</li>
                                                                        <li>✅ Fechas coherentes (inicio < final)</li>
                                                                        <li>✅ Justificación mínimo 100 caracteres</li>
                                                                    </ul>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <ul class="small mb-0">
                                                                        <li>✅ Objetivo general obligatorio</li>
                                                                        <li>✅ Mínimo 3 objetivos específicos</li>
                                                                        <li>✅ Al menos 2 áreas de aprendizaje</li>
                                                                        <li>✅ Mínimo 3 fases de desarrollo</li>
                                                                        <li>✅ Grado/sección asignados al docente</li>
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
            </div>
        </div>
    </div>
</div>
