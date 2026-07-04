<div class="row"> <!-- Contenido Principal -->
    <div class="col-md-12"> <!-- Introducción -->
        <section id="introduccion" class="guide-section">
            <h2 class="mb-4"> <i class="fas fa-user-graduate text-primary"></i> Asistente: Inscripción Administrativa
                Extemporánea & Recargos </h2>

            <div class="row">
                <div class="col-md-8">
                    <p class="lead">
                        El <strong>Asistente de Inscripción Administrativa Extemporánea</strong> es una herramienta
                        especializada para gestionar inscripciones fuera del período regular y aplicar recargos por
                        morosidad de manera controlada.
                    </p>

                    <div class="tip-box">
                        <strong><i class="fas fa-lightbulb"></i> Propósito:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Gestión de inscripciones administrativas extemporáneas</li>
                            <li>Aplicación automatizada de recargos por morosidad</li>
                            <li>Validación de estados académico y financiero</li>
                            <li>Procesamiento seguro con transacciones de base de datos</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-check fa-3x text-success mb-3"></i>
                            <h5>Características Principales</h5>
                            <span class="badge badge-primary feature-badge">Wizard de 4 Pasos</span>
                            <span class="badge badge-success feature-badge">Cálculo Automático de Recargos</span>
                            <span class="badge badge-info feature-badge">Validación en Tiempo Real</span>
                            <span class="badge badge-warning feature-badge">Transacciones Seguras</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Flujo del Proceso -->
        <section id="flujo" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-project-diagram text-warning"></i>
                Flujo del Proceso
            </h2>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-sitemap"></i> Diagrama del Wizard</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="step-card">
                                        <div class="step-number">1</div>
                                        <div class="text-center mt-2">
                                            <strong>Búsqueda</strong>
                                            <div class="small text-muted">Validar estudiante</div>
                                        </div>
                                    </div>
                                    <div class="arrow">→</div>
                                    <div class="step-card">
                                        <div class="step-number">2</div>
                                        <div class="text-center mt-2">
                                            <strong>Plan de Pago</strong>
                                            <div class="small text-muted">Seleccionar plan</div>
                                        </div>
                                    </div>
                                    <div class="arrow">→</div>
                                    <div class="step-card">
                                        <div class="step-number">3</div>
                                        <div class="text-center mt-2">
                                            <strong>Cuotas</strong>
                                            <div class="small text-muted">Aplicar recargos</div>
                                        </div>
                                    </div>
                                    <div class="arrow">→</div>
                                    <div class="step-card">
                                        <div class="step-number">4</div>
                                        <div class="text-center mt-2">
                                            <strong>Resumen</strong>
                                            <div class="small text-muted">Confirmar y guardar</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Paso 1: Búsqueda de Estudiantes -->
        <section id="paso1" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-search text-primary"></i>
                Paso 1: Búsqueda y Validación de Estudiantes
            </h2>

            <h4>Búsqueda Inteligente</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="30%">Campo de Búsqueda</th>
                                    <th width="45%">Descripción</th>
                                    <th width="25%">Ejemplo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Nombre</strong></td>
                                    <td>Búsqueda por nombre del estudiante</td>
                                    <td>María</td>
                                </tr>
                                <tr>
                                    <td><strong>Apellido</strong></td>
                                    <td>Búsqueda por apellido del estudiante</td>
                                    <td>González</td>
                                </tr>
                                <tr>
                                    <td><strong>Cédula</strong></td>
                                    <td>Búsqueda por cédula de identidad</td>
                                    <td>V12345678</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-info-circle"></i> Características:</strong>
                        <ul class="mb-0">
                            <li><strong>Debounce automático:</strong> 400ms de espera</li>
                            <li><strong>Mínimo 2 caracteres</strong> para activar búsqueda</li>
                            <li><strong>Límite de 12 resultados</strong> por eficiencia</li>
                            <li><strong>Búsqueda en tiempo real</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Estados del Estudiante</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Estados Positivos</h6>
                        </div>
                        <div class="card-body">
                            <span class="badge badge-success mb-2">Académico</span>
                            <small class="d-block">Inscrito académicamente</small>

                            <span class="badge badge-success mb-2">Solvente</span>
                            <small class="d-block">Sin deudas vencidas</small>

                            <span class="badge badge-secondary mb-2">Administrativo (no afecta)</span>
                            <small class="d-block">Inscrito pero no afecta proceso</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Estados que Bloquean</h6>
                        </div>
                        <div class="card-body">
                            <span class="badge badge-danger mb-2">No solvente</span>
                            <small class="d-block">Con deudas vencidas > 0</small>

                            <span class="badge badge-info mb-2">Administrativo (afecta)</span>
                            <small class="d-block">Ya inscrito - proceso bloqueado</small>

                            <span class="badge badge-warning mb-2">No administrativo</span>
                            <small class="d-block">Puede continuar el proceso</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="warning-box mt-3">
                <strong><i class="fas fa-exclamation-triangle"></i> Validación Crítica:</strong>
                El sistema <strong>bloquea automáticamente</strong> a estudiantes con deudas vencidas (no solventes) o
                que ya tienen inscripción administrativa que afecta el proceso.
            </div>
        </section>

        <!-- Paso 2: Selección de Plan de Pago -->
        <section id="paso2" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-file-invoice-dollar text-primary"></i>
                Paso 2: Selección de Plan de Pago
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-list"></i> Planes Disponibles</h6>
                        </div>
                        <div class="card-body">
                            <p>Los planes de pago se cargan automáticamente desde el sistema y muestran:</p>
                            <ul>
                                <li><strong>Nombre del plan</strong> identificador</li>
                                <li><strong>Cuotas asociadas</strong> al plan seleccionado</li>
                                <li><strong>Vista previa</strong> de todas las cuotas del plan</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tip-box">
                        <strong><i class="fas fa-database"></i> Fuente de Datos:</strong>
                        Los planes se obtienen mediante <code>Planpago::list_planpago_inscription()</code> que
                        proporciona la lista de planes disponibles para inscripción.
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Visualización de Cuotas</h4>
            <div class="alert alert-info">
                <strong><i class="fas fa-eye"></i> Vista Previa:</strong>
                Al seleccionar un plan, el sistema muestra inmediatamente <strong>todas las cuotas definidas</strong>
                para ese plan, permitiendo una revisión completa antes de continuar.
            </div>
        </section>

        <!-- Paso 3: Gestión de Recargos por Morosidad -->
        <section id="paso3" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-calculator text-primary"></i>
                Paso 3: Selección de Cuotas y Cálculo de Recargos
            </h2>

            <h4>Filtro Automático de Cuotas</h4>
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Tipo de Cuota</th>
                                    <th>Estado</th>
                                    <th>Habilitada para Recargo</th>
                                    <th>Acción Disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Vencida</strong></td>
                                    <td><span class="badge badge-danger">VENC</span></td>
                                    <td><span class="badge badge-success">SÍ</span></td>
                                    <td>Checkbox activo</td>
                                </tr>
                                <tr>
                                    <td><strong>Activa</strong></td>
                                    <td><span class="badge badge-secondary">OK</span></td>
                                    <td><span class="badge badge-secondary">NO</span></td>
                                    <td>Solo visualización</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <strong><i class="fas fa-filter"></i> Criterios de Filtro:</strong>
                        Solo se muestran cuotas con:
                        <ul class="mb-0 mt-2">
                            <li><code>type = 'GENERAL'</code></li>
                            <li><code>status_late_payment = false</code></li>
                            <li><code>enable_late_payment = true</code></li>
                        </ul>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Algoritmo de Cálculo de Recargos</h4>
            <div class="currency-box">
                <h5 class="text-center">Fórmula de Recargo por Morosidad</h5>
                <div class="text-center display-4 text-success">
                    1% × meses de mora
                </div>
                <div class="text-center text-muted">
                    (Máximo 12 meses - 12% de recargo)
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Cálculo de Meses de Mora</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Fecha base:</strong> <code>date_late_payment</code> o
                                    <code>date_expiration</code></li>
                                <li><strong>Método:</strong> Diferencia en meses desde fecha base hasta hoy</li>
                                <li><strong>Mínimo:</strong> 1 mes si está vencida</li>
                                <li><strong>Máximo:</strong> 12 meses</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-calculator"></i> Ejemplo Práctico</h6>
                        </div>
                        <div class="card-body">
                            <strong>Cuota:</strong> Matrícula - USD 100<br>
                            <strong>Meses de mora:</strong> 5 meses<br>
                            <strong>Recargo:</strong> 5% = USD 5.00<br>
                            <strong>Total a pagar:</strong> USD 105.00
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Paso 4: Resumen y Confirmación -->
        <section id="paso4" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-clipboard-check text-primary"></i>
                Paso 4: Resumen y Confirmación
            </h2>

            <h4>Estructura del Resumen</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-list-ol"></i> Secciones del Resumen</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-user-graduate"></i> Datos del Estudiante</h6>
                                    <ul>
                                        <li>Nombre completo</li>
                                        <li>Cédula de identidad</li>
                                        <li>ID del estudiante</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-file-invoice-dollar"></i> Plan Seleccionado</h6>
                                    <ul>
                                        <li>Nombre del plan</li>
                                        <li>Total de cuotas</li>
                                        <li>Cuotas con recargo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Desglose de Recargos</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Concepto</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Importancia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Cuotas con Recargo</strong></td>
                            <td class="text-center">Tabla detallada con montos originales, meses de mora, recargos y
                                totales</td>
                            <td class="text-center"><span class="badge badge-danger">Alta</span></td>
                        </tr>
                        <tr>
                            <td><strong>Todas las Cuotas del Plan</strong></td>
                            <td class="text-center">Lista completa con estados y aplicación de recargos</td>
                            <td class="text-center"><span class="badge badge-warning">Media</span></td>
                        </tr>
                        <tr>
                            <td><strong>Totales Consolidados</strong></td>
                            <td class="text-center">Sumatorias de montos originales, recargos y total general</td>
                            <td class="text-center"><span class="badge badge-danger">Alta</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="success-box mt-3">
                <strong><i class="fas fa-database"></i> Proceso de Guardado:</strong>
                Al confirmar, el sistema ejecuta una <strong>transacción de base de datos</strong> que garantiza la
                atomicidad de las operaciones: o se guarda todo correctamente o se revierten todos los cambios.
            </div>
        </section>

        <!-- Proceso Técnico de Guardado -->
        <section id="tecnico" class="guide-section">
            <h2 class="mb-4">
                <i class="fas fa-cogs text-primary"></i>
                Proceso Técnico de Guardado
            </h2>

            <h4>Transacción de Base de Datos</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-database"></i> Flujo de Operaciones en BD</h6>
                        </div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Inicio de Transacción</strong> - <code>DB::beginTransaction()</code></li>
                                <li><strong>Crear/Actualizar Inscripción Administrativa</strong> -
                                    <code>Administrativa::updateOrCreate()</code></li>
                                <li><strong>Para cada cuota seleccionada:</strong>
                                    <ul>
                                        <li>Buscar plantilla de cuota original</li>
                                        <li>Calcular recargo (1% mensual, máximo 12%)</li>
                                        <li>Crear nueva cuota individual con sufijo "RMA1"</li>
                                        <li>Crear concepto de pago con el recargo calculado</li>
                                    </ul>
                                </li>
                                <li><strong>Confirmar Transacción</strong> - <code>DB::commit()</code></li>
                                <li><strong>Notificación de Éxito</strong> - SweetAlert</li>
                            </ol>
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
                            <button class="btn btn-link" type="button" data-toggle="collapse"
                                data-target="#answer1">
                                <i class="fas fa-question"></i> ¿Por qué un estudiante aparece como "No disponible"
                                para selección?
                            </button>
                        </h5>
                    </div>
                    <div id="answer1" class="collapse show" data-parent="#faqAccordion">
                        <div class="card-body">
                            Un estudiante puede no estar disponible por dos razones principales:
                            <ul>
                                <li><strong>No es solvente:</strong> Tiene deudas vencidas mayores a 0</li>
                                <li><strong>Ya está inscrito administrativamente:</strong> Tiene una inscripción que
                                    afecta el proceso actual</li>
                            </ul>
                            El sistema muestra mensajes específicos para cada caso.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="card">
                    <div class="card-header" id="faq2">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#answer2">
                                <i class="fas fa-question"></i> ¿Cómo se calcula exactamente el recargo por morosidad?
                            </button>
                        </h5>
                    </div>
                    <div id="answer2" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            El cálculo sigue esta fórmula:
                            <ol>
                                <li><strong>Meses de mora:</strong> Diferencia en meses entre la fecha de inicio de mora
                                    y hoy</li>
                                <li><strong>Límite máximo:</strong> 12 meses (12% de recargo)</li>
                                <li><strong>Fórmula:</strong> <code>Recargo = MIN(meses, 12) × (monto_original ×
                                        0.01)</code></li>
                                <li><strong>Total:</strong> <code>Monto_original + Recargo</code></li>
                            </ol>
                            Ejemplo: 5 meses de mora en cuota de USD 100 = USD 5.00 de recargo.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="card">
                    <div class="card-header" id="faq3">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#answer3">
                                <i class="fas fa-question"></i> ¿Puedo proceder sin seleccionar cuotas con recargo?
                            </button>
                        </h5>
                    </div>
                    <div id="answer3" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>Sí</strong>, es posible. Si no selecciona ninguna cuota con recargo en el Paso 3, el
                            sistema mostrará una advertencia pero permitirá continuar.
                            En este caso, solo se creará la inscripción administrativa sin aplicar recargos por
                            morosidad.
                        </div>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="card">
                    <div class="card-header" id="faq4">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#answer4">
                                <i class="fas fa-question"></i> ¿Qué garantías de seguridad tiene el proceso de
                                guardado?
                            </button>
                        </h5>
                    </div>
                    <div id="answer4" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            El proceso incluye múltiples capas de seguridad:
                            <ul>
                                <li><strong>Transacciones de BD:</strong> Atomicidad - o todo se guarda o nada se guarda
                                </li>
                                <li><strong>Validaciones en cada paso:</strong> Verificación de estados y permisos</li>
                                <li><strong>Manejo de excepciones:</strong> Try-catch con rollback automático</li>
                                <li><strong>Auditoría:</strong> Registro de usuario que realiza la operación</li>
                                <li><strong>Confirmación explícita:</strong> SweetAlert de confirmación antes de guardar
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="card">
                    <div class="card-header" id="faq5">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                data-target="#answer5">
                                <i class="fas fa-question"></i> ¿Qué sucede si hay un error durante el guardado?
                            </button>
                        </h5>
                    </div>
                    <div id="answer5" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            En caso de error:
                            <ol>
                                <li>Se ejecuta <code>DB::rollBack()</code> automáticamente</li>
                                <li>Se revierten TODOS los cambios realizados durante la transacción</li>
                                <li>Se muestra un mensaje de error específico al usuario</li>
                                <li>El sistema mantiene los datos en el estado anterior al proceso</li>
                                <li>El usuario puede intentar nuevamente o contactar soporte</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Botón de Impresión -->
        <div class="text-center mt-4">
            <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.wizard.administrativas') }}', '_blank')">
                <i class="fas fa-print"></i> Versión Imprimible
            </button>
        </div>

        <hr>

        <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
            <tr>
                <td align="center">
                    <font size="2" color="#ffffff" face="Arial">
                        Guía del Asistente de Inscripción Administrativa Extemporánea & Recargos - SAEFL - Versión 1.0
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
            padding: 1rem;
            text-align: center;
        }

        .step-card:hover {
            transform: translateY(-5px);
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5rem;
            margin: 0 auto;
        }

        .arrow {
            font-size: 2rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            padding: 0 1rem;
        }

        .currency-box {
            background: #e8f4fd;
            border: 2px solid #17a2b8;
            padding: 2rem;
            margin: 1rem 0;
            border-radius: 8px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .arrow {
                display: none;
            }

            .step-card {
                margin-bottom: 1rem;
            }
        }
    </style>
@endsection
