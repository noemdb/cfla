<div class="container-fluid">
    <div class="row">
        <!-- Contenido Principal -->
        <div class="col-md-12">
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="text-primary">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    Guía - Políticas de Cobranza - Calendario
                </div>
                <p class="lead text-muted">Programación de notificaciones masivas por email para representantes con deudas</p>
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Flujo:</strong> Configurar eventos en el calendario para que el SAEFL envíe notificaciones automáticas en fecha y hora específicas.
                </div>
            </div>

            <!-- Resumen Ejecutivo -->
            <section id="resumen" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-clipboard-list text-primary"></i>
                    Resumen
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>Función del Módulo</h4>
                        <ul>
                            <li><strong>Propósito:</strong> Programar envíos masivos de notificaciones por email</li>
                            <li><strong>Destinatarios:</strong> Representantes con deudas pendientes</li>
                            <li><strong>Automatización:</strong> SAEFL ejecuta envíos automáticamente</li>
                            <li><strong>Flexibilidad:</strong> Múltiples políticas de cobranza configurables</li>
                        </ul>

                        <h5 class="mt-4">Configuración Típica</h5>
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
                                        <td><strong>Política de Cobranza</strong></td>
                                        <td>Tipo de recordatorio o notificación</td>
                                        <td>Recordatorio de pago, Aviso de mora</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha Programada</strong></td>
                                        <td>Cuándo se enviará la notificación</td>
                                        <td>2024-01-15</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Hora Programada</strong></td>
                                        <td>A qué hora se ejecutará el envío</td>
                                        <td>09:00 AM</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>Medio de Envío</strong></td>
                                        <td><strong>Correo Electrónico</strong></td>
                                        <td class="text-primary"><strong>ACTIVADO</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                                <h5>Flujo del Proceso</h5>
                                <span class="badge badge-primary feature-badge">Paso 1: Configurar</span>
                                <span class="badge badge-success feature-badge">Paso 2: Programar</span>
                                <span class="badge badge-info feature-badge">Paso 3: Verificar</span>
                                <span class="badge badge-warning feature-badge">Paso 4: Monitorear</span>

                                <div class="mt-3">
                                    <small class="text-muted">Tiempo estimado: 3-5 minutos por evento</small>
                                    <br>
                                    <small class="text-muted">*Envío automático por SAEFL</small>
                                </div>
                            </div>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Beneficio Clave:</strong>
                            Automatización completa de recordatorios de pago, mejorando la eficiencia en la cobranza.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 1: CREACIÓN DE EVENTO EN CALENDARIO -->
            <section id="paso1" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-plus-circle text-primary"></i>
                    Paso 1: Creación de Nuevo Evento en Calendario
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>1.1 Acceso al Módulo de Calendario</h4>
                        <ol>
                            <li>Navegar a: <code>Configuraciones → P. Cobranzas → Calendario</code></li>
                            <li>Interfaz principal muestra listado de eventos programados</li>
                            <li>Click en botón <span class="badge badge-primary">"Crear"</span></li>
                            <li>Formulario de creación aparece en panel lateral derecho</li>
                        </ol>

                        <h4 class="mt-4">1.2 Configuración de Política de Cobranza</h4>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-cog"></i> Selección de Política:</strong>
                            Elegir la política de cobranza adecuada según el tipo de recordatorio a enviar.
                        </div>

                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-list-alt"></i> Campos del Formulario - Sección Política</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Política de Cobranza:</strong></td>
                                        <td>
                                            <span class="badge badge-info">Selector desplegable</span><br>
                                            <small>Lista todas las políticas activas del sistema</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nombre del Evento:</strong></td>
                                        <td>
                                            <span class="badge badge-warning">Obligatorio</span><br>
                                            <small>Ej: "Recordatorio Pago Enero 2024"</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Descripción:</strong></td>
                                        <td>
                                            <span class="badge badge-secondary">Opcional</span><br>
                                            <small>Detalles adicionales del evento</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h4 class="mt-4">1.3 Programación de Fecha y Hora</h4>
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-clock"></i> Configuración Temporal</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Fecha de Envío:</strong><br>
                                        <span class="badge badge-success">Selector de fecha</span><br>
                                        <small>Fecha exacta cuando se enviará la notificación</small>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Hora de Envío:</strong><br>
                                        <span class="badge badge-success">Selector de hora</span><br>
                                        <small>Hora específica de ejecución (Formato 24h)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-calendar-plus fa-2x text-primary mb-3"></i>
                                <p class="mb-1"><strong>Nuevo Evento</strong></p>
                                <small class="text-muted">Formulario de creación</small>
                                <div class="mt-3">
                                    <span class="badge badge-primary">POLÍTICA</span>
                                    <span class="badge badge-success">FECHA/HORA</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">Ejemplos de Nomenclatura</h5>
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="text-info">Nombres Sugeridos</h6>
                                <ul class="mb-0 small">
                                    <li>"Recordatorio Pago [Mes] [Año]"</li>
                                    <li>"Aviso Mora [Mes] [Año]"</li>
                                    <li>"Notificación Vencimiento [Fecha]"</li>
                                    <li>"Recordatorio Masivo [Descripción]"</li>
                                </ul>
                            </div>
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                            Verificar que la fecha y hora sean futuras para garantizar la ejecución programada.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 2: CONFIGURACIÓN DE MEDIOS DE NOTIFICACIÓN -->
            <section id="paso2" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-envelope text-success"></i>
                    Paso 2: Configuración de Medios de Notificación
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>2.1 Activación de Correo Electrónico</h4>
                        <div class="alert alert-success">
                            <strong><i class="fas fa-envelope-open"></i> Medio Principal:</strong>
                            El correo electrónico es el medio principal para notificaciones masivas a representantes.
                        </div>

                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-toggle-on"></i> Configuración de Medios</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Medio</th>
                                            <th>Estado</th>
                                            <th>Descripción</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i class="fas fa-envelope text-danger"></i>
                                                <strong>Correo Electrónico</strong>
                                            </td>
                                            <td><span class="badge badge-success">ACTIVAR</span></td>
                                            <td>Notificación masiva vía Google Gmail</td>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" checked disabled>
                                                    <label class="custom-control-label"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fab fa-whatsapp text-success"></i>
                                                <strong>WhatsApp Business</strong>
                                            </td>
                                            <td><span class="badge badge-success">ACTIVAR</span></td>
                                            <td>Mensajería WAB (Meta WhatsApp)</td>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" checked disabled>
                                                    <label class="custom-control-label"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <h4 class="mt-4">2.2 Configuración de Estado del Evento</h4>
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-power-off"></i> Estado de Activación</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label><strong>Estado del Evento:</strong></label>
                                    <select class="form-control">
                                        <option value="1">Activo - Programa envío automático</option>
                                        <option value="0">Inactivo - Solo registro, no ejecuta</option>
                                    </select>
                                </div>
                                <div class="alert alert-warning">
                                    <small>
                                        <strong>Nota:</strong> Los eventos inactivos aparecen en el listado pero el SAEFL no los ejecuta.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-4">2.3 Confirmación y Guardado</h4>
                        <ol>
                            <li>Revisar todos los campos completados</li>
                            <li>Verificar que correo electrónico esté activado</li>
                            <li>Confirmar que el estado esté en "Activo"</li>
                            <li>Click en <span class="badge badge-primary">"Guardar"</span></li>
                            <li>Sistema muestra confirmación de creación exitosa</li>
                        </ol>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-tasks fa-2x text-success mb-3"></i>
                                <p class="mb-1"><strong>Configuración Medios</strong></p>
                                <small class="text-muted">Activación de notificaciones</small>
                                <div class="mt-3">
                                    <span class="badge badge-success">EMAIL ACTIVO</span>
                                    <span class="badge badge-success">WAB ACTIVO</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">2.4 Verificación Post-Creación</h5>
                        <div class="success-box">
                            <ul class="mb-0">
                                <li>✅ Evento aparece en listado principal</li>
                                <li>✅ Estado muestra <strong>ACTIVO</strong></li>
                                <li>✅ Icono de email y WAB visible en columna "Medio"</li>
                                <li>✅ Fecha y hora programadas correctas</li>
                                <li>✅ Política de cobranza asignada</li>
                            </ul>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Mejor Práctica:</strong>
                            Programar eventos con al menos 24 horas de anticipación para permitir revisión y ajustes.
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Atención:</strong>
                            El SAEFL ejecuta envíos automáticamente según la programación. Verificar fechas cuidadosamente.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 3: GESTIÓN Y EDICIÓN DE EVENTOS EXISTENTES -->
            <section id="paso3" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-edit text-warning"></i>
                    Paso 3: Gestión y Edición de Eventos Existentes
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>3.1 Acceso a Edición de Eventos</h4>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-sync-alt"></i> Flexibilidad:</strong>
                            Los eventos pueden modificarse siempre que no hayan sido ejecutados por el SAEFL.
                        </div>

                        <ol>
                            <li>En listado principal, ubicar evento a modificar</li>
                            <li>Click en botón <span class="badge badge-warning"><i class="fas fa-edit"></i> Editar</span></li>
                            <li>Formulario de edición carga datos existentes</li>
                            <li>Realizar modificaciones necesarias</li>
                            <li>Click en <span class="badge badge-primary">"Guardar"</span> para actualizar</li>
                        </ol>

                        <h4 class="mt-4">3.2 Casos Comunes de Edición</h4>
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-list"></i> Escenarios de Modificación</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><span class="badge badge-info">Reprogramación</span></td>
                                        <td>Cambiar fecha u hora de envío</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-info">Cambio de Política</span></td>
                                        <td>Modificar tipo de recordatorio</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-info">Desactivación</span></td>
                                        <td>Cancelar envío programado</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-info">Corrección</span></td>
                                        <td>Rectificar información errónea</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h4 class="mt-4">3.3 Eliminación de Eventos</h4>
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-trash-alt"></i> Procedimiento de Eliminación</h6>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Ubicar evento en listado principal</li>
                                    <li>Click en botón <span class="badge badge-danger"><i class="fas fa-trash"></i> Eliminar</span></li>
                                    <li>Confirmar eliminación en modal de confirmación</li>
                                    <li>Sistema elimina registro permanentemente</li>
                                </ol>
                                <div class="alert alert-warning mt-2">
                                    <small>
                                        <strong>Nota:</strong> La eliminación es permanente y no puede deshacerse.
                                        Considerar desactivar en lugar de eliminar.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-sliders-h fa-2x text-warning mb-3"></i>
                                <p class="mb-1"><strong>Gestión de Eventos</strong></p>
                                <small class="text-muted">Edición y eliminación</small>
                                <div class="mt-3">
                                    <span class="badge badge-warning">EDITAR</span>
                                    <span class="badge badge-danger">ELIMINAR</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">3.4 Estados de Evento</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Estado</th>
                                        <th>Color</th>
                                        <th>Significado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Activo</strong></td>
                                        <td><span class="badge badge-success">Verde</span></td>
                                        <td>Programado para ejecución</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Inactivo</strong></td>
                                        <td><span class="badge badge-secondary">Gris</span></td>
                                        <td>Registrado pero no se ejecutará</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ejecutado</strong></td>
                                        <td><span class="badge badge-info">Azul</span></td>
                                        <td>SAEFL ya procesó el envío</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Estrategia Recomendada:</strong>
                            Para eventos recurrentes, crear plantillas de nombres y configuraciones para agilizar la creación.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 4: MONITOREO Y VERIFICACIÓN DE ENVÍOS -->
            <section id="paso4" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-chart-line text-info"></i>
                    Paso 4: Monitoreo y Verificación
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>4.1 Listado Principal de Eventos</h4>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-columns"></i> Vista Resumen:</strong>
                            El listado principal muestra todos los eventos con información clave para monitoreo.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Columna</th>
                                        <th>Información</th>
                                        <th>Importancia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Nombre</strong></td>
                                        <td>Identificación del evento</td>
                                        <td>Búsqueda y organización</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Política</strong></td>
                                        <td>Tipo de cobranza asignada</td>
                                        <td>Contexto del recordatorio</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha/Hora</strong></td>
                                        <td>Programación de ejecución</td>
                                        <td>Planificación temporal</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Medio</strong></td>
                                        <td>Iconos de email/whatsapp</td>
                                        <td>Canales activados</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estado</strong></td>
                                        <td>Activo/Inactivo</td>
                                        <td>Control de ejecución</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h4 class="mt-4">4.2 Funcionalidades de Búsqueda y Filtrado</h4>
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-search"></i> Herramientas de Navegación</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Búsqueda por Texto:</strong></td>
                                        <td>Filtra por nombre o descripción</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ordenamiento:</strong></td>
                                        <td>Click en encabezados para ordenar</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paginación:</strong></td>
                                        <td>Navegación entre páginas de resultados</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h4 class="mt-4">4.3 Verificación de Ejecución por SAEFL</h4>
                        <div class="alert alert-success">
                            <strong><i class="fas fa-robot"></i> Proceso Automático:</strong>
                            El SAEFL ejecuta los envíos automáticamente según la programación. Monitorear logs del sistema para verificación.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-binoculars fa-2x text-info mb-3"></i>
                                <p class="mb-1"><strong>Monitoreo</strong></p>
                                <small class="text-muted">Seguimiento de eventos</small>
                                <div class="mt-3">
                                    <span class="badge badge-info">VISIÓN GENERAL</span>
                                    <span class="badge badge-success">FILTRADO</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">4.4 Indicadores Clave</h5>
                        <div class="success-box">
                            <ul class="mb-0">
                                <li>✅ Eventos programados correctamente</li>
                                <li>✅ Medios de envío configurados</li>
                                <li>✅ Estados activos/inactivos según necesidad</li>
                                <li>✅ Fechas y horas futuras válidas</li>
                                <li>✅ Políticas de cobranza asignadas</li>
                            </ul>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Reportes:</strong>
                            Utilizar el módulo de reportes del SAEFL para ver métricas de efectividad de los envíos.
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Validación:</strong>
                            Verificar periodicamente que los eventos programados se ejecuten según lo esperado.
                        </div>
                    </div>
                </div>
            </section>

            <!-- RESUMEN FINAL -->
            <section id="resumen-final" class="guide-section">
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
                                            <td><strong>Evento Calendario</strong></td>
                                            <td><span class="badge badge-success">PROGRAMADO</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Notificación Email</strong></td>
                                            <td><span class="badge badge-info">CONFIGURADO</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Política Cobranza</strong></td>
                                            <td><span class="badge badge-primary">ASIGNADA</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ejecución SAEFL</strong></td>
                                            <td><span class="badge badge-warning">PENDIENTE</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Elementos Configurados</h5>
                                <ul>
                                    <li>Política de cobranza específica</li>
                                    <li>Fecha y hora de ejecución</li>
                                    <li>Medio de notificación (email)</li>
                                    <li>Nombre y descripción del evento</li>
                                    <li>Estado de activación</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr>

            <button type="button" class="btn btn-success"
                onclick="window.open('{{ route('helpers.pdf.send.notifications.collection') }}', '_blank')">
                <i class="fas fa-print"></i> Versión Imprimible
            </button>

            <hr>

            <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
                <tr>
                    <td align="center">
                        <font size="2" color="#ffffff" face="Arial">
                            Guía de Políticas de Cobranza - Calendario - Versión 1.0
                        </font>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>