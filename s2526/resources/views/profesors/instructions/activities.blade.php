<div class="container-fluid">
    <div class="row"> <!-- Contenido Principal -->
        <div class="col-md-12"> <!-- Header -->
            <div class="page-header mb-2">
                <div class="text-primary"> <i class="fas fa-chalkboard-teacher mr-3"></i> Guía - Planificación de
                    Actividades Académicas </div>
                <p class="lead text-muted">Sistema de registro y gestión de actividades educativas para profesores</p>
                <div class="alert alert-info"> <strong><i class="fas fa-info-circle"></i> Flujo:</strong> Crear, editar y
                    gestionar actividades académicas con sus respectivos indicadores de logro para el plan de
                    evaluación. </div>
            </div>



            <!-- Resumen Ejecutivo -->
            <section id="resumen" class="guide-section mb-2">
                <h3 class="mb-2">
                    <i class="fas fa-clipboard-list text-primary"></i>
                    Resumen
                </h3>

                <div class="row">
                    <div class="col-md-8">
                        <h5>Función del Módulo</h5>
                        <ul>
                            <li><strong>Propósito:</strong> Planificar y gestionar actividades académicas con
                                indicadores de logro</li>
                            <li><strong>Destinatarios:</strong> Profesores y coordinadores académicos</li>
                            <li><strong>Flexibilidad:</strong> Múltiples actividades por plan de evaluación</li>
                            <li><strong>Evaluación:</strong> Sistema de ponderación para indicadores cuantitativos</li>
                        </ul>

                        <h6 class="mt-4">Configuración Típica</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Elemento</th>
                                        <th>Descripción</th>
                                        <th>Ejemplo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Actividad Académica</strong></td>
                                        <td>Unidad de enseñanza con fechas y contenidos</td>
                                        <td>"Espiritualidad Amigoniana - Semana 1"</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Indicadores de Logro</strong></td>
                                        <td>Criterios específicos de evaluación</td>
                                        <td>"Reconoce elementos del carisma amigoniano"</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ponderación</strong></td>
                                        <td>Valor numérico para evaluación cuantitativa</td>
                                        <td>5 puntos</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>Formato de Salida</strong></td>
                                        <td><strong>PDF Completo y Resumen</strong></td>
                                        <td class="text-primary"><strong>GENERACIÓN AUTOMÁTICA</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-tasks fa-3x text-primary mb-3"></i>
                                <h5>Flujo del Proceso</h5>
                                <span class="badge badge-primary feature-badge">Paso 1: Acceder</span>
                                <span class="badge badge-success feature-badge">Paso 2: Crear</span>
                                <span class="badge badge-info feature-badge">Paso 3: Configurar</span>
                                <span class="badge badge-warning feature-badge">Paso 4: Generar</span>

                                <div class="mt-3">
                                    <small class="text-muted">Tiempo estimado: 5-10 minutos por actividad</small>
                                    <br>
                                    <small class="text-muted">*Múltiples actividades por plan</small>
                                </div>
                            </div>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Beneficio Clave:</strong>
                            Centralización de la planificación académica con generación automática de formatos
                            institucionales.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 1: ACCESO AL MÓDULO DE ACTIVIDADES -->
            <section id="paso1" class="guide-section mb-2">

                <div class="d-flex justify-content-between">

                    <h3 class="mb-2">
                        <i class="fas fa-sign-in-alt text-primary"></i>
                        Paso 1: Acceso al Módulo de Actividades
                    </h3>
                    @include('profesors.instructions.flyers.access')

                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <h5>1.1 Navegación al Módulo</h5>
                        <ol>
                            <li>Iniciar sesión en el sistema SAEFL con credenciales de profesor</li>
                            <li>Navegar a: <code>Módulo Profesor → Plan de Actividades</code></li>
                            <li>Interfaz principal muestra listado de áreas de formación asignadas</li>
                            <li>Identificar el plan de evaluación (áreas de formación - momento académico) correspondiente</li>
                            <li>Click en botón <span class="badge badge-info"><i class="fas fa-book fa-1x"></i>
                                    </span> Actividades</li>
                        </ol>

                        <h5 class="mt-4">1.2 Estructura de la Interfaz Principal</h5>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-columns"></i> Distribución:</strong>
                            La interfaz se divide en sección de observaciones, listado de actividades y controles de
                            gestión.
                        </div>

                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-list"></i> Elementos de la Interfaz</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Observaciones Coordinación:</strong></td>
                                        <td>
                                            <span class="badge badge-warning">Sección informativa</span><br>
                                            <small>Comentarios y observaciones del coordinador evaluador</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Listado Actividades:</strong></td>
                                        <td>
                                            <span class="badge badge-info">Contenido principal</span><br>
                                            <small>Actividades registradas ordenadas por fecha</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Controles de Acción:</strong></td>
                                        <td>
                                            <span class="badge badge-success">Botones de gestión</span><br>
                                            <small>Crear, editar, eliminar actividades e indicadores</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-list-alt fa-2x text-primary mb-3"></i>
                                <p class="mb-1"><strong>Vista Principal</strong></p>
                                <small class="text-muted">Listado de actividades</small>
                                <div class="mt-3">
                                    <span class="badge badge-primary">OBSERVACIONES</span>
                                    <span class="badge badge-success">ACTIVIDADES</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">Estados de Actividad</h5>
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="text-info">Indicadores Visuales</h6>
                                <ul class="mb-0 small">
                                    <li><span class="badge badge-success">Verde</span> Incluida en resumen</li>
                                    <li><span class="badge badge-warning">Amarillo</span> Sin actividad evaluativa</li>
                                    <li><span class="badge badge-secondary">Gris</span> En edición</li>
                                </ul>
                            </div>
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                            Verificar que el lapso académico permita ediciones según las políticas institucionales.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 2: CREACIÓN DE NUEVAS ACTIVIDADES -->
            <section id="paso2" class="guide-section my-2">

                <div class="d-flex justify-content-between">
                    <h3 class="">
                        <i class="fas fa-plus-circle text-success"></i>
                        Paso 2: Creación de Nuevas Actividades
                    </h3>
                    @include('profesors.instructions.flyers.registerActivities')
                </div>                

                <div class="row">
                    <div class="col-md-8">
                        <h5>2.1 Inicio de Creación</h5>
                        <div class="alert alert-success">
                            <strong><i class="fas fa-play-circle"></i> Acción Inicial:</strong>
                            Click en botón "Registrar nueva actividad" para abrir el formulario de creación.
                        </div>

                        <ol>
                            <li>En la interfaz principal, ubicar botón <span class="badge badge-primary"><i
                                        class="fas fa-plus"></i> Registrar</span></li>
                            <li>Formulario de creación aparece en ventana/modal</li>
                            <li>Completar campos obligatorios marcados con asterisco (*)</li>
                            <li>Revisar validaciones en tiempo real</li>
                            <li>Click en <span class="badge badge-success">Guardar</span> para confirmar</li>
                        </ol>

                        <h5 class="mt-4">2.2 Campos del Formulario - Información Básica</h5>
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Configuración Temporal</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Campo</th>
                                            <th>Tipo</th>
                                            <th>Obligatorio</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Fecha Inicial</strong></td>
                                            <td>Date</td>
                                            <td><span class="badge badge-danger">SÍ</span></td>
                                            <td>Inicio de la actividad</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha Final</strong></td>
                                            <td>Date</td>
                                            <td><span class="badge badge-danger">SÍ</span></td>
                                            <td>Fin de la actividad</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <h5 class="mt-4">2.3 Campos del Formulario - Contenido Académico</h5>
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-book"></i> Información Pedagógica</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <td><strong>Tema Generador/Énfasis</strong></td>
                                        <td><span class="badge badge-danger">SÍ</span></td>
                                        <td>Tema principal y enfoque de la actividad</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tejido Temático/T. Indispensable</strong></td>
                                        <td><span class="badge badge-danger">SÍ</span></td>
                                        <td>Contenidos específicos y esenciales</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Referentes Teórico Prácticos</strong></td>
                                        <td><span class="badge badge-danger">SÍ</span></td>
                                        <td>Bases teóricas y prácticas</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Actividad Evaluativa</strong></td>
                                        <td><span class="badge badge-secondary">NO</span></td>
                                        <td>Define si aparece en resumen PDF</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-edit fa-2x text-success mb-3"></i>
                                <p class="mb-1"><strong>Formulario Actividad</strong></p>
                                <small class="text-muted">Creación/edición</small>
                                <div class="mt-3">
                                    <span class="badge badge-success">FECHAS</span>
                                    <span class="badge badge-warning">CONTENIDO</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">2.4 Proceso de Enseñanza-Aprendizaje</h5>
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="text-info">Campos Adicionales</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Enseñanza/Actividad Globalizada:</strong> Estrategias didácticas</li>
                                    <li><strong>Aprendizaje:</strong> Resultados esperados</li>
                                    <li><strong>ODS/Sistematización:</strong> Objetivos desarrollo sostenible</li>
                                    <li><strong>Comentarios J.Área:</strong> Retroalimentación jefe de área</li>
                                </ul>
                            </div>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Mejor Práctica:</strong>
                            Completar el campo "Actividad Evaluativa" para que la actividad aparezca en el resumen PDF.
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Validación:</strong>
                            Las fechas finales no pueden ser anteriores a las fechas iniciales. El sistema mostrará
                            error.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 3: EDICIÓN Y ACTUALIZACIÓN DE ACTIVIDADES -->
            <section id="paso4" class="guide-section mb-2">
                <div class="d-flex justify-content-between">
                    <h3 class="mb-2">
                        <i class="fas fa-edit text-primary"></i>
                        Paso 3: Edición y Actualización de Actividades
                    </h3>
                    @include('profesors.instructions.flyers.editActivities')
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <h5>3.1 Acceso a la Edición de Actividades</h5>
                        <div class="alert alert-primary">
                            <strong><i class="fas fa-sync-alt"></i> Flexibilidad:</strong>
                            Las actividades pueden modificarse en cualquier momento mientras el lapso académico permita ediciones.
                        </div>

                        <ol>
                            <li>En el listado principal de actividades, ubicar la actividad a modificar</li>
                            <li>Identificar botón <span class="badge badge-warning"><i class="fas fa-edit btn btn-warning btn-sm"></i> </span></li>
                            <li>Click en el botón de edición</li>
                            <li>El formulario se abre en la ventana con los datos actuales precargados</li>
                            <li>Realizar las modificaciones necesarias</li>
                            <li>Click en <span class="badge badge-primary btn btn-primary">Guardar</span> para actualizar</li>
                        </ol>

                        <h5 class="mt-4">3.2 Campos Editables</h5>
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-list-alt"></i> Información Modificable</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Campo</th>
                                            <th>Editabilidad</th>
                                            <th>Consideraciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Fechas (Inicial/Final)</strong></td>
                                            <td><span class="badge badge-success">COMPLETA</span></td>
                                            <td>Validación: fecha final ≥ fecha inicial</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contenidos Académicos</strong></td>
                                            <td><span class="badge badge-success">COMPLETA</span></td>
                                            <td>Tema, tejido temático, referentes</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Proceso Enseñanza-Aprendizaje</strong></td>
                                            <td><span class="badge badge-success">COMPLETA</span></td>
                                            <td>Estrategias y resultados esperados</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Actividad Evaluativa</strong></td>
                                            <td><span class="badge badge-success">COMPLETA</span></td>
                                            <td>Define inclusión en resumen PDF</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <h5 class="mt-4">3.3 Escenarios Comunes de Edición</h5>
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-scenario"></i> Casos de Uso Frecuentes</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><span class="badge badge-warning">Reprogramación</span></td>
                                        <td>Ajustar fechas por cambios en calendario académico</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-warning">Ampliación</span></td>
                                        <td>Agregar contenido adicional no considerado inicialmente</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-warning">Corrección</span></td>
                                        <td>Rectificar información errónea o incompleta</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-warning">Optimización</span></td>
                                        <td>Mejorar descripciones o estrategias didácticas</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-edit fa-2x text-primary mb-3"></i>
                                <p class="mb-1"><strong>Edición de Actividad</strong></p>
                                <small class="text-muted">Formulario con datos actuales</small>
                                <div class="mt-3">
                                    <span class="badge badge-primary">DATOS PRECARGADOS</span>
                                    <span class="badge badge-success">GUARDAR CAMBIOS</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">3.4 Estados Durante la Edición</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Estado Visual</th>
                                        <th>Significado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-warning">Resaltado</span></td>
                                        <td>Actividad en proceso de edición</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-success">Normal</span></td>
                                        <td>Actividad no seleccionada para edición</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-info">Procesando...</span></td>
                                        <td>Guardando cambios en el sistema</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Mejor Práctica:</strong>
                            Revisar todos los indicadores de logro después de editar una actividad para mantener coherencia.
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                            Los cambios se reflejan inmediatamente en los formatos PDF al generarlos nuevamente.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 4: GESTIÓN DE INDICADORES DE LOGRO -->
            <section id="paso3" class="guide-section mb-2">
                <div class="d-flex justify-content-between">
                    <h3 class="mb-2">
                        <i class="fas fa-bullseye text-warning"></i>
                        Paso 4: Gestión de Indicadores de Logro
                    </h3>
                    @include('profesors.instructions.flyers.manageIndicators')
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <h5>4.1 Agregar Indicadores</h5>
                        <div class="alert alert-warning">
                            <strong><i class="fas fa-bullseye"></i> Especificación:</strong>
                            Cada actividad puede tener múltiples indicadores de logro con o sin ponderación numérica.
                        </div>

                        <ol>
                            <li>En listado de actividades, ubicar actividad destino</li>
                            <li>Click en botón <span class="badge badge-info"><i class="fas fa-plus"></i> Indicador</span></li>
                            <li>Formulario de indicadores aparece en la ventana</li>
                            <li>Completar nombre del indicador</li>
                            <li>Definir si es cuantitativo y asignar ponderación</li>
                            <li>Click en <span class="badge badge-success">Guardar</span></li>
                        </ol>

                        <h5 class="mt-4">4.2 Configuración de Ponderación</h5>
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-balance-scale"></i> Sistema de Puntos</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Opción</th>
                                            <th>Descripción</th>
                                            <th>Rango</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Indicador Cualitativo</strong></td>
                                            <td>Sin valor numérico, solo descriptivo</td>
                                            <td>N/A</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Indicador Cuantitativo</strong></td>
                                            <td>Con valor numérico para evaluación</td>
                                            <td>1-20 puntos</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-info mt-2">
                                    <small>
                                        <strong>Nota:</strong> El sistema calcula automáticamente el total de ponderaciones por actividad.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">4.3 Edición y Eliminación de Indicadores</h5>
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-edit"></i> Gestión de Indicadores Existentes</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><span class="badge badge-warning">Editar</span></td>
                                        <td>Click en icono <i class="fas fa-edit"></i> junto al indicador</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-danger">Eliminar</span></td>
                                        <td>Click en icono <i class="fas fa-trash"></i> (solo si no hay restricciones)</td>
                                    </tr>
                                </table>
                                <div class="alert alert-warning mt-2">
                                    <small>
                                        <strong>Restricción:</strong> No se pueden eliminar actividades que contengan indicadores de logro.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-bullseye fa-2x text-warning mb-3"></i>
                                <p class="mb-1"><strong>Indicadores de Logro</strong></p>
                                <small class="text-muted">Gestión de criterios</small>
                                <div class="mt-3">
                                    <span class="badge badge-warning">CUALITATIVO</span>
                                    <span class="badge badge-success">CUANTITATIVO</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">4.4 Ejemplos de Indicadores</h5>
                        <div class="success-box">
                            <ul class="mb-0">
                                <li>✅ "Reconoce elementos del carisma amigoniano"</li>
                                <li>✅ "Identifica las partes de la misa" [5 puntos]</li>
                                <li>✅ "Distingue valores amigonianos" [3 puntos]</li>
                                <li>✅ "Describe el perfil del estudiante amigoniano"</li>
                            </ul>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Estrategia:</strong>
                            Combinar indicadores cualitativos y cuantitativos para una evaluación integral.
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Atención:</strong>
                            La suma total de ponderaciones se muestra automáticamente. Verificar coherencia.
                        </div>
                    </div>
                </div>
            </section>            

            <!-- PASO 5: GENERACIÓN DE FORMATOS Y REPORTES (RENUMERADO) -->
            <section id="paso5" class="guide-section mb-2">
                <h2 class="mb-2">
                    <i class="fas fa-file-pdf text-info"></i>
                    Paso 5: Generación de Formatos y Reportes
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>5.1 Tipos de Reportes Disponibles</h4>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-file-alt"></i> Salidas del Sistema:</strong>
                            Generación automática de formatos institucionales en PDF.
                        </div>

                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-print"></i> Opciones de Exportación</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Reporte</th>
                                            <th>Contenido</th>
                                            <th>Acceso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Plan de Actividades</strong></td>
                                            <td>Formato completo con todas las actividades</td>
                                            <td>Botón <span class="badge badge-success"><i class="fas fa-file-pdf"></i></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Resumen Plan de Actividades</strong></td>
                                            <td>Solo actividades con evaluación definida</td>
                                            <td>Botón <span class="badge badge-info"><i class="fas fa-file-pdf"></i></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <h4 class="mt-4">5.2 Características de los Formatos PDF</h4>
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-file-pdf"></i> Estructura de Reportes</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Formato Completo:</strong></td>
                                        <td>Incluye todas las actividades registradas</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Formato Resumen:</strong></td>
                                        <td>Solo actividades con campo "Actividad Evaluativa"</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Información de Cabecera:</strong></td>
                                        <td>Institución, grado, asignatura, periodo, profesor</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estructura Tabular:</strong></td>
                                        <td>Columnas organizadas por componentes académicos</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h4 class="mt-4">5.3 Proceso de Generación</h4>
                        <ol>
                            <li>Verificar que todas las actividades estén correctamente registradas y actualizadas</li>
                            <li>Click en botón correspondiente al tipo de reporte deseado</li>
                            <li>El sistema genera PDF en nueva pestaña del navegador</li>
                            <li>Revisar contenido y formato del documento generado</li>
                            <li>Descargar o imprimir según necesidad</li>
                        </ol>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-print fa-2x text-info mb-3"></i>
                                <p class="mb-1"><strong>Generación de PDF</strong></p>
                                <small class="text-muted">Formatos institucionales</small>
                                <div class="mt-3">
                                    <span class="badge badge-success">COMPLETO</span>
                                    <span class="badge badge-info">RESUMEN</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">5.4 Validación Pre-Generación</h5>
                        <div class="success-box">
                            <ul class="mb-0">
                                <li>✅ Actividades con fechas válidas</li>
                                <li>✅ Contenidos académicos completos</li>
                                <li>✅ Indicadores de logro definidos</li>
                                <li>✅ Información de contexto correcta</li>
                                <li>✅ Permisos de edición verificados</li>
                            </ul>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Recomendación:</strong>
                            Generar ambos formatos (completo y resumen) para diferentes usos institucionales.
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Consideración:</strong>
                            Los formatos PDF reflejan exactamente la información registrada en el sistema al momento de la generación.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 6: FUNCIONALIDADES AVANZADAS -->
            <section id="paso5" class="guide-section mb-2">
                <h2 class="mb-2">
                    <i class="fas fa-cogs text-secondary"></i>
                    Paso 6: Funcionalidades Avanzadas
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>5.1 Clonación de Actividades</h4>
                        <div class="alert alert-secondary">
                            <strong><i class="fas fa-copy"></i> Eficiencia:</strong>
                            Copiar actividades entre secciones del mismo grado para ahorrar tiempo.
                        </div>

                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-clone"></i> Proceso de Clonación</h6>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Seleccionar sección destino en dropdown</li>
                                    <li>Click en botón <span class="badge badge-info"><i class="fas fa-copy"></i>
                                            Clonar</span></li>
                                    <li>Sistema verifica existencia de plan en sección destino</li>
                                    <li>Si existe, copia actividades e indicadores</li>
                                    <li>Muestra confirmación de operación exitosa</li>
                                </ol>
                                <div class="alert alert-info mt-2">
                                    <small>
                                        <strong>Nota:</strong> Solo disponible cuando no hay actividades registradas en
                                        el plan actual.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-4">5.2 Eliminación Masiva</h4>
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-trash-alt"></i> Vaciar Actividades</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Función:</strong></td>
                                        <td>Eliminar todas las actividades del plan</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Disponibilidad:</strong></td>
                                        <td>Cuando no hay observaciones del coordinador</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Restricción:</strong></td>
                                        <td>No disponible si el lapso no permite ediciones</td>
                                    </tr>
                                </table>
                                <div class="alert alert-warning mt-2">
                                    <small>
                                        <strong>Advertencia:</strong> Esta acción no se puede deshacer. Elimina todas
                                        las actividades e indicadores.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-magic fa-2x text-secondary mb-3"></i>
                                <p class="mb-1"><strong>Funciones Avanzadas</strong></p>
                                <small class="text-muted">Optimización de trabajo</small>
                                <div class="mt-3">
                                    <span class="badge badge-info">CLONAR</span>
                                    <span class="badge badge-danger">VACIAR</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">5.3 Estados del Sistema</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Estado</th>
                                        <th>Significado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-success">Edición Habilitada</span></td>
                                        <td>Lapso permite modificaciones</td>
                                        <td>Todas disponibles</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-secondary">Edición Deshabilitada</span></td>
                                        <td>Lapso cerrado o en revisión</td>
                                        <td>Solo consulta</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-warning">Con Observaciones</span></td>
                                        <td>Coordinador agregó comentarios</td>
                                        <td>Algunas funciones restringidas</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Estrategia:</strong>
                            Usar clonación para secciones paralelas con contenidos similares.
                        </div>
                    </div>
                </div>
            </section>

            <!-- RESUMEN FINAL -->
            <section id="resumen-final" class="guide-section mb-2">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-flag-checkered"></i> Resumen del Proceso Completado</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Estados Finales Esperados</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <td><strong>Actividades Registradas</strong></td>
                                            <td><span class="badge badge-success">COMPLETADO</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Indicadores de Logro</strong></td>
                                            <td><span class="badge badge-info">CONFIGURADOS</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Formatos PDF</strong></td>
                                            <td><span class="badge badge-primary">GENERADOS</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Plan de Actividades</strong></td>
                                            <td><span class="badge badge-warning">EN REVISIÓN</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Elementos Configurados</h5>
                                <ul>
                                    <li>Actividades académicas con fechas y contenidos</li>
                                    <li>Indicadores de logro cualitativos/cuantitativos</li>
                                    <li>Proceso de enseñanza-aprendizaje definido</li>
                                    <li>ODS y sistematización incorporados</li>
                                    <li>Formatos institucionales generados</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr>

            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> Proceso Completado</h5>
                <p class="mb-0">El plan de actividades ha sido registrado exitosamente en el sistema y los formatos
                    institucionales están disponibles para revisión y distribución.</p>
            </div>

            <hr>

            <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
                <tr>
                    <td align="center">
                        <font size="2" color="#ffffff" face="Arial">
                            Guía de Planificación de Actividades Académicas - Versión 1.0 - Basado en SAEFL
                        </font>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

@section('stylesheet')
@parent
<style>
    .guide-section {
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e9ecef;
    }

    .screenshot {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }

    .tip-box {
        background: #e7f3ff;
        border-left: 4px solid #007bff;
        padding: 1rem;
        border-radius: 0.25rem;
    }

    .warning-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 1rem;
        border-radius: 0.25rem;
    }

    .success-box {
        background: #d4edda;
        border-left: 4px solid #28a745;
        padding: 1rem;
        border-radius: 0.25rem;
    }

    .feature-badge {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.8rem;
    }
</style>
@endsection