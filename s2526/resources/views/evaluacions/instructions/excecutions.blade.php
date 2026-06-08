<div class="container-fluid py-1">

    <!-- Header Principal -->
    <div class="row mb-1">
        <div class="col-12">
            <div class="jumbotron alert-secondary py-2 mb-2">
                <h3 class="">
                    <i class="fas fa-list-ol text-primary"></i>
                    Sección para la Gestión de Evaluaciones
                </h3>
                <p class="text-dark">
                    Sistema integral para la planificación, ejecución y seguimiento de evaluaciones académicas
                    <small>Guía completa de procedimientos y mejores prácticas</small>
                </p>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Contenido Principal -->
        <div class="col-md-12">

            <!-- Introducción -->
            <section id="introduccion" class="guide-section pt-1 mt-1">
                <h5 class="mt-2">
                    <i class="fas fa-play-circle text-primary"></i>
                    Introducción al Módulo
                </h5>

                <div class="row">
                    <div class="col-md-8">
                        <p class="text-dark">
                            El <strong>módulo de Gestión de Evaluaciones</strong> es una plataforma completa diseñada
                            para planificar, ejecutar y dar seguimiento a todas las evaluaciones académicas dentro de la
                            institución.
                        </p>

                        <div class="tip-box">
                            <strong><i class="fas fa-lightbulb"></i> Propósito del Módulo:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Planificar y programar evaluaciones académicas</li>
                                <li>Gestionar el estado de ejecución de evaluaciones</li>
                                <li>Monitorear el registro de calificaciones</li>
                                <li>Generar reportes de progreso estudiantil</li>
                                <li>Facilitar la toma de decisiones académicas</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-tasks fa-3x text-primary mb-3"></i>
                                <h5>Características Principales</h5>
                                <span class="badge badge-primary feature-badge">Búsqueda Avanzada</span>
                                <span class="badge badge-success feature-badge">Gestión de Estados</span>
                                <span class="badge badge-info feature-badge">Cálculo de Promedios</span>
                                <span class="badge badge-warning feature-badge">Seguimiento en Tiempo Real</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Inicio Rápido -->
            <section id="quickstart" class="guide-section pt-2 mt-2 pt-2">
                <div class="mt-2">
                    <i class="fas fa-bolt text-warning"></i>
                    Inicio Rápido - Flujo en 3 Pasos
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center step-card">
                            <div class="card-body">
                                <div class="step-number mx-auto mb-3">1</div>
                                <h5>Configurar Filtros</h5>
                                <p class="small">Seleccionar criterios de búsqueda específicos</p>
                                <span class="badge badge-primary">1-2 minutos</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center step-card">
                            <div class="card-body">
                                <div class="step-number mx-auto mb-3">2</div>
                                <h5>Ejecutar Búsqueda</h5>
                                <p class="small">Obtener listado de evaluaciones filtradas</p>
                                <span class="badge badge-info">Instantáneo</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center step-card">
                            <div class="card-body">
                                <div class="step-number mx-auto mb-3">3</div>
                                <h5>Gestionar Estados</h5>
                                <p class="small">Actualizar estado de ejecución de evaluaciones</p>
                                <span class="badge badge-success">30 segundos</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="success-box">
                    <strong><i class="fas fa-rocket"></i> ¿Primera vez?</strong> Recomendamos comenzar con una
                    <strong>búsqueda básica por grado</strong> para familiarizarse con la interfaz antes de usar filtros
                    avanzados.
                </div>
            </section>

            <!-- Sección: Filtros de Búsqueda -->
            <section id="filtros" class="guide-section pt-2 mt-2 pt-2">
                <h5 class="mt-4">
                    <i class="fas fa-filter text-primary"></i>
                    Sección 1: Sistema de Filtros Avanzados
                </h5>

                <div class="text-dark">Filtros Principales Disponibles</div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th width="20%">Filtro</th>
                                <th width="30%">Descripción</th>
                                <th width="25%">Comportamiento</th>
                                <th width="25%">Dependencias</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Grado</strong></td>
                                <td>Nivel académico de los estudiantes</td>
                                <td><span class="badge badge-primary">Obligatorio</span></td>
                                <td>Actualiza Secciones, Pensums y Profesores</td>
                            </tr>
                            <tr>
                                <td><strong>Sección</strong></td>
                                <td>Grupo específico dentro del grado</td>
                                <td><span class="badge badge-info">Condicional</span></td>
                                <td>Depende de Grado seleccionado</td>
                            </tr>
                            <tr>
                                <td><strong>Momento (Lapso)</strong></td>
                                <td>Período académico de evaluación</td>
                                <td><span class="badge badge-success">Opcional</span></td>
                                <td>Independiente</td>
                            </tr>
                            <tr>
                                <td><strong>Asignatura</strong></td>
                                <td>Materia académica específica</td>
                                <td><span class="badge badge-info">Condicional</span></td>
                                <td>Depende de Grado seleccionado</td>
                            </tr>
                            <tr>
                                <td><strong>Profesor</strong></td>
                                <td>Docente responsable de la evaluación</td>
                                <td><span class="badge badge-info">Condicional</span></td>
                                <td>Depende de Grado seleccionado</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-dark mt-2">Filtros Adicionales</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Rango de Fechas</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Propósito:</strong> Filtrar evaluaciones por período específico</p>
                                <p><strong>Formato:</strong> Fecha Inicial - Fecha Final (YYYY-MM-DD)</p>
                                <p><strong>Uso:</strong> Ideal para reportes periódicos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-toggle-on"></i> Estado de Ejecución</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Opciones:</strong> Pendiente, Ejecutada, Todos</p>
                                <p><strong>Uso:</strong> Seguimiento de completitud</p>
                                <p><strong>Importancia:</strong> Control de progreso académico</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tip-box mt-3">
                    <strong><i class="fas fa-cogs"></i> Configuración Recomendada:</strong>
                    <ul class="mb-0">
                        <li>✅ Comenzar siempre seleccionando el <strong>Grado</strong></li>
                        <li>✅ Usar filtros combinados para búsquedas específicas</li>
                        <li>✅ Aplicar rango de fechas para análisis periódicos</li>
                        <li>✅ Monitorear evaluaciones <strong>Pendientes</strong> regularmente</li>
                    </ul>
                </div>
            </section>

            <!-- Sección: Interpretación de Resultados -->
            <section id="resultados" class="guide-section pt-2 mt-2 pt-2">
                <h5 class="mt-4">
                    <i class="fas fa-table text-primary"></i>
                    Sección 2: Interpretación de la Tabla de Resultados
                </h5>

                <div class="text-dark">Estructura de Columnas</div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Columna</th>
                                <th>Descripción</th>
                                <th>Formato</th>
                                <th>Indicadores Visuales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>N</strong></td>
                                <td>Número secuencial con paginación</td>
                                <td>Numérico incremental</td>
                                <td><span class="badge badge-secondary">1, 2, 3...</span></td>
                            </tr>
                            <tr>
                                <td><strong>Descripción</strong></td>
                                <td>Detalle de la evaluación</td>
                                <td>Texto abreviado (hover completo)</td>
                                <td class="text-center"><i class="fas fa-mouse-pointer text-info"></i></td>
                            </tr>
                            <tr>
                                <td><strong>Profesor</strong></td>
                                <td>Docente responsable</td>
                                <td>Nombre completo abreviado</td>
                                <td>
                                    <span class="badge badge-success">Activo</span>
                                    <span class="badge badge-secondary">Inactivo</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Asignatura</strong></td>
                                <td>Materia académica</td>
                                <td>Código + Nombre completo</td>
                                <td class="text-center"><i class="fas fa-book text-primary"></i></td>
                            </tr>
                            <tr>
                                <td><strong>Grado/Sección/Lapso</strong></td>
                                <td>Contexto académico completo</td>
                                <td>Información combinada</td>
                                <td class="text-center"><i class="fas fa-layer-group text-warning"></i></td>
                            </tr>
                            <tr>
                                <td><strong>Fecha</strong></td>
                                <td>Fecha de la evaluación</td>
                                <td>Formato legible (DD/MM/YYYY)</td>
                                <td class="text-center"><i class="fas fa-calendar text-info"></i></td>
                            </tr>
                            <tr>
                                <td><strong>Notas</strong></td>
                                <td>Cantidad de calificaciones registradas</td>
                                <td>Número entero</td>
                                <td>
                                    <span class="badge badge-danger">0 = Crítico</span>
                                    <span class="badge badge-success">>0 = Normal</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Promedio</strong></td>
                                <td>Promedio de calificaciones</td>
                                <td>Decimal (2 posiciones)</td>
                                <td class="text-center"><i class="fas fa-calculator text-success"></i></td>
                            </tr>
                            <tr>
                                <td><strong>Estado</strong></td>
                                <td>Estado de ejecución</td>
                                <td>Pendiente / Ejecutada</td>
                                <td>
                                    <span class="badge badge-warning">Pendiente</span>
                                    <span class="badge badge-success">Ejecutada</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-dark mt-2">Sistema de Colores y Estados</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-danger mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger text-white rounded p-2 mr-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-danger mb-1">Fila Roja</h6>
                                        <p class="small text-muted mb-0">
                                            Indica evaluación <strong>sin notas registradas</strong>. Requiere atención
                                            inmediata.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-left-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning text-white rounded p-2 mr-3">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-warning mb-1">Estado Pendiente</h6>
                                        <p class="small text-muted mb-0">
                                            Evaluación programada pero <strong>no ejecutada</strong>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-left-success mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success text-white rounded p-2 mr-3">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-success mb-1">Fila Normal</h6>
                                        <p class="small text-muted mb-0">
                                            Evaluación con <strong>notas registradas</strong>. Proceso completado.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success text-white rounded p-2 mr-3">
                                        <i class="fas fa-play-circle"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-success mb-1">Estado Ejecutada</h6>
                                        <p class="small text-muted mb-0">
                                            Evaluación <strong>completada y procesada</strong>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección: Gestión de Estados -->
            <section id="gestion-estados" class="guide-section pt-2 mt-2 pt-2">
                <h5 class="mt-4">
                    <i class="fas fa-cogs text-primary"></i>
                    Sección 3: Gestión de Estados de Ejecución
                </h5>

                <div class="text-dark">Acciones Disponibles</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="bg-warning text-white rounded-circle mx-auto mb-3"
                                    style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-exclamation fa-2x"></i>
                                </div>
                                <h5>Marcar como Pendiente</h5>
                                <p class="small">Cambia el estado de Ejecutada a Pendiente</p>
                                <div class="mb-3">
                                    <button class="btn btn-warning btn-sm" disabled>
                                        <i class="fas fa-exclamation mr-1"></i> Pendiente
                                    </button>
                                </div>
                                <ul class="list-unstyled small text-left">
                                    <li class="mb-1">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        Reversible en cualquier momento
                                    </li>
                                    <li class="mb-1">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        No afecta las notas registradas
                                    </li>
                                    <li class="mb-1">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        Útil para correcciones
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="bg-success text-white rounded-circle mx-auto mb-3"
                                    style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check fa-2x"></i>
                                </div>
                                <h5>Marcar como Ejecutada</h5>
                                <p class="small">Confirma la ejecución de la evaluación</p>
                                <div class="mb-3">
                                    <button class="btn btn-success btn-sm" disabled>
                                        <i class="fas fa-check mr-1"></i> Ejecutada
                                    </button>
                                </div>
                                <ul class="list-unstyled small text-left">
                                    <li class="mb-1">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        Indica completitud del proceso
                                    </li>
                                    <li class="mb-1">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        Requiere notas registradas
                                    </li>
                                    <li class="mb-1">
                                        <i class="fas fa-check text-success mr-2"></i>
                                        Es reversible si es necesario
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="warning-box mt-3">
                    <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                    El cambio de estado es <strong>independiente</strong> del registro de notas. Una evaluación puede
                    estar marcada como "Ejecutada" incluso si no tiene notas registradas.
                </div>
            </section>

            <!-- Mejores Prácticas -->
            <section id="mejores-practicas" class="guide-section pt-2 mt-2 pt-2">
                <div class="text-dark">
                    <i class="fas fa-star text-primary"></i>
                    Mejores Prácticas y Recomendaciones
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-check-circle"></i> Prácticas Recomendadas</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>Revisión Diaria:</strong> Monitorear evaluaciones pendientes cada día
                                    </li>
                                    <li><strong>Gestión Proactiva:</strong> Actualizar estados inmediatamente después de
                                        la evaluación</li>
                                    <li><strong>Verificación de Notas:</strong> Confirmar registro de calificaciones
                                        antes de marcar como ejecutada</li>
                                    <li><strong>Comunicación:</strong> Reportar evaluaciones sin notas al profesor
                                        responsable</li>
                                    <li><strong>Segmentación:</strong> Usar filtros combinados para análisis específicos
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Evitar</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li><strong>Marcado Prematuro:</strong> No marcar como ejecutada sin verificar notas
                                    </li>
                                    <li><strong>Filtros Excesivos:</strong> Evitar usar todos los filtros
                                        simultáneamente</li>
                                    <li><strong>Desactualización:</strong> No dejar estados pendientes por tiempo
                                        prolongado</li>
                                    <li><strong>Ignorar Alertas:</strong> No pasar por alto filas en color rojo</li>
                                    <li><strong>Falta de Seguimiento:</strong> No monitorear evaluaciones sin notas
                                        registradas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Preguntas Frecuentes -->
            <section id="faq" class="guide-section pt-2 mt-2 pt-2">
                <h5 class="mt-4">
                    <i class="fas fa-question-circle text-primary"></i>
                    Preguntas Frecuentes (FAQ)
                </h5>

                <div class="accordion" id="faqAccordion">
                    <!-- Pregunta 1 -->
                    <div class="card">
                        <div class="card-header" id="faq1">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                    data-target="#answer1">
                                    <i class="fas fa-question"></i> ¿Por qué algunas filas aparecen en color rojo?
                                </button>
                            </h5>
                        </div>
                        <div id="answer1" class="collapse show" data-parent="#faqAccordion">
                            <div class="card-body">
                                Las filas en <strong class="text-danger">color rojo</strong> indican evaluaciones que
                                <strong>no tienen notas registradas</strong>. Esto requiere atención inmediata para
                                contactar al profesor responsable y solicitar el registro de calificaciones.
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 2 -->
                    <div class="card">
                        <div class="card-header" id="faq2">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                    data-target="#answer2">
                                    <i class="fas fa-question"></i> ¿Puedo cambiar el estado de una evaluación
                                    múltiples veces?
                                </button>
                            </h5>
                        </div>
                        <div id="answer2" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <strong>Sí</strong>, el cambio de estado es completamente reversible. Puede alternar
                                entre "Pendiente" y "Ejecutada" tantas veces como sea necesario. Esta flexibilidad
                                permite correcciones y ajustes en el proceso de evaluación.
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 3 -->
                    <div class="card">
                        <div class="card-header" id="faq3">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                    data-target="#answer3">
                                    <i class="fas fa-question"></i> ¿Qué debo hacer si no encuentro una evaluación
                                    específica?
                                </button>
                            </h5>
                        </div>
                        <div id="answer3" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                Si no encuentra una evaluación específica:
                                <ul>
                                    <li>Verifique que todos los filtros estén configurados correctamente</li>
                                    <li>Confirme que la evaluación esté asignada al grado y sección correctos</li>
                                    <li>Verifique que el profesor esté asignado correctamente</li>
                                    <li>Considere ampliar el rango de fechas de búsqueda</li>
                                    <li>Si persiste el problema, contacte al administrador del sistema</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 4 -->
                    <div class="card">
                        <div class="card-header" id="faq4">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                    data-target="#answer4">
                                    <i class="fas fa-question"></i> ¿Cómo funciona la paginación en la tabla de
                                    resultados?
                                </button>
                            </h5>
                        </div>
                        <div id="answer4" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                La tabla muestra <strong>15 evaluaciones por página</strong>. Puede navegar entre
                                páginas usando los controles en la parte inferior de la tabla. El sistema mantiene los
                                filtros aplicados al cambiar de página. La numeración (columna N) se ajusta
                                automáticamente para reflejar la posición global en los resultados.
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 5 -->
                    <div class="card">
                        <div class="card-header" id="faq5">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                    data-target="#answer5">
                                    <i class="fas fa-question"></i> ¿Qué significa el promedio mostrado en la tabla?
                                </button>
                            </h5>
                        </div>
                        <div id="answer5" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                El <strong>promedio</strong> representa la calificación promedio de todos los
                                estudiantes en esa evaluación específica. Se calcula automáticamente a partir de las
                                notas registradas y se muestra con dos decimales para mayor precisión. Un valor de
                                "0.00" generalmente indica que no hay notas registradas o que todas las calificaciones
                                son cero.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

@section('stylesheet')
    @parent
    <style>
        .guide-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            padding-top: 1rem;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }

        .step-card {
            transition: transform 0.3s ease;
            margin-bottom: 1rem;
        }

        .step-card:hover {
            transform: translateY(-3px);
        }

        .step-number {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .feature-badge {
            font-size: 0.7rem;
            margin: 0.1rem;
            display: block;
        }

        .tip-box {
            background: #e7f3ff;
            border-left: 3px solid #17a2b8;
            padding: 0.8rem;
            margin: 0.8rem 0;
            border-radius: 0 4px 4px 0;
            font-size: 0.9rem;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            padding: 0.8rem;
            margin: 0.8rem 0;
            border-radius: 0 4px 4px 0;
            font-size: 0.9rem;
        }

        .success-box {
            background: #d4edda;
            border-left: 3px solid #28a745;
            padding: 0.8rem;
            margin: 0.8rem 0;
            border-radius: 0 4px 4px 0;
            font-size: 0.9rem;
        }

        .table th,
        .table td {
            padding: 0.5rem;
            font-size: 0.85rem;
        }

        .card-body {
            padding: 1rem;
        }

        .card-header {
            padding: 0.5rem 1rem;
        }
    </style>
@endsection
