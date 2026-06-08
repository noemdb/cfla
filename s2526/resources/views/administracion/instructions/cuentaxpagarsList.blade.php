<div class="row">
    <!-- Contenido Principal -->
    <div class="col-md-12">
        <!-- Introducción -->
        <section id="introduccion" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-search-dollar text-primary"></i>
                Representantes con conceptos de cobro pendiente
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <p class="lead">
                        <strong>Consulta de Deudas por Mensualidad</strong> es una herramienta
                        especializada para identificar y gestionar las obligaciones pendientes de los representantes
                        organizadas por periodos mensuales específicos.
                    </p>

                    <div class="tip-box">
                        <strong><i class="fas fa-lightbulb"></i> Propósito:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Consulta centralizada de deudas por periodo mensual</li>
                            <li>Filtrado inteligente por plan de pago y grado</li>
                            <li>Cálculo automatizado de saldos y montos pendientes</li>
                            <li>Exportación de reportes para seguimiento</li>
                            <li>Integración con el sistema de pagos principal</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-file-invoice-dollar fa-3x text-success mb-3"></i>
                            <h5>Características Principales</h5>
                            <span class="badge badge-primary feature-badge">Filtro Mensual</span>
                            <span class="badge badge-success feature-badge">Múltiples Planes</span>
                            <span class="badge badge-info feature-badge">Por Grados</span>
                            <span class="badge badge-warning feature-badge">Exportación</span>
                            <span class="badge badge-danger feature-badge">Deudas Detalladas</span>
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
                <strong><i class="fas fa-database"></i> Requisito Crítico:</strong>
                <strong>Deben existir Conceptos por cobrar generadas en el sistema.</strong> El reporte no mostrará datos
                sin cuotas creadas para el periodo seleccionado.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Configuraciones Requeridas</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Conceptos por cobrar</strong> generadas para el periodo</li>
                                <li><strong>Representantes</strong> registrados en el sistema</li>
                                <li><strong>Estudiantes activos</strong> asociados a representantes</li>
                                <li><strong>Planes de pago</strong> configurados</li>
                                <li><strong>Grados académicos</strong> definidos</li>
                                <li><strong>Inscripciones vigentes</strong> de estudiantes</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-cogs"></i> Elementos Calculados</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Deudas pendientes por estudiante</li>
                                <li>Abonos disponibles del representante</li>
                                <li>Créditos a favor del representante</li>
                                <li>Saldo a favor calculado</li>
                                <li>Deuda total consolidada</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Flujo de Trabajo -->
        <section id="flujo" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-project-diagram text-primary"></i>
                Flujo de Trabajo - 4 Pasos
            </h2>

            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">1</div>
                            <h6>Selección de Periodo</h6>
                            <p class="small">Elegir mensualidad de referencia</p>
                            <span class="badge badge-primary">30 segundos</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">2</div>
                            <h6>Aplicar Filtros</h6>
                            <p class="small">Plan de pago y grado</p>
                            <span class="badge badge-info">20 segundos</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">3</div>
                            <h6>Revisar Resultados</h6>
                            <p class="small">Analizar deudas detectadas</p>
                            <span class="badge badge-success">2 minutos</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">4</div>
                            <h6>Exportar Reporte</h6>
                            <p class="small">Generar archivo para seguimiento</p>
                            <span class="badge badge-warning">1 minuto</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="success-box mt-3">
                <strong><i class="fas fa-rocket"></i> Tiempo Total Estimado:</strong>
                Obtener un reporte completo toma aproximadamente <strong>4-5 minutos</strong> dependiendo de la cantidad
                de datos.
            </div>
        </section>

        <!-- Paso 1: Selección de Periodo -->
        <section id="paso1" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-calendar-alt text-primary"></i>
                Paso 1: Selección de Mensualidad
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <h4>Parámetro Principal</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="30%">Campo</th>
                                    <th width="40%">Descripción</th>
                                    <th width="15%">Obligatorio</th>
                                    <th width="15%">Ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Mensualidad de Referencia</strong></td>
                                    <td>Periodo mensual para consultar deudas</td>
                                    <td><span class="badge badge-success">Sí</span></td>
                                    <td>Octubre 2024</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="mt-4">Cómo Funciona el Filtro</h4>
                    <div class="alert alert-info">
                        <strong><i class="fas fa-filter"></i> Lógica de Filtrado:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Selecciona Conceptos por cobrar con <code>date_expiration</code> dentro del mes</li>
                            <li>Excluye automáticamente recargos por morosidad (<code>quota_original_id IS NULL</code>)
                            </li>
                            <li>Agrupa por representante y calcula deudas consolidadas</li>
                            <li>Muestra solo representantes con deudas pendientes > 0</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-database"></i> Fuente de Datos:</strong>
                        <ul class="mb-0">
                            <li><strong>Cuentaxpagar::list_cuentaxpagar_date()</strong></li>
                            <li>Lista generada desde la tabla <code>cuentaxpagars</code></li>
                            <li>Ordenada por fecha de vencimiento</li>
                            <li>Excluye cuotas canceladas</li>
                        </ul>
                    </div>

                    <div class="warning-box mt-3">
                        <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                        Sin seleccionar una mensualidad, no mostrará resultados. Este es el filtro principal.
                    </div>
                </div>
            </div>
        </section>

        <!-- Paso 2: Filtros Adicionales -->
        <section id="paso2" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-sliders-h text-primary"></i>
                Paso 2: Filtros Específicos
            </h2>

            <h4>Filtros Disponibles</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-credit-card"></i> Plan de Pago</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Propósito:</strong> Filtrar por plan de pago específico</p>
                            <ul>
                                <li>Opcional - muestra todos si no se selecciona</li>
                                <li>Lista generada desde <code>Planpago::list_planpago()</code></li>
                                <li>Aplica filtro en consulta principal</li>
                                <li>Útil para reportes por modalidad de pago</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-graduation-cap"></i> Grado Académico</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Propósito:</strong> Filtrar estudiantes por grado</p>
                            <ul>
                                <li>Opcional - muestra todos si no se selecciona</li>
                                <li>Lista generada desde <code>Grado::list_pestudio_grado()</code></li>
                                <li>Filtro aplicado mediante join con <code>seccions</code></li>
                                <li>Ideal para reportes por nivel educativo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Estructura de Consulta</h4>
            <div class="alert alert-warning">
                <strong><i class="fas fa-sitemap"></i> Relaciones Utilizadas:</strong>
                <div class="mt-2 small">
                    <code>representants</code> → <code>estudiants</code> → <code>administrativas</code> →
                    <code>planpagos</code> → <code>cuentaxpagars</code><br>
                    + <code>inscripcions</code> → <code>seccions</code> (para filtro por grado)
                </div>
            </div>
        </section>

        <!-- Paso 3: Análisis de Resultados -->
        <section id="paso3" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-chart-bar text-primary"></i>
                Paso 3: Interpretación de Resultados
            </h2>

            <h4>Estructura de la Tabla</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Columna</th>
                            <th>Descripción</th>
                            <th>Fórmula/Cálculo</th>
                            <th>Importancia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Representante/Estudiantes</strong></td>
                            <td>Información del representante y estudiantes asociados</td>
                            <td>Datos básicos</td>
                            <td><span class="badge badge-danger">Alta</span></td>
                        </tr>
                        <tr>
                            <td><strong>CI</strong></td>
                            <td>Cédulas de identidad</td>
                            <td>Representante + cada estudiante</td>
                            <td><span class="badge badge-info">Media</span></td>
                        </tr>
                        <tr>
                            <td><strong>Deuda</strong></td>
                            <td>Monto pendiente por concepto</td>
                            <td><code>TotalExchangeMontoCuentasXPagarAdeudado()</code></td>
                            <td><span class="badge badge-danger">Alta</span></td>
                        </tr>
                        <tr>
                            <td><strong>ABN</strong></td>
                            <td>Total de abonos del representante</td>
                            <td><code>total_abono_exchange</code></td>
                            <td><span class="badge badge-warning">Media</span></td>
                        </tr>
                        <tr>
                            <td><strong>CAF</strong></td>
                            <td>Total de créditos a favor</td>
                            <td><code>total_credito_exchange</code></td>
                            <td><span class="badge badge-warning">Media</span></td>
                        </tr>
                        <tr>
                            <td><strong>SAF</strong></td>
                            <td>Saldo a favor del representante</td>
                            <td>CAF + ABN</td>
                            <td><span class="badge badge-success">Alta</span></td>
                        </tr>
                        <tr>
                            <td><strong>D.Total</strong></td>
                            <td>Deuda total después de aplicar SAF</td>
                            <td>Deuda - SAF (si > 0)</td>
                            <td><span class="badge badge-danger">Crítica</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h4 class="mt-4">Indicadores Visuales</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-exclamation-circle"></i> Deuda Pendiente</h6>
                        </div>
                        <div class="card-body">
                            <p>Cuando <strong>D.Total > 0</strong>:</p>
                            <ul>
                                <li>Fila con borde destacado</li>
                                <li>Texto en negrita</li>
                                <li>Color rojo en montos significativos</li>
                                <li>Prioridad para seguimiento</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Sin Deuda</h6>
                        </div>
                        <div class="card-body">
                            <p>Cuando <strong>D.Total ≤ 0</strong>:</p>
                            <ul>
                                <li>Fila con fondo <code>table-danger</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h4>Métricas del Reporte</h4>
            <div class="alert alert-info">
                <strong><i class="fas fa-calculator"></i> Cálculos Automáticos:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Total General:</strong> Suma de todas las deudas pendientes</li>
                    <li><strong>Representantes Deudores:</strong> Porcentaje calculado vs total de representantes</li>
                    <li><strong>Morosidad:</strong> <code>(representantes_con_deuda / total_representantes) * 100</code>
                    </li>
                </ul>
            </div>
        </section>

        <!-- Paso 4: Exportación y Acciones -->
        <section id="paso4" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-file-export text-primary"></i>
                Paso 4: Exportación y Acciones
            </h2>

            <h4>Opciones de Exportación</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-file-excel fa-2x text-success mb-3"></i>
                            <h6>Excel</h6>
                            <p class="small">Formato .xlsx</p>
                            <span class="badge badge-success">Todos los datos</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-file-csv fa-2x text-info mb-3"></i>
                            <h6>CSV</h6>
                            <p class="small">Formato .csv</p>
                            <span class="badge badge-info">Datos estructurados</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-file-pdf fa-2x text-danger mb-3"></i>
                            <h6>PDF</h6>
                            <p class="small">Formato .pdf</p>
                            <span class="badge badge-danger">Columnas seleccionadas</span>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Configuración DataTables</h4>
            <div class="alert alert-warning">
                <strong><i class="fas fa-table"></i> Características de la Tabla:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Paginación:</strong> 10, 25, 50, 100, 500 registros o "Todos"</li>
                    <li><strong>Búsqueda:</strong> Filtrado en tiempo real</li>
                    <li><strong>Ordenamiento:</strong> Click en cabeceras de columna</li>
                    <li><strong>Responsive:</strong> Adaptable a dispositivos móviles</li>
                    <li><strong>Columnas:</strong> Visibilidad configurable</li>
                </ul>
            </div>

            <h4>Acciones por Representante</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-history"></i> Histórico de Pagos</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Función:</strong> Ver todos los pagos del representante</p>
                            <ul>
                                <li>Modal con carga asíncrona</li>
                                <li>Historial completo de transacciones</li>
                                <li>Fechas, montos y conceptos</li>
                                <li>Información detallada por estudiante</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-id-card"></i> Ficha del Estudiante</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Función:</strong> Ver información completa del estudiante</p>
                            <ul>
                                <li>Modal con datos académicos</li>
                                <li>Información personal y de contacto</li>
                                <li>Estado de inscripción</li>
                                <li>Plan de pago asignado</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Optimización del Sistema -->
        <section id="optimizacion" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-tachometer-alt text-primary"></i>
                Optimización y Rendimiento
            </h2>

            <h4>Características de Rendimiento</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-bolt"></i> Optimizaciones Implementadas</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Exclusión de recargos:</strong> <code>WHERE quota_original_id IS NULL</code>
                                </li>
                                <li><strong>Filtro por estudiantes activos:</strong>
                                    <code>status_active = 'true'</code></li>
                                <li><strong>Agrupación eficiente:</strong> <code>GROUP BY ci_representant</code></li>
                                <li><strong>Joins optimizados:</strong> Solo tablas necesarias</li>
                                <li><strong>Límite opcional:</strong> <code>limit(20)</code> para pruebas</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-database"></i> Estructura de Consulta</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>7 tablas relacionadas</strong> en joins</li>
                                <li><strong>Filtros aplicados</strong> en consulta principal</li>
                                <li><strong>Cálculos en modelo</strong> no en consulta</li>
                                <li><strong>Collections</strong> para manipulación en PHP</li>
                                <li><strong>Eager loading</strong> para relaciones</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Recomendaciones para Grandes Volúmenes</h4>
            <div class="alert alert-warning">
                <strong><i class="fas fa-server"></i> Para instituciones con muchos datos:</strong>
                <ul class="mb-0 mt-2">
                    <li>Utilizar filtros específicos (plan de pago, grado) para reducir resultados</li>
                    <li>Exportar a Excel para análisis fuera del sistema</li>
                    <li>Programar reportes en horarios de baja demanda</li>
                    <li>Considerar paginación más agresiva (50 registros por página)</li>
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
                            <button class="btn btn-link" type="button" data-toggle="collapse"
                                data-target="#answer1">
                                <i class="fas fa-question"></i> ¿Por qué no veo resultados al seleccionar una
                                mensualidad?
                            </button>
                        </h5>
                    </div>
                    <div id="answer1" class="collapse show" data-parent="#faqAccordion">
                        <div class="card-body">
                            Posibles causas:
                            <ul>
                                <li>No hay Conceptos por cobrar generadas para ese periodo</li>
                                <li>Todas las cuotas están completamente pagadas</li>
                                <li>Los representantes tienen saldo a favor que cubre las deudas</li>
                                <li>No hay estudiantes activos en el sistema</li>
                                <li>Los filtros adicionales están excluyendo todos los resultados</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="card">
                    <div class="card-header" id="faq2">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#answer2">
                                <i class="fas fa-question"></i> ¿Cómo se calcula el "Saldo a Favor" (SAF)?
                            </button>
                        </h5>
                    </div>
                    <div id="answer2" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>SAF = Total Abonos + Total Créditos a Favor</strong><br><br>
                            Donde:
                            <ul>
                                <li><strong>Total Abonos:</strong> Pagos anticipados o excedentes de pagos anteriores
                                    (<code>total_abono_exchange</code>)</li>
                                <li><strong>Total Créditos:</strong> Montos a favor por diversos conceptos
                                    (<code>total_credito_exchange</code>)</li>
                            </ul>
                            Este saldo se aplica automáticamente para reducir la deuda total.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="card">
                    <div class="card-header" id="faq3">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#answer3">
                                <i class="fas fa-question"></i> ¿Por qué algunas filas aparecen en rojo?
                            </button>
                        </h5>
                    </div>
                    <div id="answer3" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Las filas en rojo (<code>table-danger</code>) indican que el <strong>Saldo a Favor es mayor
                                o igual a la deuda</strong>, por lo que técnicamente no hay deuda pendiente. Esto puede
                            ocurrir cuando:
                            <ul>
                                <li>El representante tiene créditos suficientes para cubrir la deuda</li>
                                <li>Hay abonos disponibles que superan el monto adeudado</li>
                                <li>Combinación de ambos recursos</li>
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
                                <i class="fas fa-question"></i> ¿Puedo ver deudas de meses anteriores?
                            </button>
                        </h5>
                    </div>
                    <div id="answer4" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>Sí</strong>, seleccionando la mensualidad correspondiente en el filtro principal.
                            Se mostrará todas las deudas pendientes para ese periodo específico, independientemente
                            de cuándo se generaron originalmente.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="card">
                    <div class="card-header" id="faq5">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#answer5">
                                <i class="fas fa-question"></i> ¿Los datos se actualizan en tiempo real?
                            </button>
                        </h5>
                    </div>
                    <div id="answer5" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>Sí</strong>, los datos reflejan el estado actual al momento de generar
                            el reporte. Cualquier pago registrado, abono aplicado o crédito generado se verá reflejado
                            inmediatamente en el próximo reporte generado.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.cuentaxpagarsList') }}', '_blank')">
            <i class="fas fa-print"></i> Versión Imprimible
        </button>

    </div>
    
</div>