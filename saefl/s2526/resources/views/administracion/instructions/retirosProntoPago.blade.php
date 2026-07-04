<div class="container-fluid">
    <div class="row">
        <!-- Contenido Principal -->
        <div class="col-md-12">
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="text-primary">
                    <i class="fas fa-file-invoice-dollar mr-3"></i>
                    Guía - Retiro Administrativo con Ajuste de Pronto Pago, se presenta un caso particular
                </div>
                <p class="lead text-muted">Anulación de pago adelantado, luego registro de cuotas pendientes</p>
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Flujo:</strong> Anulación del pago de agosto
                    adelantado ($90) para liberar recursos y aplicar a cuotas pendientes.
                </div>
            </div>

            <!-- Resumen Ejecutivo -->
            <section id="resumen" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-clipboard-list text-primary"></i>
                    Resumen.
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>Situación Específica</h4>
                        <ul>
                            <li><strong>Estudiante:</strong> Con beneficio de pronto pago ($90 en lugar de $120)</li>
                            <li><strong>Pago adelantado:</strong> Agosto cancelado anticipadamente ($90)</li>
                            <li><strong>Retiro:</strong> En septiembre, sin utilizar mes de agosto pagado</li>
                            <li><strong>Estrategia:</strong> Liberar recurso de agosto para aplicar a septiembre</li>
                        </ul>

                        <h5 class="mt-4">Ajuste: $120.00</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Concepto</th>
                                        <th>Valor Real</th>
                                        <th>Pagado</th>
                                        <th>Acción Requerida</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Agosto</strong></td>
                                        <td>$120</td>
                                        <td>$90 (adelantado)</td>
                                        <td class="text-warning"><strong>ANULAR $90</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Septiembre</strong></td>
                                        <td>$120</td>
                                        <td>$0</td>
                                        <td class="text-success"><strong>PAGAR $120</strong></td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>NETO</strong></td>
                                        <td><strong>$240</strong></td>
                                        <td><strong>$90 → $120</strong></td>
                                        <td class="text-primary"><strong>+$30 DIFERENCIA</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-exchange-alt fa-3x text-primary mb-3"></i>
                                <h5>Flujo del Proceso</h5>
                                <span class="badge badge-warning feature-badge">Paso 1: Anulación</span>
                                <span class="badge badge-success feature-badge">Paso 2: Pagos</span>
                                <span class="badge badge-danger feature-badge">Paso 3: Retiro</span>
                                <span class="badge badge-secondary feature-badge">Paso 4: Ajustes*</span>

                                <div class="mt-3">
                                    <small class="text-muted">Tiempo estimado: 10-12 minutos</small>
                                    <br>
                                    <small class="text-muted">*Paso 4: Opcional según necesidad</small>
                                </div>
                            </div>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Lógica del Flujo:</strong>
                            Anular pago no utilizado (agosto) para financiar cuota pendiente (septiembre).
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 1: ANULACIÓN DEL PAGO DE AGOSTO -->
            <section id="paso1" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-undo text-warning"></i>
                    Paso 1: Anulación del Pago de Agosto ($90)
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>1.1 Fundamentación de la Anulación</h4>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-calculator"></i> Justificación:</strong>
                            El pago de agosto se realizó anticipadamente pero el estudiante se retira en septiembre,
                            por lo que no utilizó el servicio de agosto. Se libera este recurso para aplicar a la cuota
                            pendiente de septiembre.
                        </div>

                        <h4 class="mt-4">1.2 Acceso a la Sección de Anulaciones <small class="small text-muted">Ver
                                guía instruccional</small>
                        </h4>
                        <ol>
                            <li>Navegar a: <code>Anulación de Pagos</code></li>
                            <li>Buscar estudiante por nombre o cédula</li>
                            <li>Localizar pago de <strong>AGOSTO - $90</strong></li>
                            <li>Verificar: <code>Concepto: MENSUALIDAD AGOSTO</code>, <code>Monto: $90</code></li>
                        </ol>

                        <h4 class="mt-4">1.3 Procedimiento de Anulación</h4>
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-exchange-alt"></i> Flujo general para la Anulación
                                    <small class="small text-muted">Ver guía instruccional</small>
                                </h6>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Seleccionar registro de pago de <strong>agosto - $90</strong></li>
                                    <li>Click en <span class="badge badge-warning">"Anular Pago"</span></li>
                                    <li>Ingresar motivo:
                                        <div class="alert alert-light m-1 p-1">
                                            <small>
                                                <strong>Texto estándar:</strong><br>
                                                "Anulación por retiro administrativo - Pago de agosto no utilizado.
                                                Recurso liberado para aplicar a cuota pendiente de septiembre."
                                            </small>
                                        </div>
                                    </li>
                                    <li>Confirmar anulación (SweetAlert de confirmación)</li>
                                    <li>Verificar que el pago aparece como <strong>ANULADO</strong></li>
                                </ol>
                            </div>
                        </div>

                        <h4 class="mt-4">1.4 Verificación de Anulación</h4>
                        <div class="success-box">
                            <ul class="mb-0">
                                <li>✅ El registro de pago de agosto se encuentra en el listado de pagos anulados</li>
                                <li>✅ Identificación del recurso liberado <strong>$90</strong>, disponible para nuevo
                                    pago (CAF/ABN)</li>
                                <li>✅ Historial de pagos, no está el registro de pago correspondiente a la cuota agosto.
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-money-bill-wave fa-2x text-warning mb-3"></i>
                                <p class="mb-1"><strong>Anulación de Pago</strong></p>
                                <small class="text-muted">Liberar recurso de $90</small>
                                <div class="mt-3">
                                    <span class="badge badge-warning">AGOSTO</span>
                                    <span class="badge badge-info">$90</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">Impacto Financiero</h5>
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="text-info">Saldo Después de Anulación</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Saldo Anterior:</strong></td>
                                        <td>$X</td>
                                    </tr>
                                    <tr>
                                        <td><strong>+ Anulación Agosto:</strong></td>
                                        <td class="text-success">+$90</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nuevo Saldo:</strong></td>
                                        <td class="text-primary"><strong>$X + $90</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                            Verificar que la anulación se complete exitosamente antes de proceder al paso 2.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 2: REGISTRO DE PAGO DE SEPTIEMBRE -->
            <!-- PASO 2: REGISTRO DE PAGO DE SEPTIEMBRE -->
            <section id="paso2" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-cash-register text-success"></i>
                    Paso 2: Registro de Pago - Septiembre ($120)
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>2.1 Cambio de Plan de Pago <small class="small text-muted">(Requerido para este caso)</small></h4>
                        <div class="alert alert-warning">
                            <strong><i class="fas fa-exchange-alt"></i> Importante:</strong>
                            En este caso particular, es necesario cambiar el plan de pago del estudiante antes de proceder con el registro del pago.
                        </div>

                        <h5>Procedimiento de Cambio de Plan:</h5>
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-sync-alt"></i> Cambio de Plan de Pago</h6>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li><strong>Ir a listado de inscripciones administrativas</strong></li>
                                    <li><strong>Buscar al estudiante</strong> (por nombre o cédula)</li>
                                    <li><strong>Clic en el botón editar</strong> <i class="fas fa-pen fa-1x text-primary"></i></li>
                                    <li><strong>Cambiar el plan de pago</strong> al correspondiente</li>
                                    <li><strong>Clic en guardar</strong> para confirmar los cambios</li>
                                </ol>
                                
                                <div class="alert alert-info mt-3">
                                    <small>
                                        <strong>Nota:</strong> Este paso es específico para casos donde el estudiante 
                                        requiere un cambio de plan antes del retiro administrativo.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-4">2.2 Fundamentación del Pago</h4>
                        <div class="alert alert-success">
                            <strong><i class="fas fa-sync-alt"></i> Uso de Recurso Liberado:</strong>
                            El recurso de $90 liberado en el paso 1 se utiliza para cubrir parte de la cuota de
                            septiembre ($120),
                            generando una diferencia de $30 a favor de la institución.
                        </div>

                        <h4 class="mt-4">2.3 Acceso al Módulo de Registro de Pagos</h4>
                        <ol>
                            <li>Navegar al: <code>Asistente de Registro de Pagos</code></li>
                            <li>Buscar estudiante por nombre o cédula</li>
                            <li>Identificar cuota pendiente de <strong>SEPTIEMBRE - $120</strong></li>
                            <li>Verificar saldo disponible del representante (debe incluir los $90 liberados)</li>
                        </ol>

                        <h4 class="mt-4">2.4 Procedimiento de Registro</h4>
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-cash-register"></i> Flujo general de Registro
                                    <small class="small text-light">Ver guía instruccional</small>
                                </h6>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Seleccionar cuota de <strong>SEPTIEMBRE - $120</strong></li>
                                    <li>Click en <span class="badge badge-success">"Registrar Pago"</span></li>
                                    <li>Completar modal de pago:
                                        <ul>
                                            <li><strong>Monto:</strong> $120</li>
                                            <li><strong>Método de pago:</strong> "Saldo disponible"</li>
                                            <li><strong>Referencia:</strong> "Ajuste por retiro"</li>
                                        </ul>
                                    </li>
                                    <li>En observaciones incluir:
                                        <div class="alert alert-light mt-2">
                                            <small>
                                                <strong>Texto estándar:</strong><br>
                                                "Pago de septiembre con recursos liberados de anulación de agosto.
                                                Retiro administrativo - Ajuste por pronto pago no devengado."
                                            </small>
                                        </div>
                                    </li>
                                    <li>Confirmar registro de pago</li>
                                </ol>
                            </div>
                        </div>

                        <h4 class="mt-4">2.5 Análisis Financiero del Proceso</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Operación</th>
                                        <th>Monto</th>
                                        <th>Saldo Representante</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Saldo Inicial</td>
                                        <td>$X</td>
                                        <td>$X</td>
                                        <td><span class="badge badge-secondary">Inicial</span></td>
                                    </tr>
                                    <tr>
                                        <td>Anulación Agosto</td>
                                        <td class="text-success">+$90</td>
                                        <td>$X + $90</td>
                                        <td><span class="badge badge-warning">Liberado</span></td>
                                    </tr>
                                    <tr>
                                        <td>Pago Septiembre</td>
                                        <td class="text-danger">-$120</td>
                                        <td>($X + $90) - $120</td>
                                        <td><span class="badge badge-success">Aplicado</span></td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>Resultado Neto</strong></td>
                                        <td><strong>-$30</strong></td>
                                        <td><strong>$X - $30</strong></td>
                                        <td><span class="badge badge-primary">Completado</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-sync-alt fa-2x text-warning mb-3"></i>
                                <p class="mb-1"><strong>Cambio de Plan</strong></p>
                                <small class="text-muted">Requerido para este caso</small>
                                <div class="mt-3">
                                    <span class="badge badge-warning">PASO PREVIO</span>
                                    <span class="badge badge-info">EDITAR</span>
                                </div>
                            </div>
                        </div>

                        <div class="screenshot text-center mt-4">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-receipt fa-2x text-success mb-3"></i>
                                <p class="mb-1"><strong>Registro de Pago</strong></p>
                                <small class="text-muted">Septiembre - $120</small>
                                <div class="mt-3">
                                    <span class="badge badge-success">SEPTIEMBRE</span>
                                    <span class="badge badge-info">$120</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">2.6 Verificación Post-Registro</h5>
                        <div class="success-box">
                            <ul class="mb-0">
                                <li>✅ Plan de pago actualizado correctamente</li>
                                <li>✅ Cuota de septiembre muestra estado <strong>PAGADA</strong></li>
                                <li>✅ Saldo del representante actualizado correctamente</li>
                                <li>✅ Historial de pagos registra la transacción</li>
                                <li>✅ Observaciones incluyen justificación del ajuste</li>
                            </ul>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Punto Clave:</strong>
                            El pago de septiembre ($120) se realiza con el recurso liberado de agosto ($90) más $30
                            adicionales del saldo del representante.
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                            El cambio de plan de pago debe realizarse antes del registro del pago para garantizar
                            que la cuota se calcule correctamente.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 3: RETIRO ADMINISTRATIVO -->
            <section id="paso3" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-user-slash text-danger"></i>
                    Paso 3: Retiro Administrativo <small class="small text-muted">Ver guía instruccional</small>
                </h2>

                <div class="row">
                    <div class="col-md-8">
                        <h4>3.1 Condiciones para el Retiro</h4>
                        <div class="alert alert-success">
                            <strong><i class="fas fa-check-circle"></i> Pre-requisitos Completados:</strong>
                            <ul class="mb-0 mt-2">
                                <li>✅ Pago de agosto anulado ($90 liberados)</li>
                                <li>✅ Pago de septiembre registrado ($120 aplicados)</li>
                                <li>✅ Situación financiera regularizada</li>
                                <li>✅ Saldos actualizados correctamente</li>
                            </ul>
                        </div>

                        <h4 class="mt-4">3.2 Acceso a la sección Gestión de Retiros <small class="small">Ver guía
                                instruccional</small></h4>
                        <ol>
                            <li>Navegar a: <code>Gestión de Retiros Estudiantiles</code></li>
                            <li>Buscar estudiante por nombre o cédula</li>
                            <li>Verificar que deuda pendiente tiene el monto correspondiente.</li>
                            <li>Confirmar que todas las cuotas están en estado correcto</li>
                        </ol>

                        <h4 class="mt-4">3.3 Proceso de Retiro Administrativo</h4>
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-list-ol"></i> Flujo en el Sistema</h6>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Click en botón <span class="badge badge-danger">ROJO "Retirar
                                            Estudiante"</span></li>
                                    <li><strong>Modal de observaciones obligatorias</strong> aparece</li>
                                    <li>Ingresar justificación detallada (mínimo 10 caracteres)</li>
                                    <li>Botón se habilita automáticamente al validar</li>
                                    <li>Confirmar retiro administrativo</li>
                                </ol>
                            </div>
                        </div>

                        <h4 class="mt-4">3.4 Generación Automática de Deuda</h4>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-file-invoice-dollar"></i> Nuevo Comportamiento:</strong>
                            El sistema genera automáticamente un concepto de cobro tipo <strong>INDIVIDUAL</strong>
                            con la deuda actual del representante al momento del retiro.
                        </div>

                        <h5>Características del Concepto Generado:</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Campo</th>
                                        <th>Valor Automático</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Tipo de Cuenta</strong></td>
                                        <td><span class="badge badge-info">INDIVIDUAL</span></td>
                                        <td>Generado automáticamente por el sistema</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Concepto de Pago</strong></td>
                                        <td><code>RETIRO ADMINISTRATIVO</code></td>
                                        <td>Deuda base del representante</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Monto</strong></td>
                                        <td><strong>Deuda Actual</strong></td>
                                        <td>Calculado automáticamente al momento del retiro</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Descripción</strong></td>
                                        <td><code>DEUDA POR RETIRO ADMIN [CI ESTUDIANTE]</code></td>
                                        <td>Incluye cédula del estudiante</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h4 class="mt-4">3.5 Observaciones Obligatorias - Texto Estándar</h4>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-comment"></i> Texto para campo de observaciones:</strong>
                            <div class="mt-2 p-2 bg-light border rounded">
                                <small>
                                    "RETIRO ADMINISTRATIVO - Proceso de ajuste por pronto pago completado.<br><br>
                                    RESUMEN DE OPERACIONES:<br>
                                    • Anulación pago agosto: $90 (no utilizado)<br>
                                    • Pago cuota septiembre: $120<br>
                                    • Ajuste neto: +$30 a favor institución<br><br>
                                    Estudiante no completó los 13 pagos requeridos para mantener beneficio de pronto
                                    pago.
                                    Proceso financiero regularizado antes del retiro."
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-user-times fa-2x text-danger mb-3"></i>
                                <p class="mb-1"><strong>Retiro Administrativo</strong></p>
                                <small class="text-muted">Con generación automática de deuda</small>
                                <div class="mt-3">
                                    <span class="badge badge-danger">10-500 chars</span>
                                    <span class="badge badge-success">Deuda Generada</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">3.6 Verificación Post-Retiro</h5>
                        <div class="success-box">
                            <ul class="mb-0">
                                <li>✅ Badge "R.Administrativo" cambia a <strong>SI</strong> (color rojo)</li>
                                <li>✅ Botón de retiro cambia a <strong>gris deshabilitado</strong></li>
                                <li>✅ Sistema genera <strong>concepto de cobro automático</strong></li>
                                <li>✅ Deuda actual registrada como concepto <strong>INDIVIDUAL</strong></li>
                                <li>✅ Observaciones guardadas permanentemente</li>
                                <li>✅ Estudiante muestra estado <strong>RETIRADO</strong></li>
                            </ul>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Importante:</strong>
                            La deuda generada automáticamente en este paso representa la situación financiera
                            actual del representante. Para ajustes adicionales, usar el Paso 4 (opcional).
                        </div>

                        <div class="warning-box mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Verificación Final:</strong>
                            Confirmar que el retiro se procesa exitosamente y que el concepto de deuda
                            se genera correctamente.
                        </div>
                    </div>
                </div>
            </section>

            <!-- PASO 4: AJUSTES ADICIONALES OPCIONALES -->
            <section id="paso4" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-adjust text-secondary"></i>
                    Paso 4: Ajustes Adicionales <small class="text-muted">(Opcional)</small>
                </h2>

                <div class="alert alert-secondary">
                    <strong><i class="fas fa-info-circle"></i> Propósito de este paso:</strong>
                    Este paso es <strong>opcional</strong> y solo debe usarse cuando se requieran ajustes
                    adicionales a la deuda del representante, fuera de la deuda base generada automáticamente
                    en el Paso 3.
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <h4>4.1 Casos de Uso para Ajustes Adicionales</h4>
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-scenarios"></i> Escenarios que requieren Paso 4
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li><strong>Modificación de montos:</strong> Ajustes manuales a la deuda base</li>
                                    <li><strong>Cargos adicionales:</strong> Multas, recargos o conceptos extra</li>
                                    <li><strong>Descuentos especiales:</strong> Ajustes por convenios o situaciones
                                        especiales</li>
                                    <li><strong>Corrección de errores:</strong> Rectificación de cálculos automáticos
                                    </li>
                                    <li><strong>Documentación adicional:</strong> Conceptos de cierre documental</li>
                                </ul>
                            </div>
                        </div>

                        <h4 class="mt-4">4.2 Cuándo NO usar el Paso 4</h4>
                        <div class="alert alert-warning">
                            <strong><i class="fas fa-exclamation-triangle"></i> No es necesario si:</strong>
                            <ul class="mb-0 mt-2">
                                <li>✅ La deuda generada automáticamente en el Paso 3 es correcta</li>
                                <li>✅ No hay ajustes manuales requeridos</li>
                                <li>✅ No existen cargos adicionales fuera del cálculo automático</li>
                                <li>✅ La situación financiera está completamente regularizada</li>
                            </ul>
                        </div>

                        <h4 class="mt-4">4.3 Procedimiento para Ajustes Adicionales</h4>
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-receipt"></i> Creación Manual de Conceptos</h6>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Navegar a: <code>Conceptos de Cobro</code></li>
                                    <li>Click en <span class="badge badge-primary">"Nueva Cuenta"</span></li>
                                    <li>Configurar con los siguientes datos:</li>
                                </ol>

                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="30%">Campo</th>
                                                <th width="70%">Valor Recomendado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Tipo de Cuenta</strong></td>
                                                <td><span class="badge badge-info">INDIVIDUAL</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Concepto de Pago</strong></td>
                                                <td><code>AJUSTE ADMINISTRATIVO</code></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Descripción</strong></td>
                                                <td><code>AJUSTE - RETIRO ADMIN [CI ESTUDIANTE]</code></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Monto</strong></td>
                                                <td><strong>Valor del ajuste requerido</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <h5 class="mt-3">Observaciones del Ajuste</h5>
                                <div class="alert alert-light">
                                    <strong>Texto para observaciones:</strong>
                                    <div class="mt-2 p-2 bg-light border rounded">
                                        <small>
                                            "AJUSTE ADICIONAL - RETIRO ADMINISTRATIVO<br><br>
                                            JUSTIFICACIÓN DEL AJUSTE:<br>
                                            • [Describir razón específica del ajuste]<br>
                                            • [Indicar monto y concepto]<br>
                                            • [Relacionar con retiro del estudiante]<br><br>
                                            FECHA AJUSTE: [Fecha] - USUARIO: [Usuario]"
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-tools fa-2x text-secondary mb-3"></i>
                                <p class="mb-1"><strong>Ajustes Adicionales</strong></p>
                                <small class="text-muted">Solo si es necesario</small>
                                <div class="mt-3">
                                    <span class="badge badge-secondary">OPCIONAL</span>
                                    <span class="badge badge-warning">AJUSTES</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">4.4 Diferenciación de Conceptos</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Concepto</th>
                                        <th>Origen</th>
                                        <th>Propósito</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>RETIRO ADMINISTRATIVO</strong></td>
                                        <td><span class="badge badge-success">Automático</span></td>
                                        <td>Deuda base del representante</td>
                                    </tr>
                                    <tr>
                                        <td><strong>AJUSTE ADMINISTRATIVO</strong></td>
                                        <td><span class="badge badge-warning">Manual</span></td>
                                        <td>Modificaciones adicionales</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="success-box mt-3">
                            <strong><i class="fas fa-check-circle"></i> Verificación Final:</strong>
                            <ul class="mb-0">
                                <li>✅ Deuda base generada en Paso 3</li>
                                <li>✅ Ajustes adicionales (si aplica) en Paso 4</li>
                                <li>✅ Documentación completa del proceso</li>
                                <li>✅ Situación financiera regularizada</li>
                            </ul>
                        </div>

                        <div class="tip-box mt-3">
                            <strong><i class="fas fa-lightbulb"></i> Buenas Prácticas:</strong>
                            Documentar claramente la razón de cada ajuste adicional para mantener
                            la trazabilidad del proceso completo.
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
                                            <td><strong>Pagos</strong></td>
                                            <td><span class="badge badge-success">REGULARIZADOS</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Retiro Administrativo</strong></td>
                                            <td><span class="badge badge-danger">PROCESADO</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Deuda Base</strong></td>
                                            <td><span class="badge badge-info">GENERADA</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ajustes Adicionales</strong></td>
                                            <td><span class="badge badge-secondary">OPCIONAL</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estudiante</strong></td>
                                            <td><span class="badge badge-dark">RETIRADO</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Documentación Generada</h5>
                                <ul>
                                    <li>Registro de anulación de pago</li>
                                    <li>Registro de pago de cuota pendiente</li>
                                    <li>Registro de retiro con observaciones</li>
                                    <li>Concepto de deuda automático (Paso 3)</li>
                                    <li>Conceptos de ajuste (Paso 4, si aplica)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <hr>

            <button type="button" class="btn btn-success"
                onclick="window.open('{{ route('helpers.pdf.retiros.pronto.pago') }}', '_blank')">
                <i class="fas fa-print"></i> Versión Imprimible
            </button>

            <hr>

            <table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#343a40">
                <tr>
                    <td align="center">
                        <font size="2" color="#ffffff" face="Arial">
                            Guía de Retiro Administrativo con Ajuste de Pronto Pago - Versión 1.1
                        </font>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>
