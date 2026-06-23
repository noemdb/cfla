<div class="tab-pane fade" id="pedagogical-reports" role="tabpanel">
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0"><i class="fas fa-file-alt mr-2"></i>Caso de Uso: Generación de Informes Pedagógicos</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5>Descripción</h5>
                    <p class="text-muted">
                        Este caso de uso permite a los docentes generar informes finales individualizados sobre
                        el progreso de los estudiantes, documentando logros, observaciones socioafectivas,
                        participación familiar y recomendaciones. Los informes integran expectativas de
                        aprendizaje con evaluaciones específicas para proporcionar una visión integral
                        del desarrollo de cada niño.
                    </p>

                    <div class="mt-3">
                        <h6><i class="fas fa-list-ul mr-2"></i>Funcionalidades Principales:</h6>
                        <ul class="text-muted">
                            <li>Crear informes finales individualizados por estudiante</li>
                            <li>Vincular expectativas de aprendizaje con áreas específicas</li>
                            <li>Documentar logros, observaciones y participación familiar</li>
                            <li>Generar conclusiones y recomendaciones personalizadas</li>
                            <li>Exportar informes oficiales en formato PDF</li>
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
                                <li>Eifinalk</li>
                                <li>Eilearningexpectation</li>
                                <li>Eilearningarea</li>
                                <li>Pevaluacion</li>
                                <li>Estudiant</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-cogs mr-2"></i>Sistema Involucrado</h6>
                            <p class="mb-0">Sistema de Informes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagrama Principal -->
            <div class="diagram-container">
                <h5 class="text-center mb-3"><i class="fas fa-sitemap mr-2"></i>Diagrama de Casos de Uso</h5>
                <div class="mermaid">
                    graph TD
                        A["👤 Docente"] --> B["Crear Informe Final"]
                        A --> C["Editar Informe"]
                        A --> D["Consultar Informes"]
                        A --> E["Generar PDF"]
                        A --> F["Imprimir Informe"]

                        B --> G["Seleccionar Estudiante"]
                        B --> H["Definir Título del Informe"]
                        B --> I["Agregar Contexto del Grupo"]
                        B --> J["Describir Planificación Ejecutada"]
                        B --> K["Documentar Proyecto Destacado"]

                        B --> L["Registrar Actividades Especiales"]
                        B --> M["Describir Logros del Estudiante"]
                        B --> N["Agregar Observaciones Individuales"]
                        B --> O["Documentar Participación Familiar"]
                        B --> P["Incluir Conclusiones"]
                        B --> Q["Agregar Recomendaciones"]

                        C --> R["Modificar Secciones"]
                        C --> S["Actualizar Expectativas"]
                        C --> T["Cambiar Orden"]

                        D --> U["Filtrar por Estudiante"]
                        D --> V["Buscar por Período"]
                        D --> W["Ver por Sección"]

                        I --> X["Vincular Expectativas de Aprendizaje"]
                        X --> Y["Seleccionar Área de Aprendizaje"]
                        X --> Z["Asociar con Evaluación"]

                        AA["📄 Sistema de Informes"] --> BB["Validar Completitud"]
                        AA --> CC["Formatear Documento"]
                        AA --> DD["Generar PDF Oficial"]

                        style A fill:#e1f5fe
                        style AA fill:#fff3e0
                        style B fill:#f5f5f5
                        style I fill:#e8f5e8
                        style X fill:#fff8e1
                </div>
            </div>

            <!-- Estructura del Informe -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-list-ol mr-2"></i>Estructura del Informe Pedagógico Final</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary"><i class="fas fa-info-circle mr-2"></i>Información General</h6>
                                    <ul class="small text-muted mb-4">
                                        <li><strong>Título del Informe:</strong> Identificación personalizada</li>
                                        <li><strong>Estudiante:</strong> Datos del niño evaluado</li>
                                        <li><strong>Período:</strong> Lapso o momento evaluativo</li>
                                        <li><strong>Docente:</strong> Profesor responsable</li>
                                        <li><strong>Sección:</strong> Grupo al que pertenece</li>
                                    </ul>

                                    <h6 class="text-success"><i class="fas fa-users mr-2"></i>Contexto Educativo</h6>
                                    <ul class="small text-muted mb-4">
                                        <li><strong>Contexto del Grupo:</strong> Características generales</li>
                                        <li><strong>Planificación Ejecutada:</strong> Resumen de actividades</li>
                                        <li><strong>Proyecto Destacado:</strong> Proyecto más significativo</li>
                                        <li><strong>Actividades Especiales:</strong> Eventos particulares</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-warning"><i class="fas fa-star mr-2"></i>Desarrollo Individual</h6>
                                    <ul class="small text-muted mb-4">
                                        <li><strong>Logros del Estudiante:</strong> Avances significativos</li>
                                        <li><strong>Observaciones Individuales:</strong> Aspectos socioafectivos</li>
                                        <li><strong>Participación Familiar:</strong> Involucramiento de padres</li>
                                        <li><strong>Expectativas de Aprendizaje:</strong> Áreas específicas</li>
                                    </ul>

                                    <h6 class="text-danger"><i class="fas fa-lightbulb mr-2"></i>Conclusiones y Proyección</h6>
                                    <ul class="small text-muted mb-4">
                                        <li><strong>Conclusiones:</strong> Reflexión final del docente</li>
                                        <li><strong>Recomendaciones:</strong> Sugerencias para familia y equipo</li>
                                        <li><strong>Orden:</strong> Secuencia lógica del informe</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sistema de Expectativas -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-target mr-2"></i>Expectativas de Aprendizaje</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Sistema de vinculación entre áreas de aprendizaje y evaluaciones específicas.
                            </p>

                            <h6 class="text-info">Eilearningarea (Áreas de Aprendizaje)</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Grado:</strong> Grupo de edad específico</li>
                                <li><strong>Nombre:</strong> Denominación del área</li>
                                <li><strong>Descripción:</strong> Características del área</li>
                            </ul>

                            <h6 class="text-success">Eilearningexpectation (Expectativas)</h6>
                            <ul class="small text-muted mb-0">
                                <li><strong>Área de Aprendizaje:</strong> Campo curricular</li>
                                <li><strong>Descripción:</strong> Aprendizaje esperado</li>
                                <li><strong>Observaciones:</strong> Comentarios específicos</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-link mr-2"></i>Tabla Pivot: eifinalk_expectation</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                Relación many-to-many entre informes finales y expectativas de aprendizaje.
                            </p>

                            <h6 class="text-warning">Campos de la Relación</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>eifinalk_id:</strong> ID del informe final</li>
                                <li><strong>eilearningexpectation_id:</strong> ID de la expectativa</li>
                                <li><strong>eilearningarea_id:</strong> ID del área de aprendizaje</li>
                                <li><strong>pevaluacion_id:</strong> ID de la evaluación</li>
                            </ul>

                            <h6 class="text-danger">Funcionalidad</h6>
                            <p class="small text-muted mb-0">
                                Permite asociar múltiples expectativas a un informe,
                                especificando el área y la evaluación correspondiente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proceso de Creación -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-route mr-2"></i>Proceso de Creación del Informe</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-user-plus fa-2x text-primary mb-2"></i>
                                            <h6>1. Selección</h6>
                                            <p class="small text-muted mb-0">
                                                Elegir estudiante y período evaluativo
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-edit fa-2x text-info mb-2"></i>
                                            <h6>2. Información</h6>
                                            <p class="small text-muted mb-0">
                                                Completar datos generales y contexto
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-link fa-2x text-warning mb-2"></i>
                                            <h6>3. Expectativas</h6>
                                            <p class="small text-muted mb-0">
                                                Vincular áreas de aprendizaje
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-star fa-2x text-success mb-2"></i>
                                            <h6>4. Logros</h6>
                                            <p class="small text-muted mb-0">
                                                Documentar avances y logros
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-comments fa-2x text-danger mb-2"></i>
                                            <h6>5. Observaciones</h6>
                                            <p class="small text-muted mb-0">
                                                Agregar comentarios y familia
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center mb-3">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <i class="fas fa-lightbulb fa-2x text-secondary mb-2"></i>
                                            <h6>6. Conclusiones</h6>
                                            <p class="small text-muted mb-0">
                                                Reflexiones y recomendaciones
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Áreas de Aprendizaje en Educación Inicial -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-graduation-cap mr-2"></i>Áreas de Aprendizaje en Educación Inicial</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary"><i class="fas fa-child mr-2"></i>Formación Personal y Social</h6>
                                    <ul class="small text-muted">
                                        <li>Identidad y autonomía</li>
                                        <li>Convivencia (interacción social)</li>
                                        <li>Valores, normas, derechos y deberes</li>
                                        <li>Autoestima y confianza</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fas fa-comments mr-2"></i>Comunicación y Lenguaje</h6>
                                    <ul class="small text-muted">
                                        <li>Lenguaje oral</li>
                                        <li>Lenguaje escrito</li>
                                        <li>Expresión plástica</li>
                                        <li>Expresión corporal</li>
                                        <li>Expresión musical</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning"><i class="fas fa-globe mr-2"></i>Relación con el Ambiente</h6>
                                    <ul class="small text-muted">
                                        <li>Tecnología y calidad de vida</li>
                                        <li>Características, cuidado y preservación del ambiente</li>
                                        <li>Relaciones espaciales y temporales</li>
                                        <li>La matemática y sus aplicaciones</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tipos de Observaciones -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-eye mr-2"></i>Tipos de Observaciones</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-info">Observaciones Socioafectivas</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Interacción Social:</strong> Relación con pares y adultos</li>
                                <li><strong>Expresión Emocional:</strong> Manejo de sentimientos</li>
                                <li><strong>Autonomía:</strong> Independencia en actividades</li>
                                <li><strong>Autoestima:</strong> Confianza en sí mismo</li>
                            </ul>

                            <h6 class="text-success">Observaciones Cognitivas</h6>
                            <ul class="small text-muted mb-0">
                                <li><strong>Atención:</strong> Concentración en actividades</li>
                                <li><strong>Memoria:</strong> Retención de información</li>
                                <li><strong>Resolución de Problemas:</strong> Estrategias utilizadas</li>
                                <li><strong>Creatividad:</strong> Originalidad en propuestas</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-home mr-2"></i>Participación Familiar</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-success">Aspectos a Documentar</h6>
                            <ul class="small text-muted mb-3">
                                <li><strong>Asistencia a Reuniones:</strong> Participación en encuentros</li>
                                <li><strong>Apoyo en Casa:</strong> Refuerzo de aprendizajes</li>
                                <li><strong>Comunicación:</strong> Intercambio con docentes</li>
                                <li><strong>Actividades Especiales:</strong> Colaboración en eventos</li>
                            </ul>

                            <h6 class="text-warning">Recomendaciones para la Familia</h6>
                            <ul class="small text-muted mb-0">
                                <li><strong>Estrategias de Apoyo:</strong> Actividades en el hogar</li>
                                <li><strong>Recursos Sugeridos:</strong> Materiales y herramientas</li>
                                <li><strong>Seguimiento:</strong> Aspectos a observar</li>
                                <li><strong>Próximos Pasos:</strong> Metas a corto plazo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validaciones y Controles -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-shield-alt mr-2"></i>Validaciones del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li><strong>Estudiante Válido:</strong> Verificar existencia y pertenencia a sección</li>
                                <li><strong>Período Activo:</strong> Lapso evaluativo vigente</li>
                                <li><strong>Expectativas Vinculadas:</strong> Al menos una expectativa asociada</li>
                                <li><strong>Campos Obligatorios:</strong> Título, contexto y conclusiones</li>
                                <li><strong>Permisos Docente:</strong> Autorización para crear informe</li>
                                <li><strong>Coherencia Temporal:</strong> Fechas y períodos consistentes</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-cogs mr-2"></i>Métodos del Sistema</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="text-secondary">Eifinalk (Informe Final)</h6>
                            <ul class="small text-muted mb-3">
                                <li><code>expectations()</code> - Relación con expectativas</li>
                                <li><code>pevaluacion()</code> - Relación con evaluación</li>
                                <li><code>estudiant()</code> - Relación con estudiante</li>
                                <li><code>scopeByLapsoYSeccion()</code> - Filtro por período</li>
                            </ul>

                            <h6 class="text-info">Accessors Disponibles</h6>
                            <ul class="small text-muted mb-0">
                                <li><code>getProfesorAttribute()</code> - Acceso al docente</li>
                                <li><code>getSeccionAttribute()</code> - Acceso a la sección</li>
                                <li><code>getLapsoAttribute()</code> - Acceso al lapso</li>
                                <li><code>getResumenTituloAttribute()</code> - Título resumido</li>
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
                                    <h6 class="text-primary"><i class="fas fa-clipboard-check mr-2"></i>Sistema de Evaluación</h6>
                                    <p class="small text-muted mb-3">
                                        Los informes se nutren de las evaluaciones realizadas durante
                                        el período, incorporando logros y observaciones específicas.
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fas fa-project-diagram mr-2"></i>Proyectos de Aula</h6>
                                    <p class="small text-muted mb-3">
                                        Se documenta el proyecto más significativo desarrollado
                                        durante el período evaluativo.
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning"><i class="fas fa-calendar-week mr-2"></i>Planificaciones</h6>
                                    <p class="small text-muted mb-3">
                                        Se resume la planificación ejecutada y las actividades
                                        especiales realizadas durante el período.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
