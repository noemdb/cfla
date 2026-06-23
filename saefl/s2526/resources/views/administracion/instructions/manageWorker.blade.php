
<div class="container-fluid">
    <!-- CABECERA DE LA GUÍA -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="jumbotron py-1 my-1">
                <h3 class="">Gestión de Personal para Control de Asistencia</h3>
                <p class="lead">información del personal y sus horarios de asistencia</p>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- CONTENIDO PRINCIPAL -->
        <div class="col-md-12">
            <!-- INTRODUCCIÓN -->
            <section id="introduccion" class="guide-section">
                
                <div class="row">
                    <div class="col-md-8">
                        <p class="">
                            La sección para la gestión de Personal permite administrar toda la información relacionada con el personal 
                            activo del sistema, incluyendo datos personales, cargos, horarios de asistencia y estados laborales.
                        </p>
                        
                        <div class="tip-box">
                            <strong><i class="fas fa-lightbulb"></i> Propósito:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Centralizar la información del personal activo</li>
                                <li>Gestionar horarios y cargos de manera eficiente</li>
                                <li>Facilitar el control de asistencia automatizado</li>
                                <li>Mantener actualizados los datos laborales</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h5>Características Principales</h5>
                                <span class="badge badge-primary feature-badge">Paginación</span>
                                <span class="badge badge-success feature-badge">Búsqueda Avanzada</span>
                                <span class="badge badge-info feature-badge">Edición en Tiempo Real</span>
                                <span class="badge badge-warning feature-badge">Validación de Datos</span>
                                <span class="badge badge-danger feature-badge">Responsive</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- INICIO RÁPIDO -->
            <section id="quickstart" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-bolt text-warning"></i>
                    Inicio Rápido
                </h2>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center step-card">
                            <div class="card-body">
                                <div class="step-number mx-auto mb-3">1</div>
                                <h5>Acceder al Módulo</h5>
                                <p class="small">Navegar a Administración → Control de Asistencia → Personal</p>
                                <span class="badge badge-primary">1 min</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center step-card">
                            <div class="card-body">
                                <div class="step-number mx-auto mb-3">2</div>
                                <h5>Buscar Personal</h5>
                                <p class="small">Utilizar la barra de búsqueda para localizar empleados específicos</p>
                                <span class="badge badge-info">30 seg</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center step-card">
                            <div class="card-body">
                                <div class="step-number mx-auto mb-3">3</div>
                                <h5>Editar Información</h5>
                                <p class="small">Hacer clic en el botón editar y actualizar los datos necesarios</p>
                                <span class="badge badge-success">2 min</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="success-box">
                    <strong><i class="fas fa-rocket"></i> ¿Primera vez?</strong> Comienza explorando la lista de personal y familiarizándote con las opciones de búsqueda y filtrado disponibles.
                </div>
            </section>

            <!-- CONCEPTOS CLAVE -->
            <section id="conceptos" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-cogs text-primary"></i>
                    Conceptos Clave
                </h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-tie"></i>
                                    Cargo Actual vs Cargo Asignado
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-primary">Definición:</h6>
                                <p>Distinción entre el cargo actualmente vigente del empleado y el cargo asignado en el sistema.</p>
                                
                                <h6 class="text-primary">Características:</h6>
                                <ul>
                                    <li><strong>Cargo Actual:</strong> Determinado por fechas de vigencia y estado activo</li>
                                    <li><strong>Cargo Asignado:</strong> Configuración manual en el sistema</li>
                                    <li><strong>Validación:</strong> El sistema verifica horarios activos y fechas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock"></i>
                                    Estado del Horario
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-warning">Definición:</h6>
                                <p>Indica si el horario asignado al empleado está activo o inactivo en el sistema.</p>
                                
                                <h6 class="text-warning">Aplicación:</h6>
                                <ul>
                                    <li><strong>Activo:</strong> El empleado está sujeto a control de asistencia</li>
                                    <li><strong>Inactivo:</strong> No se registra asistencia para este empleado</li>
                                    <li><strong>Vigencia:</strong> Se considera fechas de inicio y fin configuradas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-sort-numeric-down"></i>
                                    Orden de Trabajo
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-info">Definición:</h6>
                                <p>Número que determina la posición del empleado en listados y reportes.</p>
                                
                                <h6 class="text-info">Importancia:</h6>
                                <ul>
                                    <li>Organización visual en interfaces</li>
                                    <li>Ordenamiento en reportes automáticos</li>
                                    <li>Priorización en procesos específicos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-id-card"></i>
                                    Identificadores Únicos
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-success">Definición:</h6>
                                <p>Conjunto de identificadores que garantizan la unicidad de cada empleado.</p>
                                
                                <h6 class="text-success">Tipos:</h6>
                                <ul>
                                    <li><strong>Cédula:</strong> Identificación principal</li>
                                    <li><strong>Work ID:</strong> Identificador laboral</li>
                                    <li><strong>Card ID:</strong> Identificador de tarjeta</li>
                                    <li><strong>Ident:</strong> Identificador alternativo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ACCEDER AL MÓDULO -->
            <section id="acceder-modulo" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-door-open text-primary"></i>
                    Acceder al Módulo de Gestión
                </h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <h4>Procedimiento Paso a Paso</h4>
                        <ol>
                            <li>
                                <strong>Iniciar Sesión</strong> - Acceder al sistema con credenciales autorizadas
                                <ul>
                                    <li>Usuario debe tener permisos de administración</li>
                                    <li>Verificar que la sesión esté activa</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Navegar al Módulo</strong> - Seguir la ruta de navegación
                                <ul>
                                    <li>Menú Principal → Administración</li>
                                    <li>Control de Asistencia → Personal</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Verificar Interfaz</strong> - Confirmar que se cargue correctamente
                                <ul>
                                    <li>Listado de personal visible</li>
                                    <li>Barra de búsqueda funcional</li>
                                    <li>Controles de paginación activos</li>
                                </ul>
                            </li>
                        </ol>
                        
                        <div class="tip-box">
                            <strong><i class="fas fa-lightbulb"></i> Consejo Práctico:</strong>
                            Agrega esta página a tus favoritos del navegador para acceder rápidamente en futuras ocasiones.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-list fa-2x text-primary mb-3"></i>
                                <p class="mb-1"><strong>Vista Principal del Módulo</strong></p>
                                <small class="text-muted">Interfaz de gestión con listado y controles</small>
                                <div class="mt-3">
                                    <span class="badge badge-primary">Listado</span>
                                    <span class="badge badge-warning">Búsqueda</span>
                                    <span class="badge badge-info">Paginación</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- GESTIONAR PERSONAL -->
            <section id="gestionar-personal" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-users text-primary"></i>
                    Gestionar Información del Personal
                </h2>
                
                <div class="row">
                    <div class="col-md-12">
                        <h4>Editar Datos de un Empleado</h4>
                        <ol>
                            <li>
                                <strong>Localizar Empleado</strong> - Encontrar al empleado en el listado
                                <ul>
                                    <li>Usar la barra de búsqueda si es necesario</li>
                                    <li>Navegar entre páginas con la paginación</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Iniciar Edición</strong> - Hacer clic en el botón de edición (✏️)
                                <ul>
                                    <li>El formulario aparecerá en el lado derecho</li>
                                    <li>Los datos actuales se cargarán automáticamente</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Actualizar Información</strong> - Modificar los campos necesarios
                                <ul>
                                    <li><strong>Datos Personales:</strong> Nombres, apellidos, cédula</li>
                                    <li><strong>Identificadores:</strong> Work ID, Card ID, Ident</li>
                                    <li><strong>Información Laboral:</strong> Cargo, horario, orden</li>
                                    <li><strong>Estado:</strong> Activar/desactivar horario</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Guardar Cambios</strong> - Hacer clic en "Actualizar"
                                <ul>
                                    <li>El sistema validará los datos automáticamente</li>
                                    <li>Mostrará mensaje de confirmación</li>
                                    <li>Los cambios se reflejarán inmediatamente</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>
                
                <div class="warning-box">
                    <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong> 
                    Los campos de identificación (cédula, work_id, ident, card_id) deben ser únicos en el sistema. 
                    Si intentas usar un valor existente, el sistema mostrará un error de validación.
                </div>

                <div class="tip-box">
                    <strong><i class="fas fa-lightbulb"></i> Buenas Prácticas:</strong>
                    <ul class="mb-0">
                        <li>Verifica siempre la información antes de guardar</li>
                        <li>Utiliza la función de búsqueda para empleados con nombres similares</li>
                        <li>Mantén actualizado el campo "Orden de Trabajo" para reportes consistentes</li>
                    </ul>
                </div>
            </section>

            <!-- BÚSQUEDA Y FILTROS -->
            <section id="buscar-filtrar" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-search text-primary"></i>
                    Búsqueda y Filtrado Avanzado
                </h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <h4>Funcionalidades de Búsqueda</h4>
                        <p>El sistema cuenta con capacidades avanzadas de búsqueda en tiempo real:</p>
                        
                        <ul>
                            <li>
                                <strong>Búsqueda por Texto</strong>
                                <ul>
                                    <li>Nombres y apellidos</li>
                                    <li>Número de cédula</li>
                                    <li>Work ID y identificadores alternativos</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Paginación Configurable</strong>
                                <ul>
                                    <li>10, 25, 50 o 100 registros por página</li>
                                    <li>Navegación intuitiva entre páginas</li>
                                    <li>Contador de resultados visibles</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Filtros Automáticos</strong>
                                <ul>
                                    <li>Solo muestra personal activo</li>
                                    <li>Filtra por grupo de empleados</li>
                                    <li>Considera horarios activos únicamente</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-filter fa-2x text-primary mb-3"></i>
                                <p class="mb-1"><strong>Controles de Búsqueda</strong></p>
                                <small class="text-muted">Barra de búsqueda y selector de items</small>
                                <div class="mt-3">
                                    <span class="badge badge-primary">Búsqueda</span>
                                    <span class="badge badge-success">Items/Página</span>
                                    <span class="badge badge-info">Resultados</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="success-box">
                    <strong><i class="fas fa-magic"></i> Característica Inteligente:</strong> 
                    La búsqueda se ejecuta automáticamente mientras escribes (con delay de 300ms) 
                    y reinicia la paginación para mostrar los resultados más relevantes primero.
                </div>
            </section>

            <!-- TABLA DE CAMPOS -->
            <section id="tabla-campos" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-table text-primary"></i>
                    Referencia de Campos del Formulario
                </h2>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-custom">
                        <thead class="thead-dark">
                            <tr>
                                <th width="20%">Campo</th>
                                <th width="30%">Descripción</th>
                                <th width="20%">Tipo/Validación</th>
                                <th width="30%">Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Cédula</strong></td>
                                <td>Número de identificación principal</td>
                                <td><span class="badge badge-info">Entero único</span></td>
                                <td>Campo único en el sistema</td>
                            </tr>
                            <tr>
                                <td><strong>Work ID</strong></td>
                                <td>Identificador laboral interno</td>
                                <td><span class="badge badge-warning">Texto (10 chars)</span></td>
                                <td>Debe ser único opcional</td>
                            </tr>
                            <tr>
                                <td><strong>Nombres</strong></td>
                                <td>Nombres del empleado</td>
                                <td><span class="badge badge-success">Texto (3-64 chars)</span></td>
                                <td>Opcional pero recomendado</td>
                            </tr>
                            <tr>
                                <td><strong>Apellidos</strong></td>
                                <td>Apellidos del empleado</td>
                                <td><span class="badge badge-success">Texto (3-64 chars)</span></td>
                                <td>Opcional pero recomendado</td>
                            </tr>
                            <tr>
                                <td><strong>Orden Trabajo</strong></td>
                                <td>Posición en listados</td>
                                <td><span class="badge badge-primary">Número</span></td>
                                <td>Para ordenamiento visual</td>
                            </tr>
                            <tr>
                                <td><strong>Cargo</strong></td>
                                <td>Posición laboral asignada</td>
                                <td><span class="badge badge-info">Selección</span></td>
                                <td>Lista predefinida de cargos</td>
                            </tr>
                            <tr>
                                <td><strong>Horario</strong></td>
                                <td>Horario de asistencia</td>
                                <td><span class="badge badge-info">Selección</span></td>
                                <td>Lista predefinida de horarios</td>
                            </tr>
                            <tr>
                                <td><strong>Estado Horario</strong></td>
                                <td>Activo/Inactivo</td>
                                <td><span class="badge badge-danger">Booleano</span></td>
                                <td>Controla registro de asistencia</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- PREGUNTAS FRECUENTES -->
            <section id="faq" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-question-circle text-primary"></i>
                    Preguntas Frecuentes (FAQ)
                </h2>
                
                <div class="accordion" id="faqAccordion">
                    <!-- PREGUNTA 1 -->
                    <div class="card">
                        <div class="card-header" id="faq1">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#answer1">
                                    <i class="fas fa-question"></i> ¿Por qué no puedo guardar los cambios en un empleado?
                                </button>
                            </h5>
                        </div>
                        <div id="answer1" class="collapse show" data-parent="#faqAccordion">
                            <div class="card-body">
                                Esto ocurre cuando intentas usar un valor que ya existe en el sistema (cédula, work_id, ident o card_id). 
                                Verifica que los identificadores sean únicos. El sistema mostrará mensajes específicos indicando 
                                cuál campo está causando el conflicto.
                            </div>
                        </div>
                    </div>
                    
                    <!-- PREGUNTA 2 -->
                    <div class="card">
                        <div class="card-header" id="faq2">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer2">
                                    <i class="fas fa-question"></i> ¿Cómo encuentro rápidamente a un empleado en una lista larga?
                                </button>
                            </h5>
                        </div>
                        <div id="answer2" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                Utiliza la barra de búsqueda en la parte superior. Puedes buscar por:
                                <ul>
                                    <li>Nombre o apellido (parcial o completo)</li>
                                    <li>Número de cédula</li>
                                    <li>Work ID o identificadores alternativos</li>
                                </ul>
                                La búsqueda es en tiempo real y se actualiza automáticamente mientras escribes.
                            </div>
                        </div>
                    </div>

                    <!-- PREGUNTA 3 -->
                    <div class="card">
                        <div class="card-header" id="faq3">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer3">
                                    <i class="fas fa-question"></i> ¿Qué diferencia hay entre "Cargo" y "Cargo Actual"?
                                </button>
                            </h5>
                        </div>
                        <div id="answer3" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <strong>Cargo:</strong> Es la asignación manual que haces en el sistema.<br>
                                <strong>Cargo Actual:</strong> Es determinado automáticamente por el sistema basado en:
                                <ul>
                                    <li>Fechas de vigencia del rol</li>
                                    <li>Estado activo del horario</li>
                                    <li>El rol más reciente del empleado</li>
                                </ul>
                                Pueden coincidir o no dependiendo de la configuración actual.
                            </div>
                        </div>
                    </div>

                    <!-- PREGUNTA 4 -->
                    <div class="card">
                        <div class="card-header" id="faq4">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer4">
                                    <i class="fas fa-question"></i> ¿Qué pasa si desactivo el "Estado del Horario"?
                                </button>
                            </h5>
                        </div>
                        <div id="answer4" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                Al desactivar el estado del horario:
                                <ul>
                                    <li>El empleado no aparecerá en los controles de asistencia activos</li>
                                    <li>No se registrarán entradas/salidas para este empleado</li>
                                    <li>El cargo actual mostrará "Sin cargo vigente"</li>
                                    <li>El empleado permanece en el sistema pero sin horario activo</li>
                                </ul>
                                Esto es útil para empleados en vacaciones, licencias o permisos prolongados.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- REFERENCIAS -->
            <section id="referencias" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-book text-primary"></i>
                    Referencias y Recursos Adicionales
                </h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-link"></i> Módulos Relacionados</h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>Control de Asistencia - Registro Diario</li>
                                    <li>Gestión de Horarios</li>
                                    <li>Formato Reportes de Asistencia</li>
                                    <li>Administración de Cargos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-alt"></i> Documentación Técnica</h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>Manual de Usuario - Sistema de Asistencia</li>
                                    <li>Guía de Configuración de Horarios</li>
                                    <li>Procedimientos de Backup y Restauración</li>
                                    <li>Políticas de Seguridad de Datos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <button type="button" class="btn btn-success"
                onclick="window.open('{{ route('helpers.pdf.liberaciones') }}', '_blank')">
                <i class="fas fa-print"></i> Versión Imprimible
            </button>
            
        </div>
    </div>
    
</div>