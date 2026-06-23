<div class="row">
    <!-- Contenido Principal -->
    <div class="col-md-12">
        <!-- Introducción -->
        <section id="introduccion" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-history text-primary"></i>
                Introducción
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <p class="lead">
                        El <strong>Módulo de Histórico de Pagos</strong> es una herramienta integral diseñada para consultar, analizar y gestionar el historial completo de transacciones financieras de los representantes en la institución.
                    </p>

                    <div class="tip-box">
                        <strong><i class="fas fa-lightbulb"></i> Propósito del Sistema:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Consulta centralizada de historial de pagos por representante</li>
                            <li>Visualización detallada de transacciones combinadas</li>
                            <li>Análisis de recursos aplicados (efectivo, abonos, créditos)</li>
                            <li>Generación de recibos históricos en PDF</li>
                            <li>Detección y marcado de estados irregulares en pagos</li>
                            <li>Acceso rápido al asistente de nuevos pagos</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-file-invoice-dollar fa-3x text-success mb-3"></i>
                            <h5>Características Principales</h5>
                            <span class="badge badge-primary feature-badge">Búsqueda Inteligente</span>
                            <span class="badge badge-success feature-badge">Vista Detallada</span>
                            <span class="badge badge-info feature-badge">Multi-Moneda</span>
                            <span class="badge badge-warning feature-badge">Modal AJAX</span>
                            <span class="badge badge-danger feature-badge">PDF Recibos</span>
                            <span class="badge badge-dark feature-badge">Responsive</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Requisitos del Sistema -->
        <section id="requisitos" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-clipboard-check text-warning"></i>
                Requisitos del Sistema
            </h2>

            <div class="currency-box">
                <strong><i class="fas fa-database"></i> Requisito Crítico:</strong>
                <strong>Debe existir al menos un representante registrado en el sistema con transacciones de pago.</strong> El histórico no mostrará datos sin esta información fundamental.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Configuraciones Requeridas</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Representantes registrados</strong> en el sistema</li>
                                <li><strong>Transacciones de pago</strong> existentes</li>
                                <li><strong>Estructura de cuentas por pagar</strong> configurada</li>
                                <li><strong>Estudiantes asociados</strong> a representantes</li>
                                <li><strong>Sistema de monedas</strong> operativo (Bs/USD)</li>
                                <li><strong>Pagos combinados</strong> generados previamente</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-cogs"></i> Elementos Opcionales</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Abonos aplicados en transacciones</li>
                                <li>Créditos generados y aplicados</li>
                                <li>Descuentos aplicados</li>
                                <li>Múltiples métodos de pago</li>
                                <li>Transacciones en diferentes monedas</li>
                                <li>Estados irregulares en pagos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Flujo Principal -->
        <section id="flujo-principal" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-project-diagram text-warning"></i>
                Flujo Principal - Consulta en 4 Pasos
            </h2>

            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">1</div>
                            <h6>Búsqueda</h6>
                            <p class="small">Filtrar representante por CI o nombre</p>
                            <span class="badge badge-primary">30 segundos</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">2</div>
                            <h6>Vista General</h6>
                            <p class="small">Revisar tabla resumen de pagos</p>
                            <span class="badge badge-info">1 minuto</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">3</div>
                            <h6>Detalles</h6>
                            <p class="small">Modal con transacción específica</p>
                            <span class="badge badge-warning">2 minutos</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">4</div>
                            <h6>Acciones</h6>
                            <p class="small">Generar PDF</p>
                            <span class="badge badge-success">1 minuto</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="success-box">
                <strong><i class="fas fa-rocket"></i> Tiempo Total Estimado:</strong>
                Consultar el histórico completo toma aproximadamente <strong>4-5 minutos</strong> dependiendo de la cantidad de transacciones.
            </div>
        </section>

        <!-- Paso 1: Búsqueda de Representante -->
        <section id="paso1" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-search text-primary"></i>
                Paso 1: Búsqueda Inteligente de Representante
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <h4>Métodos de Búsqueda</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="25%">Campo</th>
                                    <th width="35%">Descripción</th>
                                    <th width="20%">Obligatorio</th>
                                    <th width="20%">Ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Help Representante</strong></td>
                                    <td>Filtro en tiempo real por CI o nombre completo</td>
                                    <td><span class="badge badge-warning">Opcional</span></td>
                                    <td>V-12345678</td>
                                </tr>
                                <tr>
                                    <td><strong>Representante ID</strong></td>
                                    <td>Selección formal desde lista desplegable</td>
                                    <td><span class="badge badge-danger">Sí</span></td>
                                    <td>María González</td>
                                </tr>
                                <tr>
                                    <td><strong>Botón Buscar</strong></td>
                                    <td>Ejecuta la consulta principal en base de datos</td>
                                    <td><span class="badge badge-danger">Sí</span></td>
                                    <td>Acción principal</td>
                                </tr>
                                <tr>
                                    <td><strong>Asistente Pago</strong></td>
                                    <td>Acceso rápido a registro de nuevos pagos</td>
                                    <td><span class="badge badge-success">Navegación</span></td>
                                    <td>Botón verde con "+"</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-filter"></i> Filtro Inteligente:</strong>
                        <ul class="mb-0">
                            <li>Escriba CI → Filtra lista automáticamente</li>
                            <li>Escriba nombre → Filtra por coincidencias parciales</li>
                            <li>Búsqueda en tiempo real → Sin recarga de página</li>
                            <li>Selección única → Representante específico</li>
                            <li>Case insensitive → No distingue mayúsculas/minúsculas</li>
                        </ul>
                    </div>

                    <div class="warning-box mt-3">
                        <strong><i class="fas fa-exclamation-triangle"></i> Validación:</strong>
                        El sistema requiere que seleccione un representante de la lista antes de permitir la búsqueda. El campo "Help Representante" solo filtra, no selecciona.
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Características de Búsqueda</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-bolt"></i> Búsqueda Rápida (JavaScript)</h6>
                        </div>
                        <div class="card-body">
                            <p>Al usar el campo <strong>"CI o nombre"</strong>:</p>
                            <ul class="small">
                                <li>Filtra la lista desplegable en tiempo real</li>
                                <li>No requiere enviar el formulario</li>
                                <li>Mantiene la selección actual si existe</li>
                                <li>Funciona con expresiones regulares</li>
                                <li>Performance optimizado en cliente</li>
                                <li>Sin latencia de servidor</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-database"></i> Búsqueda Formal (Servidor)</h6>
                        </div>
                        <div class="card-body">
                            <p>Al usar el <strong>select de representantes</strong>:</p>
                            <ul class="small">
                                <li>Lista completa desde base de datos</li>
                                <li>Datos validados y consistentes</li>
                                <li>Información actualizada en tiempo real</li>
                                <li>Requiere envío de formulario POST/GET</li>
                                <li>Genera consulta SQL optimizada</li>
                                <li>Incluye relaciones con estudiantes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Paso 2: Vista General de Pagos -->
        <section id="paso2" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-table text-primary"></i>
                Paso 2: Vista General - Tabla de Histórico
            </h2>

            <h4>Estructura de la Tabla</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Columna</th>
                                    <th>Descripción</th>
                                    <th>Formato</th>
                                    <th>Importancia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>FEC. REGISTRO</strong></td>
                                    <td>Fecha y hora exacta del pago combinado</td>
                                    <td>dd-mm-YYYY hh:ii (24h)</td>
                                    <td><span class="badge badge-danger">Alta</span></td>
                                </tr>
                                <tr>
                                    <td><strong>CONCEPTOS</strong></td>
                                    <td>Detalle de cuentas por pagar y estudiantes beneficiados</td>
                                    <td>Lista jerárquica con nombres</td>
                                    <td><span class="badge badge-danger">Alta</span></td>
                                </tr>
                                <tr>
                                    <td><strong>RECURSO</strong></td>
                                    <td>Total de recursos aplicados (ING + ABN + CAF)</td>
                                    <td>Monto Bs | $ USD</td>
                                    <td><span class="badge badge-warning">Media</span></td>
                                </tr>
                                <tr>
                                    <td><strong>PAGADO</strong></td>
                                    <td>Total aplicado a conceptos/cuotas</td>
                                    <td>Monto Bs | $ USD</td>
                                    <td><span class="badge badge-warning">Media</span></td>
                                </tr>
                                <tr>
                                    <td><strong>CAF</strong></td>
                                    <td>Créditos a favor generados (diferencia)</td>
                                    <td>Monto Bs | $ USD</td>
                                    <td><span class="badge badge-info">Baja</span></td>
                                </tr>
                                <tr>
                                    <td><strong>ACCIÓN</strong></td>
                                    <td>Operaciones disponibles por registro</td>
                                    <td>Grupo de botones</td>
                                    <td><span class="badge badge-success">Funcional</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-palette"></i> Indicadores Visuales:</strong>
                        <ul class="mb-0">
                            <li><strong>Fila normal:</strong> Fondo blanco estándar</li>
                            <li><strong>Estado irregular:</strong> Fondo rojo claro (#f8d7da)</li>
                            <li><strong>ID Admin:</strong> Visible solo para administradores</li>
                            <li><strong>Botones:</strong> Color según función e estado</li>
                            <li><strong>Hover:</strong> Efecto visual al pasar mouse</li>
                        </ul>
                    </div>

                    <div class="warning-box mt-3">
                        <strong><i class="fas fa-calculator"></i> Fórmula de Recursos:</strong>
                        <strong>RECURSO = Σ(INGRESOS) + Σ(ABONOS) + Σ(CRÉDITOS APLICADOS)</strong>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Desglose de Recursos</h4>
            <div class="alert alert-info">
                <strong><i class="fas fa-money-bill-wave"></i> Tipos de Recursos Mostrados:</strong>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <strong class="text-success">INGRESOS (ING):</strong>
                        <ul class="mb-0 small">
                            <li>Pagos en efectivo reportados</li>
                            <li>Transferencias bancarias</li>
                            <li>Depósitos directos</li>
                            <li>Otros métodos de pago</li>
                            <li>Referencias únicas</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-warning">ABONOS (ABN):</strong>
                        <ul class="mb-0 small">
                            <li>Pagos anticipados aplicados</li>
                            <li>Recursos en tránsito utilizados</li>
                            <li>Fondos pre-existentes</li>
                            <li>Reservas aplicadas</li>
                            <li>Saldo disponible usado</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-info">CRÉDITOS (CAF):</strong>
                        <ul class="mb-0 small">
                            <li>Saldo a favor aplicado</li>
                            <li>Excedentes de pagos anteriores</li>
                            <li>Ajustes positivos</li>
                            <li>Bonificaciones</li>
                            <li>Creditos generados</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h4>Estructura de Datos por Fila</h4>
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-code"></i> Relaciones de Datos por Registro</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>PagoCombinado (Principal):</strong>
                            <ul class="small">
                                <li>ID único del pago combinado</li>
                                <li>Fecha de creación (created_at)</li>
                                <li>Monto total pagado</li>
                                <li>Monto créditos generados</li>
                                <li>Estado irregular (flag)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Relaciones:</strong>
                            <ul class="small">
                                <li><strong>Registropagos:</strong> → Registros individuales</li>
                                <li><strong>Estudiant:</strong> → Estudiantes beneficiados</li>
                                <li><strong>Cuentaxpagar:</strong> → Conceptos pagados</li>
                                <li><strong>Ingresos:</strong> → Transacciones de entrada</li>
                                <li><strong>Pagos:</strong> → Aplicaciones específicas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Paso 3: Modal de Detalles -->
        <section id="paso3" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-expand-alt text-primary"></i>
                Paso 3: Vista Detallada - Modal AJAX
            </h2>

            <h4>Tecnología y Rendimiento</h4>
            <div class="success-box">
                <strong><i class="fas fa-magic"></i> Tecnología AJAX:</strong>
                Los detalles se cargan mediante <strong>peticiones AJAX asíncronas</strong> sin recargar la página, mejorando la experiencia de usuario y el rendimiento del sistema.
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Sección</th>
                                    <th>Contenido</th>
                                    <th>Componentes Incluidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>REGISTRO</strong></td>
                                    <td>Información básica del pago combinado</td>
                                    <td>Fecha, ID, observaciones, usuario</td>
                                </tr>
                                <tr>
                                    <td><strong>TRANSACCIÓN</strong></td>
                                    <td>Detalle de ingresos y métodos de pago</td>
                                    <td>Bancos, referencias, montos, fechas</td>
                                </tr>
                                <tr>
                                    <td><strong>PAGADO</strong></td>
                                    <td>Desglose completo de aplicación de recursos</td>
                                    <td>Pagos, créditos, descuentos, abonos</td>
                                </tr>
                                <tr>
                                    <td><strong>CTA. CANCELADA</strong></td>
                                    <td>Conceptos específicos pagados y cancelados</td>
                                    <td>Cuentas por pagar satisfechas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-rocket"></i> Ventajas AJAX:</strong>
                        <ul class="mb-0">
                            <li><strong>Carga bajo demanda:</strong> Solo cuando se necesita</li>
                            <li><strong>Sin recarga:</strong> Mantiene contexto actual</li>
                            <li><strong>Rápido:</strong> Solo datos necesarios</li>
                            <li><strong>Eficiente:</strong> Menos carga del servidor</li>
                            <li><strong>Interactivo:</strong> Mejor experiencia usuario</li>
                        </ul>
                    </div>

                    <div class="warning-box mt-3">
                        <strong><i class="fas fa-columns"></i> Diseño Responsive:</strong>
                        Columnas <strong>c_registro, c_ingreso, c_conceptocancelados</strong> se ocultan automáticamente en dispositivos móviles para optimizar el espacio.
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Componentes Detallados del Modal</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-file-invoice"></i> Registro Principal</h6>
                        </div>
                        <div class="card-body">
                            <p>Información del <strong>Pago Combinado</strong>:</p>
                            <ul class="small">
                                <li><strong>Fecha y hora</strong> de creación exacta</li>
                                <li><strong>ID único</strong> de transacción</li>
                                <li><strong>Observaciones</strong> generales del pago</li>
                                <li><strong>Estado</strong> del registro (activo/inactivo)</li>
                                <li><strong>Usuario</strong> que registró la transacción</li>
                                <li><strong>Abonos aplicados</strong> si existen</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-money-check"></i> Transacciones</h6>
                        </div>
                        <div class="card-body">
                            <p>Detalle de <strong>Ingresos Reportados</strong>:</p>
                            <ul class="small">
                                <li><strong>Métodos de pago</strong> utilizados</li>
                                <li><strong>Referencias bancarias</strong> únicas</li>
                                <li><strong>Montos</strong> en Bs y USD separados</li>
                                <li><strong>Fechas</strong> de transacción y pago</li>
                                <li><strong>Bancos</strong> e instituciones involucradas</li>
                                <li><strong>Números</strong> de transacción/comprobante</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-credit-card"></i> Aplicación de Recursos</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Desglose de Pagos Aplicados</strong>:</p>
                            <ul class="small">
                                <li><strong>Pagos directos</strong> a conceptos</li>
                                <li><strong>Créditos aplicados</strong> disponibles</li>
                                <li><strong>Créditos generados</strong> nuevos</li>
                                <li><strong>Descuentos aplicados</strong> especiales</li>
                                <li><strong>Distribución</strong> por estudiante/concepto</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Conceptos Cancelados</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Cuentas por Pagar Satisfechas</strong>:</p>
                            <ul class="small">
                                <li><strong>Lista completa</strong> de conceptos pagados</li>
                                <li><strong>Estudiantes</strong> beneficiados</li>
                                <li><strong>Montos específicos</strong> por concepto</li>
                                <li><strong>Fechas</strong> de vencimiento cubiertas</li>
                                <li><strong>Estados</strong> de cancelación</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Paso 4: Acciones Disponibles -->
        <section id="paso4" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-tools text-primary"></i>
                Paso 4: Acciones y Funcionalidades
            </h2>

            <h4>Botones de Acción por Registro</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Botón</th>
                                    <th>Función</th>
                                    <th>Color/Estado</th>
                                    <th>Comportamiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                                        <strong> Detalles</strong>
                                    </td>
                                    <td>Abre modal AJAX con información completa de la transacción</td>
                                    <td><span class="badge badge-info">Azul - Siempre activo</span></td>
                                    <td>Trigger AJAX → Carga modal → Muestra datos</td>
                                </tr>
                                <tr>
                                    <td>
                                        <button class="btn btn-dark btn-sm"><i class="fas fa-file-pdf"></i></button>
                                        <strong> PDF</strong>
                                    </td>
                                    <td>Genera recibo oficial en formato PDF descargable</td>
                                    <td><span class="badge badge-dark">Negro - Condicional</span></td>
                                    <td>Deshabilitado si status_irregular = true</td>
                                </tr>
                                <tr>
                                    <td>
                                        <button class="btn btn-success btn-sm"><i class="fas fa-plus"></i></button>
                                        <strong> Asistente</strong>
                                    </td>
                                    <td>Acceso rápido al asistente de registro de nuevos pagos</td>
                                    <td><span class="badge badge-success">Verde - Global</span></td>
                                    <td>Navegación directa con representante pre-cargado</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-mouse-pointer"></i> Interacción de Botones:</strong>
                        <ul class="mb-0">
                            <li><strong>Hover:</strong> Efecto visual al pasar mouse</li>
                            <li><strong>Disabled:</strong> Atenuado + no-click si no disponible</li>
                            <li><strong>Tooltips:</strong> Descripción emergente al hover</li>
                            <li><strong>Grupos:</strong> Organizados en btn-group</li>
                            <li><strong>Responsive:</strong> Adaptable a móviles</li>
                        </ul>
                    </div>

                    <div class="warning-box mt-3">
                        <strong><i class="fas fa-ban"></i> Estado Irregular:</strong>
                        Cuando <strong>status_irregular = true</strong>, el botón PDF se deshabilita automáticamente para evitar generar comprobantes de transacciones problemáticas.
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Generación de PDF - Recibos</h4>
            <div class="alert alert-success">
                <strong><i class="fas fa-file-pdf"></i> Características del Recibo PDF:</strong>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Información Institucional:</strong>
                        <ul class="small">
                            <li>Logo oficial y datos de la institución</li>
                            <li>Dirección, teléfonos, RIF</li>
                            <li>Encabezado profesional estandarizado</li>
                            <li>Número de recibo único y correlativo</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Contenido del Recibo:</strong>
                        <ul class="small">
                            <li>Datos completos del representante</li>
                            <li>Fecha y hora de emisión exacta</li>
                            <li>Detalle de transacción específica</li>
                            <li>Montos en Bs y USD claramente diferenciados</li>
                            <li>Firma digital del sistema</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h4>Flujos de Navegación y Acciones</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-plus-circle fa-2x text-primary mb-3"></i>
                            <h6>Nuevo Pago</h6>
                            <p class="small">Mismo representante</p>
                            <span class="badge badge-primary">Asistente Pago</span>
                            <p class="small mt-2">Acceso directo al asistente con el representante actual pre-seleccionado</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-search fa-2x text-success mb-3"></i>
                            <h6>Nueva Búsqueda</h6>
                            <p class="small">Cambiar representante</p>
                            <span class="badge badge-success">Reiniciar Búsqueda</span>
                            <p class="small mt-2">Volver al formulario de búsqueda para consultar otro representante</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-chart-bar fa-2x text-info mb-3"></i>
                            <h6>Reportes Avanzados</h6>
                            <p class="small">Análisis detallado</p>
                            <span class="badge badge-info">Módulo Reportes</span>
                            <p class="small mt-2">Acceder a reportes financieros y análisis avanzados del sistema</p>
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
                                <i class="fas fa-question"></i> ¿Por qué algunos pagos aparecen en rojo en la tabla?
                            </button>
                        </h5>
                    </div>
                    <div id="answer1" class="collapse show" data-parent="#faqAccordion">
                        <div class="card-body">
                            Los pagos que aparecen con <strong class="text-danger">fondo rojo</strong> tienen <strong>status_irregular = true</strong>. Esto indica que hubo algún problema o irregularidad en el registro del pago que requiere revisión administrativa. Ejemplos comunes:
                            <ul class="mt-2">
                                <li>Discrepancia en montos reportados vs aplicados</li>
                                <li>Problemas de conciliación bancaria</li>
                                <li>Errores en la distribución de recursos</li>
                                <li>Transacciones marcadas para revisión</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="card">
                    <div class="card-header" id="faq2">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer2">
                                <i class="fas fa-question"></i> ¿Puedo generar un recibo PDF de un pago con estado irregular?
                            </button>
                        </h5>
                    </div>
                    <div id="answer2" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong class="text-danger">No, no es posible.</strong> El sistema <strong>deshabilita automáticamente</strong> el botón de PDF para pagos con estado irregular. Esto evita generar comprobantes oficiales de transacciones que requieren revisión, ajuste o validación administrativa previa.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="card">
                    <div class="card-header" id="faq3">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer3">
                                <i class="fas fa-question"></i> ¿Qué significa la diferencia entre "RECURSO" y "PAGADO"?
                            </button>
                        </h5>
                    </div>
                    <div id="answer3" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong class="text-success">RECURSO:</strong>
                                    <ul>
                                        <li>Total de fondos disponibles</li>
                                        <li>Suma de todos los ingresos</li>
                                        <li>Incluye abonos aplicados</li>
                                        <li>Incluye créditos utilizados</li>
                                        <li><strong>FÓRMULA:</strong> ING + ABN + CAF</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong class="text-primary">PAGADO:</strong>
                                    <ul>
                                        <li>Total aplicado a conceptos</li>
                                        <li>Montos asignados a cuotas</li>
                                        <li>Pagos efectivamente realizados</li>
                                        <li>Recursos utilizados</li>
                                        <li><strong>RESULTADO:</strong> RECURSO - CAF_GENERADO</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="card">
                    <div class="card-header" id="faq4">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer4">
                                <i class="fas fa-question"></i> ¿Por qué no puedo ver los IDs de transacción?
                            </button>
                        </h5>
                    </div>
                    <div id="answer4" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Los <strong>IDs de transacción</strong> solo son visibles para usuarios con <strong class="text-warning">rol de administrador</strong>. Esto se controla mediante la directiva Blade <code>admin</code>. Para usuarios regulares, esta información se oculta para:
                            <ul class="mt-2">
                                <li>Simplificar la interfaz de usuario</li>
                                <li>Evitar confusión con números internos</li>
                                <li>Mantener el enfoque en la información relevante</li>
                                <li>Cumplir con políticas de seguridad</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="card">
                    <div class="card-header" id="faq5">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer5">
                                <i class="fas fa-question"></i> ¿Cómo accedo rápidamente a registrar un nuevo pago?
                            </button>
                        </h5>
                    </div>
                    <div id="answer5" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Use el <strong class="text-success">botón verde con icono "+"</strong> ubicado en la barra de búsqueda. Esta funcionalidad ofrece:
                            <ul class="mt-2">
                                <li>Acceso directo al asistente de registro de pagos</li>
                                <li>Representante actual pre-seleccionado automáticamente</li>
                                <li>Ahorro de tiempo al evitar nueva búsqueda</li>
                                <li>Flujo continuo entre consulta y registro</li>
                                <li>Mantenimiento del contexto de trabajo</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 6 -->
                <div class="card">
                    <div class="card-header" id="faq6">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer6">
                                <i class="fas fa-question"></i> ¿Los datos se actualizan en tiempo real?
                            </button>
                        </h5>
                    </div>
                    <div id="answer6" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>Sí, pero con matices:</strong>
                            <ul>
                                <li><strong>Búsqueda:</strong> Requiere enviar formulario para ver datos actualizados</li>
                                <li><strong>Filtro rápido:</strong> Funciona en tiempo real (JavaScript)</li>
                                <li><strong>Modal detalles:</strong> Siempre carga datos actualizados vía AJAX</li>
                                <li><strong>PDF:</strong> Genera con información del momento exacto</li>
                                <li><strong>Nuevos registros:</strong> Aparecen inmediatamente después de búsqueda</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Soporte Técnico -->
        <section id="soporte" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-headset text-primary"></i>
                Soporte Técnico y Contacto
            </h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Problemas Comunes</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>No aparecen representantes:</strong> Verificar que existan representantes registrados</li>
                                <li><strong>Botón PDF deshabilitado:</strong> El pago tiene estado irregular</li>
                                <li><strong>Modal no carga:</strong> Verificar conexión a internet</li>
                                <li><strong>Datos desactualizados:</strong> Realizar nueva búsqueda</li>
                                <li><strong>Error en filtro:</strong> Recargar la página</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-envelope"></i> Contacto Soporte</h6>
                        </div>
                        <div class="card-body">
                            <p>Para asistencia técnica contactar a:</p>
                            <ul>
                                <li><strong>Departamento de Sistemas:</strong> sistemas@institucion.edu.ve</li>
                                <li><strong>Soporte Técnico:</strong> soporte@institucion.edu.ve</li>
                                <li><strong>Teléfono:</strong> (0212) 123-4567</li>
                                <li><strong>Horario:</strong> Lunes a Viernes 8:00 am - 5:00 pm</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.representants.historico') }}', '_blank')">
            <i class="fas fa-print"></i> Versión Imprimible
        </button>
    </div>
</div>