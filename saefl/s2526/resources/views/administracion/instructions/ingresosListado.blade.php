<div class="row">
    <!-- Contenido Principal -->
    <div class="col-md-12">
        <!-- Introducción -->
        <section id="introduccion" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-list-alt text-primary"></i>
                Listado de Ingresos Registrados
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <p class="lead">
                        El <strong>Listado de Ingresos Registrados</strong> es una herramienta para consultar, filtrar y administrar todos los ingresos reportados en la institución con capacidades de búsqueda avanzada y gestión edición/actualizacón completa.
                    </p>

                    <div class="tip-box">
                        <strong><i class="fas fa-lightbulb"></i> Propósito del Sistema:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Consulta centralizada de todos los ingresos</li>
                            <li>Filtrado avanzado por múltiples criterios</li>
                            <li>Gestión completa edición/actualización de registros</li>
                            <li>Reportes financieros en tiempo real</li>
                            <li>Exportación de datos para análisis</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-database fa-3x text-success mb-3"></i>
                            <h5>Características Principales</h5>
                            <span class="badge badge-primary feature-badge">Búsqueda Avanzada</span>
                            <span class="badge badge-success feature-badge">Edición/Actualización</span>
                            <span class="badge badge-info feature-badge">Totales Automáticos</span>
                            <span class="badge badge-warning feature-badge">Exportación PDF</span>
                            <span class="badge badge-danger feature-badge">Auditoría Completa</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Estructura de Datos -->
        <section id="estructura" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-sitemap text-warning"></i>
                Estructura de Datos
            </h2>

            <div class="currency-box">
                <strong><i class="fas fa-project-diagram"></i> Relaciones Principales:</strong>
                Cada ingreso está relacionado con múltiples entidades del sistema para garantizar integridad referencial.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-link"></i> Relaciones del Modelo Ingreso</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>belongsTo Estudiante</strong> - Estudiante asociado</li>
                                <li><strong>belongsTo Representante</strong> - Persona que paga</li>
                                <li><strong>belongsTo Banco</strong> - Entidad bancaria</li>
                                <li><strong>belongsTo MetodoPago</strong> - Forma de pago</li>
                                <li><strong>belongsTo ExchangeRate</strong> - Tasa de cambio</li>
                                <li><strong>belongsTo User</strong> - Usuario que registra</li>
                                <li><strong>SoftDeletes</strong> - Eliminación lógica</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-table"></i> Campos Principales</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>number_i_pay</strong> - Referencia única</li>
                                <li><strong>date_payment</strong> - Fecha de pago</li>
                                <li><strong>date_transaction</strong> - Fecha en banco</li>
                                <li><strong>ingreso_ammount</strong> - Monto principal</li>
                                <li><strong>exchange_ammount</strong> - Monto cambiario</li>
                                <li><strong>status_late_payment</strong> - Estado extemporáneo</li>
                                <li><strong>ingreso_observations</strong> - Observaciones</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filtrado Avanzado -->
        <section id="filtrado" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-filter text-primary"></i>
                Filtrado
            </h2>

            <h4>Criterios de Búsqueda Disponibles</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="25%">Filtro</th>
                                    <th width="45%">Descripción</th>
                                    <th width="15%">Tipo</th>
                                    <th width="15%">Ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Fechas de Pago</strong></td>
                                    <td>Rango entre fecha inicial y final</td>
                                    <td><span class="badge badge-info">Fecha</span></td>
                                    <td>01/10/2024 - 31/10/2024</td>
                                </tr>
                                <tr>
                                    <td><strong>Banco Receptor</strong></td>
                                    <td>Entidad bancaria específica</td>
                                    <td><span class="badge badge-primary">Select</span></td>
                                    <td>Banco de Venezuela</td>
                                </tr>
                                <tr>
                                    <td><strong>Identificación</strong></td>
                                    <td>Cédula estudiante o representante</td>
                                    <td><span class="badge badge-warning">Texto</span></td>
                                    <td>12345678</td>
                                </tr>
                                <tr>
                                    <td><strong>Referencia</strong></td>
                                    <td>Número de transacción bancaria</td>
                                    <td><span class="badge badge-warning">Texto</span></td>
                                    <td>TRF-123456789</td>
                                </tr>
                                <tr>
                                    <td><strong>Extemporáneos</strong></td>
                                    <td>Pagos fuera de fecha límite</td>
                                    <td><span class="badge badge-success">Switch</span></td>
                                    <td>Activado/Desactivado</td>
                                </tr>
                                <tr>
                                    <td><strong>Banco Público</strong></td>
                                    <td>Filtrar solo bancos públicos</td>
                                    <td><span class="badge badge-success">Switch</span></td>
                                    <td>Activado/Desactivado</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-search"></i> Tips de Búsqueda:</strong>
                        <ul class="mb-0">
                            <li>Use % para búsquedas parciales</li>
                            <li>Fechas vacías = sin filtro temporal</li>
                            <li>Múltiples filtros se combinan con AND</li>
                            <li>Los switches son excluyentes</li>
                        </ul>
                    </div>

                    <div class="warning-box mt-3">
                        <strong><i class="fas fa-database"></i> Performance:</strong>
                        Las búsquedas con muchos filtros pueden tomar más tiempo. Use solo los necesarios.
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Ejemplos de Uso de Filtros</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-calendar"></i> Búsqueda por Período</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Objetivo:</strong> Ver todos los ingresos de octubre 2024</p>
                            <ul class="small">
                                <li><strong>Fecha Inicial:</strong> 2024-10-01</li>
                                <li><strong>Fecha Final:</strong> 2024-10-31</li>
                                <li><strong>Resultado:</strong> Ingresos del mes completo</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Búsqueda por Persona</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Objetivo:</strong> Encontrar pagos de un representante</p>
                            <ul class="small">
                                <li><strong>Identificación:</strong> V-12345678</li>
                                <li><strong>Resultado:</strong> Todos los pagos de esa cédula</li>
                                <li><strong>Nota:</strong> Busca en estudiante y representante</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Estructura de la Tabla -->
        <section id="tabla" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-table text-primary"></i>
                Estructura de la Tabla Principal
            </h2>

            <h4>Columnas</h4>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Columna</th>
                            <th>Descripción</th>
                            <th>Visible en</th>
                            <th>Contenido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>N</strong></td>
                            <td>Número de orden</td>
                            <td><span class="badge badge-secondary">Todos</span></td>
                            <td>Iteración del loop</td>
                        </tr>
                        <tr>
                            <td><strong>ID</strong></td>
                            <td>Identificador único</td>
                            <td><span class="badge badge-secondary">Todos</span></td>
                            <td>ID del registro</td>
                        </tr>
                        <tr>
                            <td><strong>Representante</strong></td>
                            <td>Persona que realizó el pago</td>
                            <td><span class="badge badge-success">Siempre</span></td>
                            <td>Nombre + Cédula</td>
                        </tr>
                        <tr>
                            <td><strong>F. Pago</strong></td>
                            <td>Fecha de realización del pago</td>
                            <td><span class="badge badge-info">Tablet+</span></td>
                            <td>dd-mm-aaaa</td>
                        </tr>
                        <tr>
                            <td><strong>F. Banco</strong></td>
                            <td>Fecha de transacción bancaria</td>
                            <td><span class="badge badge-info">Tablet+</span></td>
                            <td>dd-mm-aaaa</td>
                        </tr>
                        <tr>
                            <td><strong>Banco</strong></td>
                            <td>Entidad receptora</td>
                            <td><span class="badge badge-info">Tablet+</span></td>
                            <td>Nombre del banco</td>
                        </tr>
                        <tr>
                            <td><strong>Referencia</strong></td>
                            <td>Número de transacción</td>
                            <td><span class="badge badge-warning">Desktop+</span></td>
                            <td>Número único</td>
                        </tr>
                        <tr>
                            <td><strong>Monto (Bs)</strong></td>
                            <td>Monto en moneda local</td>
                            <td><span class="badge badge-warning">Desktop+</span></td>
                            <td>Formato decimal</td>
                        </tr>
                        <tr>
                            <td><strong>M. Cambiario ($)</strong></td>
                            <td>Monto en dólares</td>
                            <td><span class="badge badge-warning">Desktop+</span></td>
                            <td>Formato decimal</td>
                        </tr>
                        <tr>
                            <td><strong>Destino</strong></td>
                            <td>Concepto del pago</td>
                            <td><span class="badge badge-warning">Desktop+</span></td>
                            <td>Descripción</td>
                        </tr>
                        <tr>
                            <td><strong>F. Registro</strong></td>
                            <td>Fecha de creación</td>
                            <td><span class="badge badge-info">Tablet+</span></td>
                            <td>dd-mm-aaaa</td>
                        </tr>
                        <tr>
                            <td><strong>Usuario</strong></td>
                            <td>Registrador</td>
                            <td><span class="badge badge-info">Tablet+</span></td>
                            <td>Username</td>
                        </tr>
                        <tr>
                            <td><strong>Acción</strong></td>
                            <td>Operaciones</td>
                            <td><span class="badge badge-success">Siempre</span></td>
                            <td>Botones Editar/Eliminar</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h4 class="mt-4">Indicadores Visuales</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-trash"></i> Registros Eliminados</h6>
                        </div>
                        <div class="card-body">
                            <p>Los ingresos eliminados lógicamente se muestran con:</p>
                            <ul>
                                <li><strong>Fondo rojo</strong> en toda la fila</li>
                                <li><strong>Texto [BORRADO]</strong> en la referencia</li>
                                <li><strong>Botones deshabilitados</strong> para edición</li>
                                <li><strong>Solo visible</strong> para administradores</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-exchange-alt"></i> Tasa de Cambio</h6>
                        </div>
                        <div class="card-body">
                            <p>Indicadores de conversión monetaria:</p>
                            <ul>
                                <li><strong>Texto en negrita</strong> cuando hay tasa</li>
                                <li><strong>Color azul</strong> para montos convertidos</li>
                                <li><strong>Tooltip</strong> con detalles de la tasa</li>
                                <li><strong>Texto normal</strong> sin tasa disponible</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gestión CRUD -->
        <section id="crud" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-edit text-primary"></i>
                Operaciones Disponibles
            </h2>

            <h4>Edición de Ingresos</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="25%">Campo Editable</th>
                                    <th width="35%">Descripción</th>
                                    <th width="20%">Validación</th>
                                    <th width="20%">Ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Método de Pago</strong></td>
                                    <td>Forma de pago utilizada</td>
                                    <td><span class="badge badge-danger">Requerido</span></td>
                                    <td>Transferencia, Efectivo</td>
                                </tr>
                                <tr>
                                    <td><strong>Banco</strong></td>
                                    <td>Entidad bancaria</td>
                                    <td><span class="badge badge-danger">Requerido</span></td>
                                    <td>Banco de Venezuela</td>
                                </tr>
                                <tr>
                                    <td><strong>Referencia</strong></td>
                                    <td>Número de transacción</td>
                                    <td><span class="badge badge-danger">Requerido</span></td>
                                    <td>TRF-123456789</td>
                                </tr>
                                <tr>
                                    <td><strong>Monto del Pago</strong></td>
                                    <td>Monto principal</td>
                                    <td><span class="badge badge-danger">Requerido</span></td>
                                    <td>120.00</td>
                                </tr>
                                <tr>
                                    <td><strong>Monto Extemporáneo</strong></td>
                                    <td>Monto cambiario adicional</td>
                                    <td><span class="badge badge-warning">Opcional</span></td>
                                    <td>5.00</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Pago</strong></td>
                                    <td>Fecha del pago</td>
                                    <td><span class="badge badge-danger">Requerido</span></td>
                                    <td>15/10/2024</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha en Banco</strong></td>
                                    <td>Fecha bancaria</td>
                                    <td><span class="badge badge-danger">Requerido</span></td>
                                    <td>15/10/2024</td>
                                </tr>
                                <tr>
                                    <td><strong>Observaciones</strong></td>
                                    <td>Notas adicionales</td>
                                    <td><span class="badge badge-warning">Opcional</span></td>
                                    <td>Pago parcial</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-calculator"></i> Cálculo Automático:</strong>
                        <ul class="mb-0">
                            <li>Al guardar, busca tasa de cambio de la fecha</li>
                            <li>Recalcula monto cambiario automáticamente</li>
                            <li>Si no hay tasa, campos quedan nulos</li>
                            <li>Actualiza todos los relacionados</li>
                        </ul>
                    </div>

                    <div class="warning-box mt-3">
                        <strong><i class="fas fa-exclamation-triangle"></i> Auditoría:</strong>
                        La edición mantiene el usuario original pero actualiza timestamps.
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Eliminación Lógica</h4>
            <div class="danger-box">
                <strong><i class="fas fa-trash-alt"></i> Proceso de Eliminación:</strong>
                La eliminación es <strong>LÓGICA (Soft Delete)</strong> - Los registros se marcan como eliminados pero permanecen en la base de datos para auditoría.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-code"></i> Acciones al Eliminar</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Marca deleted_at</strong> con timestamp</li>
                                <li><strong>Modifica number_i_pay</strong> añadiendo "[BORRADO]"</li>
                                <li><strong>Mantiene relaciones</strong> intactas</li>
                                <li><strong>Permite recuperación</strong> vía administración</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-eye-slash"></i> Comportamiento Visual</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Fila en rojo</strong> en listados</li>
                                <li><strong>Excluido</strong> de búsquedas normales</li>
                                <li><strong>Visible solo</strong> para administradores</li>
                                <li><strong>Botón editar</strong> deshabilitado</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Reportes y Exportación -->
        <section id="reportes" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-chart-bar text-primary"></i>
                Reportes y Exportación de Datos
            </h2>

            <h4>Totales Automáticos</h4>
            <div class="alert alert-success">
                <strong><i class="fas fa-calculator"></i> Resumen Financiero:</strong>
                El sistema calcula y muestra automáticamente los totales generales en ambas monedas al aplicar filtros.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Total General</h6>
                        </div>
                        <div class="card-body text-center">
                            <h4>{{ $currency_primary->symbol ?? 'Bs' }} 1000.00 <small>Ej:</small></h4>
                            <small class="text-muted">Sumatoria de todos los montos principales</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-exchange-alt"></i> Total Monto Cambiario</h6>
                        </div>
                        <div class="card-body text-center">
                            <h4>{{ $currency_secondary->symbo ?? 'USD' }} 100.00</h4>
                            <small class="text-muted">Sumatoria de montos en moneda de referencia</small>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Exportación PDF</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-file-pdf"></i> Generación de Reportes PDF</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Características del PDF generado:</strong></p>
                            <ul>
                                <li><strong>Incluye todos los filtros aplicados</strong></li>
                                <li><strong>Mantiene el formato de la tabla</strong></li>
                                <li><strong>Incluye totales y resúmenes</strong></li>
                                <li><strong>Encabezado institucional</strong></li>
                                <li><strong>Fecha y hora de generación</strong></li>
                                <li><strong>Paginación automática</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-print"></i> Cómo Generar PDF:</strong>
                        <ol class="mb-0">
                            <li>Aplicar filtros deseados</li>
                            <li>Hacer click en 
                                <span class="badge badge-dark">
                                    <i class="fa fa-file-pdf"></i>
                                </span>
                            </li>
                            <li>Se abre en nueva pestaña</li>
                            <li>Imprimir o guardar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Permisos y Seguridad -->
        <section id="permisos" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-shield-alt text-primary"></i>
                Permisos y Seguridad
            </h2>

            <h4>Control de Acceso</h4>
            <div class="warning-box">
                <strong><i class="fas fa-user-lock"></i> Restricciones por Rol:</strong>
                El acceso al listado de ingresos está restringido a usuarios con rol de administrador mediante middleware.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Operaciones Permitidas</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Ver listado completo</strong></li>
                                <li><strong>Aplicar filtros de búsqueda</strong></li>
                                <li><strong>Exportar a PDF</strong></li>
                                <li><strong>Ver detalles</strong></li>
                                <li><strong>Navegar entre páginas</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-ban"></i> Operaciones Restringidas</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Editar registros</strong> (solo admin)</li>
                                <li><strong>Eliminar registros</strong> (solo admin)</li>
                                <li><strong>Ver eliminados</strong> (solo admin)</li>
                                <li><strong>Acceso sin autenticación</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
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
                                <i class="fas fa-question"></i> ¿Cómo busco pagos de un representante específico?
                            </button>
                        </h5>
                    </div>
                    <div id="answer1" class="collapse show" data-parent="#faqAccordion">
                        <div class="card-body">
                            Use el campo <strong>"Identificador"</strong> e ingrese la cédula del representante (con o sin formato). El sistema buscará en estudiante y representante automáticamente.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="card">
                    <div class="card-header" id="faq2">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer2">
                                <i class="fas fa-question"></i> ¿Por qué algunos montos aparecen en negrita y azul?
                            </button>
                        </h5>
                    </div>
                    <div id="answer2" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Indican que tienen una <strong>tasa de cambio asociada</strong>. Pase el mouse sobre el monto para ver los detalles de la tasa de cambio utilizada.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="card">
                    <div class="card-header" id="faq3">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer3">
                                <i class="fas fa-question"></i> ¿Qué significa el fondo rojo en algunas filas?
                            </button>
                        </h5>
                    </div>
                    <div id="answer3" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Indica que el registro fue <strong>eliminado lógicamente</strong>. Solo los administradores pueden ver estos registros y no son editables.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="card">
                    <div class="card-header" id="faq4">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer4">
                                <i class="fas fa-question"></i> ¿Cómo exporto los datos filtrados a PDF?
                            </button>
                        </h5>
                    </div>
                    <div id="answer4" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Aplique los filtros deseados y haga click en el botón 
                            <span class="badge badge-dark">
                                <i class="fa fa-file-pdf"></i>
                            </span> 
                            en el formulario de búsqueda. Se generará un PDF con los datos actuales.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="card">
                    <div class="card-header" id="faq5">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer5">
                                <i class="fas fa-question"></i> ¿Puedo editar un pago registrado por error?
                            </button>
                        </h5>
                    </div>
                    <div id="answer5" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>Sí</strong>, si tiene permisos de administrador. Use el botón 
                            <span class="badge badge-warning">
                                <i class="fas fa-edit"></i>
                            </span> 
                            para acceder al formulario de edición. Los cambios recalculan automáticamente los montos cambiarios.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="text-center mt-4">
            <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.ingresos.listado') }}', '_blank')">
                <i class="fas fa-print"></i> Versión Imprimible
            </button>
        </div>

        <hr>

        <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
        <tr>
            <td align="center">
                <font size="2" color="#ffffff" face="Arial">
                    Guía del Sistema de Listado de Ingresos Registrados - Versión 1.0
                </font>
            </td>
        </tr>
    </table>
    </div>
</div>

@section('stylesheet')
@parent
<style>
.guide-section {
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eaeaea;
}
.tip-box {
    background: #e8f5e8;
    border-left: 4px solid #28a745;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 4px 4px 0;
}
.warning-box {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 4px 4px 0;
}
.danger-box {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 4px 4px 0;
}
.success-box {
    background: #d1ecf1;
    border-left: 4px solid #17a2b8;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 4px 4px 0;
}
.currency-box {
    background: #e9ecef;
    border-left: 4px solid #6c757d;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 4px 4px 0;
}
.step-card {
    transition: transform 0.2s;
    height: 100%;
}
.step-card:hover {
    transform: translateY(-5px);
}
.step-number {
    width: 40px;
    height: 40px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.feature-badge {
    margin: 2px;
    font-size: 0.7rem;
}
.resource-badge {
    margin: 2px;
    font-size: 0.8rem;
}
.screenshot {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    background: #f8f9fa;
}
</style>
@endsection