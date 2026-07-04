<div class="row">
    <!-- Contenido Principal -->
    <div class="col-md-12">
        <!-- Introducción -->
        <section id="introduccion" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Gestión de Lista representantes con restricciones administrativas
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <p class="lead">
                        La <strong>Gestión de Lista de representantes con restricciones administrativas</strong> es una herramienta especializada diseñada para administrar representantes con morosidad y restricciones administrativas de manera eficiente y controlada.
                    </p>

                    <div class="tip-box">
                        <strong><i class="fas fa-lightbulb"></i> Propósito:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Identificación centralizada de representantes problemáticos</li>
                            <li>Gestión controlada de inclusión y retiro de lista de representantes con restricciones administrativas</li>
                            <li>Filtrado avanzado por estado y situación financiera</li>
                            <li>Alertas automáticas sobre verificación de deudas</li>
                            <li>Seguimiento completo de cambios realizados</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-user-slash fa-3x text-danger mb-3"></i>
                            <h5>Características Principales</h5>
                            <span class="badge badge-warning feature-badge">Filtros Avanzados</span>
                            <span class="badge badge-danger feature-badge">Gestión Controlada</span>
                            <span class="badge badge-info feature-badge">Estadísticas en Tiempo Real</span>
                            <span class="badge badge-success feature-badge">Confirmaciones Seguras</span>
                            <span class="badge badge-primary feature-badge">Alertas de Verificación</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Requisitos del Sistema -->
        <section id="requisitos" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-clipboard-check text-warning"></i>
                Requisitos
            </h2>

            <div class="currency-box">
                <strong><i class="fas fa-shield-alt"></i> Permisos Requeridos:</strong>
                <strong>Debe tener permisos de administración para gestionar la lista negra.</strong> Esta funcionalidad está restringida a usuarios autorizados.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Configuraciones Requeridas</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Permisos de administración</strong> activos</li>
                                <li>Representantes registrados</li>
                                <li>Estudiantes asociados a representantes</li>
                                <li>Sistema de autenticación activo</li>
                                <li>Conexión a base de datos estable</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-cogs"></i> Elementos</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Componente Livewire activo</li>
                                <li>SweetAlert2 para confirmaciones</li>
                                <li>Bootstrap 4.3 para interfaz</li>
                                <li>Paginación automática</li>
                                <li>Búsqueda en tiempo real</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Panel de Control Principal -->
        <section id="panel-control" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-tachometer-alt text-primary"></i>
                Panel de Control Principal
            </h2>

            <h4>Estructura de la Interfaz</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="25%">Sección</th>
                                    <th width="45%">Descripción</th>
                                    <th width="30%">Funcionalidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Filtros de Búsqueda</strong></td>
                                    <td>Panel superior con controles de filtrado</td>
                                    <td>Búsqueda por texto, estado y lista negra</td>
                                </tr>
                                <tr>
                                    <td><strong>Estadísticas Rápidas</strong></td>
                                    <td>Tarjetas con conteos en tiempo real</td>
                                    <td>Total, activos, lista negra, mostrando</td>
                                </tr>
                                <tr>
                                    <td><strong>Tabla de Representantes</strong></td>
                                    <td>Lista principal con información detallada</td>
                                    <td>Datos, estados, acciones disponibles</td>
                                </tr>
                                <tr>
                                    <td><strong>Controles de Paginación</strong></td>
                                    <td>Navegación entre páginas de resultados</td>
                                    <td>Selección de registros por página</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-magic"></i> Características Inteligentes:</strong>
                        <ul class="mb-0">
                            <li><strong>Debounce:</strong> Búsqueda optimizada (300ms)</li>
                            <li><strong>Reset automático:</strong> Página se reinicia al filtrar</li>
                            <li><strong>Estados visuales:</strong> Colores por situación</li>
                            <li><strong>Responsive:</strong> Adaptable a dispositivos</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Tarjetas de Estadísticas</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center text-white bg-primary">
                        <div class="card-body py-2">
                            <h6 class="mb-0">Total</h6>
                            <small>Representantes</small>
                            <h4 class="mb-0 mt-1">150</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center text-white bg-success">
                        <div class="card-body py-2">
                            <h6 class="mb-0">Activos</h6>
                            <small>En sistema</small>
                            <h4 class="mb-0 mt-1">135</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center text-white bg-danger">
                        <div class="card-body py-2">
                            <h6 class="mb-0">Lista Negra</h6>
                            <small>Actual</small>
                            <h4 class="mb-0 mt-1">8</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center text-white bg-warning">
                        <div class="card-body py-2">
                            <h6 class="mb-0">Mostrando</h6>
                            <small>Resultados</small>
                            <h4 class="mb-0 mt-1">10</h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filtros Avanzados -->
        <section id="filtros" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-filter text-primary"></i>
                Filtros de Búsqueda Avanzados
            </h2>

            <h4>Tipos de Filtros Disponibles</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-search"></i> Búsqueda por Texto</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Función:</strong> Buscar por cédula o nombre</p>
                            <ul class="small">
                                <li>Campo: Input de texto</li>
                                <li>Búsqueda: Cédula o nombre completo</li>
                                <li>Tiempo: Debounce de 300ms</li>
                                <li>Coincidencia: Parcial (LIKE %texto%)</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-user-check"></i> Filtro por Estado</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Función:</strong> Filtrar por estado activo/inactivo</p>
                            <ul class="small">
                                <li>Opciones: Todos, Activo, Inactivo</li>
                                <li>Campo: Select dropdown</li>
                                <li>Valores: "true", "false", ""</li>
                                <li>Actualización: En tiempo real</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-ban"></i> Filtro Lista Negra</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Función:</strong> Filtrar por situación en lista negra</p>
                            <ul class="small">
                                <li>Opciones: Todos, En Lista, Fuera</li>
                                <li>Campo: Select dropdown</li>
                                <li>Valores: "true", "false", ""</li>
                                <li>Visual: Badges coloridos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Resumen de Filtros Activos</h4>
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Indicador Visual:</strong>
                Cuando hay filtros aplicados, aparece un panel resumen con badges que muestran:
                <ul class="mb-0 mt-2">
                    <li><span class="badge badge-primary">Búsqueda: "texto"</span> - Término buscado</li>
                    <li><span class="badge badge-info">Estado: Activo</span> - Filtro de estado</li>
                    <li><span class="badge badge-danger">Lista Negra: Sí</span> - Filtro lista negra</li>
                </ul>
            </div>

            <div class="tip-box">
                <strong><i class="fas fa-sync-alt"></i> Botón Limpiar Filtros:</strong>
                <ul class="mb-0">
                    <li>Ubicación: Lado derecho del panel de filtros</li>
                    <li>Función: Restablece todos los filtros a valores por defecto</li>
                    <li>Acción: Limpia búsqueda, estados y reinicia paginación</li>
                    <li>Icono: <i class="fas fa-redo"></i> Limpiar</li>
                </ul>
            </div>
        </section>

        <!-- Gestión de Lista Negra -->
        <section id="gestion" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-cogs text-primary"></i>
                Gestión de Lista Negra
            </h2>

            <h4>Proceso de Inclusión en Lista Negra</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="20%">Paso</th>
                                    <th width="40%">Acción</th>
                                    <th width="40%">Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>1. Identificación</strong></td>
                                    <td>Localizar representante en la tabla</td>
                                    <td>Fila resaltada según estado actual</td>
                                </tr>
                                <tr>
                                    <td><strong>2. Acción</strong></td>
                                    <td>Click en <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Agregar</span></td>
                                    <td>Se activa proceso de confirmación</td>
                                </tr>
                                <tr>
                                    <td><strong>3. Confirmación</strong></td>
                                    <td>SweetAlert de advertencia aparece</td>
                                    <td>Opción de confirmar o cancelar</td>
                                </tr>
                                <tr>
                                    <td><strong>4. Ejecución</strong></td>
                                    <td>Confirmar la acción</td>
                                    <td>Estado cambia a "En Lista Negra"</td>
                                </tr>
                                <tr>
                                    <td><strong>5. Notificación</strong></td>
                                    <td>Mensaje de éxito con alertas</td>
                                    <td>Recordatorio de verificación de deudas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="warning-box">
                        <strong><i class="fas fa-exclamation-triangle"></i> Alerta de Verificación:</strong>
                        Al agregar a lista de representantes con restricciones administrativas, el listado muestra un mensaje recordando verificar:
                        <ul class="mb-0 mt-2">
                            <li>Plan de pago del representante</li>
                            <li>Conceptos de cobro pendientes</li>
                            <li>Cuentas de cobro vencidas</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h4>Proceso de Retiro de Lista Negra</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="20%">Paso</th>
                                    <th width="40%">Acción</th>
                                    <th width="40%">Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>1. Identificación</strong></td>
                                    <td>Localizar representante en lista negra (fila roja)</td>
                                    <td>Fila destacada en color rojo</td>
                                </tr>
                                <tr>
                                    <td><strong>2. Acción</strong></td>
                                    <td>Click en <span class="badge badge-success"><i class="fas fa-check-circle"></i> Retirar</span></td>
                                    <td>Se activa proceso de confirmación</td>
                                </tr>
                                <tr>
                                    <td><strong>3. Confirmación</strong></td>
                                    <td>SweetAlert de confirmación aparece</td>
                                    <td>Opción de confirmar o cancelar</td>
                                </tr>
                                <tr>
                                    <td><strong>4. Ejecución</strong></td>
                                    <td>Confirmar la acción</td>
                                    <td>Estado cambia a "Normal"</td>
                                </tr>
                                <tr>
                                    <td><strong>5. Notificación</strong></td>
                                    <td>Mensaje de éxito con recomendaciones</td>
                                    <td>Sugerencias de verificación posterior</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="success-box">
                        <strong><i class="fas fa-check-circle"></i> Recomendación Post-Retiro:</strong>
                        Al retirar de lista negra, se sugiere verificar:
                        <ul class="mb-0 mt-2">
                            <li>Situación financiera actual</li>
                            <li>Estado de pagos recientes</li>
                            <li>Restricciones administrativas</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h4>Indicadores Visuales</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-ban"></i> En Lista Negra</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Fila:</strong> Fondo rojo claro (table-danger)</li>
                                <li><strong>Badge:</strong> Rojo con icono de advertencia</li>
                                <li><strong>Texto:</strong> "En Lista Negra"</li>
                                <li><strong>Botón:</strong> Verde "Retirar"</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check"></i> Estado Normal</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Fila:</strong> Sin color especial</li>
                                <li><strong>Badge:</strong> Gris con icono de check</li>
                                <li><strong>Texto:</strong> "Normal"</li>
                                <li><strong>Botón:</strong> Amarillo "Agregar"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tabla de Representantes -->
        <section id="tabla" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-table text-primary"></i>
                Tabla de Representantes
            </h2>

            <h4>Estructura de Columnas</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th width="10%">Columna</th>
                            <th width="25%">Descripción</th>
                            <th width="25%">Contenido</th>
                            <th width="20%">Formato</th>
                            <th width="20%">Ejemplo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>N</strong></td>
                            <td>Número de orden</td>
                            <td>Posición en la página actual</td>
                            <td>Numérico centrado</td>
                            <td class="text-center">1</td>
                        </tr>
                        <tr>
                            <td><strong>Cédula</strong></td>
                            <td>Identificación del representante</td>
                            <td>Cédula de identidad</td>
                            <td>Texto en negrita</td>
                            <td><strong>12345678</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre</strong></td>
                            <td>Nombre completo del representante</td>
                            <td>Nombre + email (si existe)</td>
                            <td>Texto + email pequeño</td>
                            <td>Juan Pérez<br><small>juan@email.com</small></td>
                        </tr>
                        <tr>
                            <td><strong>Estudiantes</strong></td>
                            <td>Cantidad de estudiantes asociados</td>
                            <td>Número con badge</td>
                            <td>Badge azul centrado</td>
                            <td class="text-center"><span class="badge badge-info">2</span></td>
                        </tr>
                        <tr>
                            <td><strong>Estado</strong></td>
                            <td>Estado activo/inactivo</td>
                            <td>Badge colorido</td>
                            <td>Verde/rojo centrado</td>
                            <td class="text-center"><span class="badge badge-success">Activo</span></td>
                        </tr>
                        <tr>
                            <td><strong>Lista Negra</strong></td>
                            <td>Estado en lista negra</td>
                            <td>Badge colorido con icono</td>
                            <td>Rojo/gris centrado</td>
                            <td class="text-center"><span class="badge badge-danger">En Lista Negra</span></td>
                        </tr>
                        <tr>
                            <td><strong>Acciones</strong></td>
                            <td>Operaciones disponibles</td>
                            <td>Botones de gestión</td>
                            <td>Grupo de botones</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-warning btn-sm"><i class="fas fa-exclamation-triangle"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h4>Estados de Fila</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body p-2">
                            <table class="table table-sm mb-0">
                                <tr class="table-danger">
                                    <td><strong>Lista Negra</strong></td>
                                    <td>Fondo rojo claro</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer small">
                            Representante en lista negra
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body p-2">
                            <table class="table table-sm mb-0">
                                <tr class="table-warning">
                                    <td><strong>Inactivo</strong></td>
                                    <td>Fondo amarillo claro</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer small">
                            Representante inactivo
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body p-2">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td><strong>Normal</strong></td>
                                    <td>Sin color especial</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer small">
                            Representante normal
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Confirmaciones y Seguridad -->
        <section id="confirmaciones" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-shield-alt text-primary"></i>
                Confirmaciones y Seguridad
            </h2>

            <h4>Confirmación</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-plus-circle"></i> Confirmación de Agregado</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Características:</strong></p>
                            <ul>
                                <li>Icono: ⚠️ (advertencia)</li>
                                <li>Color: Naranja (#d33)</li>
                                <li>Texto: "¿Agregar a lista negra?"</li>
                                <li>Botón: "Sí, agregar"</li>
                                <li>Focus: En botón cancelar</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Confirmación de Retiro</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Características:</strong></p>
                            <ul>
                                <li>Icono: ❓ (pregunta)</li>
                                <li>Color: Verde (#28a745)</li>
                                <li>Texto: "¿Retirar de lista negra?"</li>
                                <li>Botón: "Sí, retirar"</li>
                                <li>Focus: En botón cancelar</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Mensajes de Resultado</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <strong><i class="fas fa-exclamation-triangle"></i> Mensaje de Agregado Exitoso:</strong>
                        <div class="mt-2">
                            <strong>¡Representante agregado a lista negra!</strong><br>
                            Incluye recordatorio de verificación de deudas individuales.
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <strong><i class="fas fa-check-circle"></i> Mensaje de Retiro Exitoso:</strong>
                        <div class="mt-2">
                            <strong>¡Representante retirado de lista negra!</strong><br>
                            Incluye recomendaciones de verificación posterior.
                        </div>
                    </div>
                </div>
            </div>

            <div class="danger-box">
                <strong><i class="fas fa-user-secret"></i> Auditoría:</strong>
                Todas las acciones de gestión de lista negra quedan registradas automáticamente:
                <ul class="mb-0 mt-2">
                    <li><strong>Usuario:</strong> Se registra el ID del usuario que realizó la acción</li>
                    <li><strong>Fecha:</strong> Timestamp automático</li>
                    <li><strong>Acción:</strong> Tipo de operación (agregar/retirar)</li>
                    <li><strong>Representante:</strong> ID del representante afectado</li>
                </ul>
            </div>
        </section>

        <!-- Preguntas Frecuentes -->
        <section id="faq" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-question-circle text-primary"></i>
                Preguntas Frecuentes (FAQ)
            </h2>

            <div class="accordion" id="faqAccordion">
                <!-- Pregunta 1 -->
                <div class="card">
                    <div class="card-header" id="faq1">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#answer1">
                                <i class="fas fa-question"></i> ¿Qué sucede si intento agregar a lista negra un representante que ya está en ella?
                            </button>
                        </h5>
                    </div>
                    <div id="answer1" class="collapse show" data-parent="#faqAccordion">
                        <div class="card-body">
                            Se <strong>detecta automáticamente</strong> la situación y muestra un mensaje informativo: "El representante ya se encuentra en la lista negra". No se genera duplicación ni error.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="card">
                    <div class="card-header" id="faq2">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer2">
                                <i class="fas fa-question"></i> ¿Puedo filtrar solo los representantes que están en lista negra?
                            </button>
                        </h5>
                    </div>
                    <div id="answer2" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>Sí</strong>, use el filtro "Lista Negra" y seleccione la opción "En Lista Negra". Se mostrará exclusivamente los representantes que cumplen con este criterio, actualizando las estadísticas en tiempo real.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="card">
                    <div class="card-header" id="faq3">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer3">
                                <i class="fas fa-question"></i> ¿Qué significa el mensaje de verificación de deudas después de agregar a lista negra?
                            </button>
                        </h5>
                    </div>
                    <div id="answer3" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Es un <strong>recordatorio de mejores prácticas</strong>. Después de incluir un representante en lista negra, se recomienda verificar:
                            <ul>
                                <li>Plan de pago actual del representante</li>
                                <li>Conceptos de cobro pendientes</li>
                                <li>Cuentas de cobro vencidas o por vencer</li>
                            </ul>
                            Esto asegura que la decisión esté respaldada por información financiera actualizada.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="card">
                    <div class="card-header" id="faq4">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer4">
                                <i class="fas fa-question"></i> ¿Cómo sé qué usuario realizó cambios en la lista negra?
                            </button>
                        </h5>
                    </div>
                    <div id="answer4" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Se <strong>registra automáticamente</strong> el ID del usuario en cada operación. Esta información queda almacenada en el campo <code>user_id</code> de la tabla de representantes y puede ser consultada a través de reportes administrativos o auditoría.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="card">
                    <div class="card-header" id="faq5">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer5">
                                <i class="fas fa-question"></i> ¿Qué pasa si hay un error durante el proceso de confirmación?
                            </button>
                        </h5>
                    </div>
                    <div id="answer5" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Se implementan <strong>mecanismos de seguridad</strong>:
                            <ul>
                                <li>Transacciones de base de datos con rollback automático</li>
                                <li>Validación de permisos en cada paso</li>
                                <li>Manejo de excepciones con mensajes claros</li>
                                <li>Logging detallado de errores para diagnóstico</li>
                            </ul>
                            En caso de error, se mostrará un mensaje específico y la operación no se completará.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mejores Prácticas -->
        <section id="mejores-practicas" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-star text-warning"></i>
                Mejores Prácticas
            </h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-thumbs-up"></i> Prácticas Recomendadas</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Verificar deudas</strong> antes de agregar a lista negra</li>
                                <li><strong>Utilizar filtros</strong> para encontrar representantes específicos</li>
                                <strong>Revisar estadísticas</strong> para tener contexto general</li>
                                <li><strong>Confirmar cuidadosamente</strong> cada acción</li>
                                <li><strong>Documentar razones</strong> fuera del sistema</li>
                                <li><strong>Revisar periódicamente</strong> la lista negra actual</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Prácticas a Evitar</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>No agregar</strong> sin verificar situación financiera</li>
                                <li><strong>Evitar acciones</strong> sin confirmación adecuada</li>
                                <li><strong>No ignorar</strong> los mensajes de verificación</li>
                                <li><strong>Evitar uso</strong> sin permisos adecuados</li>
                                <li><strong>No realizar cambios</strong> sin documentación</li>
                                <li><strong>Evitar retiros</strong> sin verificación posterior</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="success-box mt-4