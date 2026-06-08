<div class="row">
    <!-- Contenido Principal -->
    <div class="col-md-12">
        <!-- Introducción -->
        <section id="introduccion" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-list-alt text-primary"></i>
                Listado de Reportes de Pago
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <p class="lead">
                        El <strong>Listado de Reportes de Pago</strong> es una sección para la gestión y supervisión de todos los pagos reportados por los representantes (a través del portal web) antes de su procesamiento en el SAEFL.
                    </p>

                    <div class="tip-box">
                        <strong><i class="fas fa-lightbulb"></i> Propósito:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Visualización centralizada de reportes de pago</li>
                            <li>Filtrado avanzado por múltiples criterios</li>
                            <li>Seguimiento del estado de procesamiento</li>
                            <li>Integración con el asistente de registro</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-file-invoice-dollar fa-3x text-success mb-3"></i>
                            <h5>Características Principales</h5>
                            <span class="badge badge-primary feature-badge">Vista Tabular Avanzada</span>
                            <span class="badge badge-success feature-badge">Filtros Múltiples</span>
                            <span class="badge badge-info feature-badge">Estados de procesamiento</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Estructura de Datos -->
        <section id="estructura" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-database text-warning"></i>
                Estructura de Datos del Reporte
            </h2>

            <div class="info-box">
                <strong><i class="fas fa-info-circle"></i> Característica Única:</strong>
                Cada reporte de pago puede contener <strong>una transacción independiente</strong> en una sola entrada.
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-user-tie"></i> Datos del Representante</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Cédula de Identidad</strong> (CI)</li>
                                <li><strong>Nombre Completo</strong></li>
                                <li><strong>Teléfono de Contacto</strong></li>
                                <li><strong>Tipo de Pago</strong> reportado</li>
                                <li><strong>Comentarios</strong> adicionales</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Transacción</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Transacción</th>
                            <th>Campos Incluidos</th>
                            <th>Color Identificador</th>
                            <th>Estado de Aplicación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Transacción 1</strong></td>
                            <td>Banco, Método, Referencia, Monto, Fecha, Observaciones, Imagen</td>
                            <td><span class="badge badge-warning">Amarillo</span></td>
                            <td><code>Por procesar</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Filtros de Búsqueda -->
        <section id="filtros" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-filter text-primary"></i>
                Filtros de Búsqueda Avanzada
            </h2>

            <h4>Filtros Principales</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="25%">Filtro</th>
                                    <th width="40%">Descripción</th>
                                    <th width="20%">Tipo</th>
                                    <th width="15%">Ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>CI Representante</strong></td>
                                    <td>Búsqueda por cédula exacta del representante</td>
                                    <td><span class="badge badge-info">Texto</span></td>
                                    <td>V12345678</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Aprobación</strong></td>
                                    <td>Filtra reportes aprobados o no aprobados</td>
                                    <td><span class="badge badge-success">Select</span></td>
                                    <td>SI / NO</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Aplicación</strong></td>
                                    <td>Filtra transacciones aplicadas o pendientes</td>
                                    <td><span class="badge badge-success">Select</span></td>
                                    <td>APLICADAS / NO APLICADAS</td>
                                </tr>
                                <tr>
                                    <td><strong>Banco Receptor</strong></td>
                                    <td>Selección de entidad bancaria receptora</td>
                                    <td><span class="badge badge-success">Select</span></td>
                                    <td>Banco de Venezuela</td>
                                </tr>
                                <tr>
                                    <td><strong>N° Referencia</strong></td>
                                    <td>Búsqueda en todas las transacciones del reporte</td>
                                    <td><span class="badge badge-info">Texto</span></td>
                                    <td>TRF-123456</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-search"></i> Búsqueda Inteligente:</strong>
                        <ul class="mb-0">
                            <li>Búsqueda en <strong>4 campos de referencia</strong> simultáneamente</li>
                            <li>Filtro por <strong>4 bancos diferentes</strong> en un solo reporte</li>
                            <li>Búsqueda por <strong>rango de fechas</strong></li>
                            <li>Filtros <strong>combinables</strong> para precisión</li>
                        </ul>
                    </div>

                    <div class="warning-box mt-3">
                        <strong><i class="fas fa-exclamation-triangle"></i> Filtro de Banco:</strong>
                        Al seleccionar un banco, se buscan coincidencias en el campos de banco del reporte.
                    </div>
                </div>
            </div>
        </section>

        <!-- Interpretación de la Tabla -->
        <section id="tabla" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-table text-primary"></i>
                Interpretación de la Vista Tabular
            </h2>

            <h4>Columnas</h4>

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Columna</th>
                                    <th>Contenido</th>
                                    <th>Importancia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>N°</strong></td>
                                    <td>Número de orden en la lista</td>
                                    <td><span class="badge badge-secondary">Baja</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Estado</strong></td>
                                    <td>Indicadores de aplicación por transacción</td>
                                    <td><span class="badge badge-danger">Alta</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Registrado</strong></td>
                                    <td>Fecha de creación del reporte</td>
                                    <td><span class="badge badge-secondary">Media</span></td>
                                </tr>
                                <tr>
                                    <td><strong>CI Representante</strong></td>
                                    <td>Cédula del representante</td>
                                    <td><span class="badge badge-danger">Alta</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Representante</strong></td>
                                    <td>Nombre completo</td>
                                    <td><span class="badge badge-danger">Alta</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Teléfono</strong></td>
                                    <td>Contacto del representante</td>
                                    <td><span class="badge badge-warning">Media</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Estados de Aplicación</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Transacción Aplicada</h6>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-1"><strong>Verde - Check</strong></p>
                            <small>La transacción ya fue procesada en el asistente de registro</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-clock"></i> Transacción Pendiente</h6>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-circle fa-2x text-dark mb-2"></i>
                            <p class="mb-1"><strong>Naranja - Círculo</strong></p>
                            <small>Click para ir al asistente y aplicar esta transacción</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Acciones y Funcionalidades -->
        <section id="acciones" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-mouse-pointer text-primary"></i>
                Acciones y Funcionalidades
            </h2>

            <h4>Acciones por Fila</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Elemento</th>
                                    <th>Acción</th>
                                    <th>Resultado</th>
                                    <th>Requisitos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Botón Amarillo</strong> <span class="badge badge-warning">○</span></td>
                                    <td>Click en transacción pendiente</td>
                                    <td>Redirige al asistente de registro con datos pre-cargados</td>
                                    <td>Transacción no aplicada</td>
                                </tr>
                                <tr>
                                    <td><strong>Icono Verde</strong> <span class="badge badge-success">✓</span></td>
                                    <td>Indicador visual</td>
                                    <td>Muestra que la transacción fue aplicada</td>
                                    <td>Transacción procesada</td>
                                </tr>
                                <tr>
                                    <td><strong>Imagen Comprobante</strong> <span class="badge badge-info"><i class="fas fa-file-image"></i></span></td>
                                    <td>Click para visualizar</td>
                                    <td>Modal con imagen del comprobante</td>
                                    <td>Archivo de imagen existente</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-rocket"></i> Flujo Rápido:</strong>
                        <ul class="mb-0">
                            <li><strong>1.</strong> Buscar reporte pendiente</li>
                            <li><strong>2.</strong> Click en botón amarillo</li>
                            <li><strong>3.</strong> Asistente pre-cargado</li>
                            <li><strong>4.</strong> Completar aplicación</li>
                            <li><strong>5.</strong> Regresar a lista actualizada</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Herramientas de Navegación</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-search fa-2x text-primary mb-3"></i>
                            <h6>Buscar</h6>
                            <p class="small">Ejecutar filtros</p>
                            <span class="badge badge-primary">Aplicar Búsqueda</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-redo fa-2x text-success mb-3"></i>
                            <h6>Refrescar</h6>
                            <p class="small">Limpiar filtros</p>
                            <span class="badge badge-success">Resetear Vista</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Integración con Asistente -->
        <section id="integracion" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-link text-primary"></i>
                Integración con Asistente de Registro
            </h2>

            <div class="success-box">
                <strong><i class="fas fa-sync-alt"></i> Flujo Integrado:</strong>
                Este listado funciona como el <strong>punto de entrada principal</strong> hacia el Asistente de Registro de Pagos para aplicar transacciones reportadas.
            </div>

            <h4>Datos Pre-cargados en el Asistente</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-database"></i> Información Automática</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Representante:</strong> Datos completos pre-cargados</li>
                                <li><strong>Banco Receptor:</strong> Según transacción seleccionada</li>
                                <li><strong>Método de Pago:</strong> Definido en el reporte</li>
                                <li><strong>N° Transacción:</strong> Referencia exacta</li>
                                <li><strong>Fecha Transacción:</strong> Del comprobante</li>
                                <li><strong>Monto Reportado:</strong> Monto exacto a aplicar</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-bolt"></i> Beneficios</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Reducción de errores</strong> por tipeo manual</li>
                                <li><strong>Ahorro de tiempo</strong> en ingreso de datos</li>
                                <li><strong>Consistencia</strong> entre reporte y registro</li>
                                <li><strong>Trazabilidad completa</strong> del proceso</li>
                                <li><strong>Actualización automática</strong> de estados</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Estados Sincronizados</h4>
            <div class="alert alert-warning">
                <strong><i class="fas fa-sync"></i> Actualización en Tiempo Real:</strong>
                Al completar exitosamente el asistente de registro, el listado se actualiza automáticamente mostrando el nuevo estado de aplicación de la transacción procesada.
            </div>
        </section>

        <!-- Preguntas Frecuentes -->
        <section id="faq" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-question-circle text-primary"></i>
                Preguntas Frecuentes (FAQ)
            </h2>

            <div class="accordion" id="faqAccordion">
                <!-- Pregunta 2 -->
                <div class="card">
                    <div class="card-header" id="faq2">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#answer2">
                                <i class="fas fa-question"></i> ¿Qué significa cuando una transacción muestra el icono verde (✓)?
                            </button>
                        </h5>
                    </div>
                    <div id="answer2" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            El icono verde <strong>indica que la transacción ya fue procesada</strong> a través del Asistente de Registro de Pagos. Esto significa que los fondos fueron aplicados a cuotas específicas y se generó el recibo correspondiente.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="card">
                    <div class="card-header" id="faq3">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer3">
                                <i class="fas fa-question"></i> ¿Cómo aplico una transacción pendiente?
                            </button>
                        </h5>
                    </div>
                    <div id="answer3" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Simplemente <strong>haga click en el botón naranja con el círculo (○)</strong> correspondiente a la transacción pendiente. Esto le llevará al Asistente de Registro con todos los datos pre-cargados para completar el proceso.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="card">
                    <div class="card-header" id="faq4">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer4">
                                <i class="fas fa-question"></i> ¿Puedo filtrar solo las transacciones pendientes de aplicación?
                            </button>
                        </h5>
                    </div>
                    <div id="answer4" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>Sí</strong>, use el filtro <strong>"Estado Aplicación"</strong> y seleccione <strong>"NO APLICADAS"</strong>. Esto mostrará únicamente los reportes que tienen al menos una transacción pendiente de procesar.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="card">
                    <div class="card-header" id="faq5">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer5">
                                <i class="fas fa-question"></i> ¿Qué hago si no encuentro un reporte específico?
                            </button>
                        </h5>
                    </div>
                    <div id="answer5" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Revise los siguientes aspectos:
                            <ul>
                                <li><strong>Filtros activos:</strong> Puede tener filtros aplicados que ocultan el reporte</li>
                                <li><strong>Rango de fechas:</strong> Verifique que cubra la fecha del reporte</li>
                                <li><strong>CI exacto:</strong> Confirme que está usando la cédula correcta</li>
                                <li><strong>Referencia parcial:</strong> Use solo parte del número de referencia</li>
                                <li><strong>Click en "Refrescar":</strong> Para limpiar todos los filtros</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Botón de Impresión -->
        <div class="text-center mt-4">
            <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.payments.listado') }}', '_blank')">
                <i class="fas fa-print"></i> Versión Imprimible
            </button>
        </div>

        <hr>

        <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
            <tr>
                <td align="center">
                    <font size="2" color="#ffffff" face="Arial">
                        Guía del Listado de Reportes de Pago - SAEFL - Versión 1.0
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
    border-bottom: 1px solid #e9ecef;
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

.info-box {
    background: #d1ecf1;
    border-left: 4px solid #17a2b8;
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
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 4px 4px 0;
}

.feature-badge {
    display: block;
    margin: 0.25rem 0;
    font-size: 0.75rem;
}

.step-card {
    transition: transform 0.2s;
    height: 100%;
}

.step-card:hover {
    transform: translateY(-5px);
}

.step-number {
    width: 50px;
    height: 50px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.5rem;
}

.resource-badge {
    margin: 0.1rem;
}

.currency-box {
    background: #e8f4fd;
    border: 2px solid #17a2b8;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 4px;
    text-align: center;
}

.screenshot {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    overflow: hidden;
}
</style>
@endsection