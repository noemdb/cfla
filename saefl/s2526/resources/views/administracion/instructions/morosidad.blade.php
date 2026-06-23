<div>
    <div class="container-fluid py-2">
        <!-- Header -->
        <div class="row mb-2">
            <div class="col-12">
                <div class="card alert-secondary">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <h1 class="h3 mb-1">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Sistema de Recargos por Morosidad
                                </h1>
                                <p class="mb-0 opacity-75">Guía completa para la gestión y aplicación de recargos por morosidad</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- Contenido Principal -->
            <div class="col-md-12">
                <!-- Introducción -->
                <section id="introduccion" class="guide-section">
                    <h2 class="h4 mb-3">
                        <i class="fas fa-home text-primary mr-2"></i>
                        Introducción al Sistema
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <p class="lead mb-3">
                                El <strong>Sistema de Recargos por Morosidad</strong> permite aplicar cargos automáticos a cuotas vencidas mediante un proceso asistido y validado.
                            </p>
                            
                            <div class="info-box p-3 mb-3 rounded">
                                <strong><i class="fas fa-info-circle mr-2"></i>Propósito del Sistema:</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    <li>Aplicar recargos automáticos por morosidad</li>
                                    <li>Gestionar cuotas vencidas de estudiantes</li>
                                    <li>Calcular montos basados en tiempo de mora</li>
                                    <li>Generar nuevas cuotas de recargo</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card feature-card">
                                <div class="card-body text-center py-3">
                                    <i class="fas fa-calculator fa-2x text-primary mb-2"></i>
                                    <h6 class="mb-2">Características</h6>
                                    <span class="badge badge-primary badge-pill mb-1">Búsqueda en Tiempo Real</span>
                                    <span class="badge badge-success badge-pill mb-1">Cálculo Automático</span>
                                    <span class="badge badge-info badge-pill mb-1">Validación Integrada</span>
                                    <span class="badge badge-warning badge-pill">Historial de Recargos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Flujo del Proceso -->
                <section id="flujo-proceso" class="guide-section">
                    <h2 class="h4 mb-3">
                        <i class="fas fa-project-diagram text-primary mr-2"></i>
                        Flujo del Proceso - 4 Pasos
                    </h2>
                    
                    <div class="step-indicator-guide">
                        <div class="step-guide completed">
                            <div class="step-circle-guide">1</div>
                            <div class="step-label small">Búsqueda</div>
                        </div>
                        <div class="step-guide active">
                            <div class="step-circle-guide">2</div>
                            <div class="step-label small">Selección</div>
                        </div>
                        <div class="step-guide">
                            <div class="step-circle-guide">3</div>
                            <div class="step-label small">Cálculo</div>
                        </div>
                        <div class="step-guide">
                            <div class="step-circle-guide">4</div>
                            <div class="step-label small">Confirmación</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center h-100">
                                <div class="card-body py-3">
                                    <div class="step-circle-guide bg-primary text-white mx-auto mb-2">1</div>
                                    <h6 class="mb-2">Búsqueda</h6>
                                    <p class="small text-muted mb-0">Localizar estudiante por nombre o cédula</p>
                                    <span class="badge badge-light text-dark mt-2">30 seg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center h-100">
                                <div class="card-body py-3">
                                    <div class="step-circle-guide bg-primary text-white mx-auto mb-2">2</div>
                                    <h6 class="mb-2">Selección</h6>
                                    <p class="small text-muted mb-0">Elegir cuota vencida para recargo</p>
                                    <span class="badge badge-light text-dark mt-2">15 seg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center h-100">
                                <div class="card-body py-3">
                                    <div class="step-circle-guide bg-primary text-white mx-auto mb-2">3</div>
                                    <h6 class="mb-2">Cálculo</h6>
                                    <p class="small text-muted mb-0">Sistema calcula recargo automático</p>
                                    <span class="badge badge-light text-dark mt-2">5 seg</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center h-100">
                                <div class="card-body py-3">
                                    <div class="step-circle-guide bg-primary text-white mx-auto mb-2">4</div>
                                    <h6 class="mb-2">Confirmación</h6>
                                    <p class="small text-muted mb-0">Aplicar recargo y generar cuota</p>
                                    <span class="badge badge-light text-dark mt-2">10 seg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 1: Búsqueda -->
                <section id="paso1" class="guide-section">
                    <h2 class="h4 mb-3">
                        <i class="fas fa-search text-primary mr-2"></i>
                        Paso 1: Búsqueda de Estudiantes
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Procedimiento de Búsqueda</h5>
                            <ol class="pl-3">
                                <li class="mb-2">
                                    <strong>Acceder al campo de búsqueda</strong> - Ubique el campo con placeholder "Buscar estudiante..."
                                </li>
                                <li class="mb-2">
                                    <strong>Ingresar criterios</strong> - Escriba mínimo 3 caracteres:
                                    <ul class="mt-1">
                                        <li>Nombre del estudiante</li>
                                        <li>Apellido del estudiante</li>
                                        <li>Cédula de identidad</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Seleccionar de resultados</strong> - Click en estudiante de la lista desplegable
                                </li>
                                <li class="mb-2">
                                    <strong>Confirmar selección</strong> - Sistema carga automáticamente cuotas vencidas
                                </li>
                            </ol>
                            
                            <div class="info-box p-3 rounded mt-3">
                                <strong><i class="fas fa-lightbulb mr-2"></i>Consejo:</strong> 
                                Use la cédula para búsquedas más precisas y rápidas.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="screenshot-placeholder">
                                <i class="fas fa-search fa-3x mb-3"></i>
                                <p class="mb-1"><strong>Interfaz de Búsqueda</strong></p>
                                <small>Campo de texto con resultados en tiempo real</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="warning-box p-3 rounded mt-3">
                        <strong><i class="fas fa-exclamation-triangle mr-2"></i>Importante:</strong> 
                        Debe escribir mínimo <strong>3 caracteres</strong> para activar la búsqueda automática.
                    </div>
                </section>

                <!-- Paso 2: Selección de Cuotas -->
                <section id="paso2" class="guide-section">
                    <h2 class="h4 mb-3">
                        <i class="fas fa-list text-primary mr-2"></i>
                        Paso 2: Selección de Cuotas Vencidas
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Lista de Cuotas Disponibles</h5>
                            <p>Al seleccionar un estudiante, el sistema muestra automáticamente:</p>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter mr-2"></i>
                                        Cuotas Filtradas
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <ul class="mb-0">
                                        <li>Cuotas con fecha de expiración vencida</li>
                                        <li>Cuotas no canceladas completamente</li>
                                        <li>Cuotas con saldo pendiente</li>
                                        <li>Excluye cuotas ya pagadas</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <h6 class="mt-4">Acción Requerida</h6>
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-mouse-pointer fa-2x text-primary mr-3"></i>
                                <div>
                                    <strong class="d-block">Click en cuota deseada</strong>
                                    <small class="text-muted">Seleccione la cuota a la que aplicará el recargo</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="screenshot-placeholder">
                                <i class="fas fa-file-invoice-dollar fa-3x mb-3"></i>
                                <p class="mb-1"><strong>Lista de Cuotas</strong></p>
                                <small>Tarjetas con información de cuotas vencidas</small>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-body py-3">
                                    <h6 class="mb-2">
                                        <i class="fas fa-info-circle text-info mr-2"></i>
                                        Información Mostrada
                                    </h6>
                                    <ul class="small pl-3 mb-0">
                                        <li>Nombre de la cuota</li>
                                        <li>Fecha de vencimiento</li>
                                        <li>Monto pendiente</li>
                                        <li>Estado de pago</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 3: Cálculo de Recargo -->
                <section id="paso3" class="guide-section">
                    <h2 class="h4 mb-3">
                        <i class="fas fa-calculator text-primary mr-2"></i>
                        Paso 3: Cálculo Automático de Recargo
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Fórmula de Cálculo</h5>
                            <div class="code-block mb-3">
                                Recargo = Monto Original × 1% × Meses de Mora
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Parámetro</th>
                                            <th>Descripción</th>
                                            <th>Ejemplo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Monto Original</strong></td>
                                            <td>Saldo pendiente de la cuota</td>
                                            <td>$100.00</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tasa Recargo</strong></td>
                                            <td>1% mensual fijo</td>
                                            <td>1%</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Meses de Mora</strong></td>
                                            <td>Diferencia en meses desde vencimiento</td>
                                            <td>3 meses</td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong>Recargo Total</strong></td>
                                            <td>Resultado del cálculo</td>
                                            <td>$3.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <h6 class="mt-4">Límites Aplicados</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body py-2 text-center">
                                            <i class="fas fa-calendar-alt text-primary mb-2"></i>
                                            <h6 class="mb-1">Meses Máximos</h6>
                                            <strong class="text-primary">12</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body py-2 text-center">
                                            <i class="fas fa-percentage text-primary mb-2"></i>
                                            <h6 class="mb-1">Tasa Máxima</h6>
                                            <strong class="text-primary">12%</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="screenshot-placeholder">
                                <i class="fas fa-calculator fa-3x mb-3"></i>
                                <p class="mb-1"><strong>Panel de Cálculo</strong></p>
                                <small>Visualización de montos y meses</small>
                            </div>
                            
                            <div class="info-box p-3 rounded mt-3">
                                <strong><i class="fas fa-calculator mr-2"></i>Ejemplo Práctico:</strong>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Monto:</span>
                                        <strong>$100.00</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Meses:</span>
                                        <strong>3</strong>
                                    </div>
                                    <hr class="my-1">
                                    <div class="d-flex justify-content-between">
                                        <span>Recargo:</span>
                                        <strong class="text-success">$3.00</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Paso 4: Confirmación -->
                <section id="paso4" class="guide-section">
                    <h2 class="h4 mb-3">
                        <i class="fas fa-check-circle text-primary mr-2"></i>
                        Paso 4: Confirmación y Aplicación
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">Proceso de Confirmación</h5>
                            
                            <div class="card border-danger mb-3">
                                <div class="card-header bg-danger text-white py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Confirmación Requerida
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <p class="mb-3">Antes de proceder, verifique:</p>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Estudiante correcto seleccionado</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Cuota vencida apropiada</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Cálculo de recargo verificado</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Meses de mora correctos</label>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mt-4">Acción Final</h6>
                            <div class="d-flex align-items-center p-3 bg-success text-white rounded">
                                <i class="fas fa-play-circle fa-2x mr-3"></i>
                                <div>
                                    <strong class="d-block">Click en "Generar Recargo"</strong>
                                    <small>El sistema aplicará los cambios permanentemente</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="screenshot-placeholder">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <p class="mb-1"><strong>Panel de Confirmación</strong></p>
                                <small>Resumen final antes de aplicar</small>
                            </div>
                            
                            <div class="success-box p-3 rounded mt-3">
                                <strong><i class="fas fa-sync-alt mr-2"></i>Resultados Esperados:</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    <li>Nueva cuota de recargo creada</li>
                                    <li>Concepto de morosidad generado</li>
                                    <li>Confirmación visual de éxito</li>
                                    <li>Actualización de lista de cuotas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Configuración -->
                <section id="configuracion" class="guide-section">
                    <h2 class="h4 mb-3">
                        <i class="fas fa-cogs text-primary mr-2"></i>
                        Configuración del Sistema
                    </h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark text-white py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-sliders-h mr-2"></i>
                                        Parámetros Configurables
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="30%"><strong>Tasa Recargo</strong></td>
                                                <td>1% mensual</td>
                                                <td><span class="badge badge-success">Fijo</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Meses Máximos</strong></td>
                                                <td>12 meses</td>
                                                <td><span class="badge badge-warning">Límite</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha Base</strong></td>
                                                <td>Fecha de mora o vencimiento</td>
                                                <td><span class="badge badge-info">Automático</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Monto Base</strong></td>
                                                <td>Saldo pendiente actual</td>
                                                <td><span class="badge badge-info">Automático</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark text-white py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-code mr-2"></i>
                                        Estructura de Datos
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="code-block small">
// Nueva Cuota de Recargo<br>
Cuentaxpagar: {<br>
&nbsp;&nbsp;name: '[Nombre Original] RM1',<br>
&nbsp;&nbsp;type: 'INDIVIDUAL',<br>
&nbsp;&nbsp;description: 'Recargo por Morosidad'<br>
}<br><br>
// Concepto de Recargo<br>
ConceptoPago: {<br>
&nbsp;&nbsp;concepto_description: '[Descripción] Recargo por Morosidad',<br>
&nbsp;&nbsp;exchange_ammount: [Monto Calculado]<br>
}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Solución de Errores -->
                <section id="errores" class="guide-section">
                    <h2 class="h4 mb-3">
                        <i class="fas fa-exclamation-triangle text-primary mr-2"></i>
                        Solución de Problemas Comunes
                    </h2>
                    
                    <div class="accordion" id="troubleshootingAccordion">
                        <!-- Problema 1 -->
                        <div class="card">
                            <div class="card-header bg-light" id="problem1">
                                <h6 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#solution1">
                                        <i class="fas fa-search text-danger mr-2"></i>
                                        No aparecen resultados en búsqueda
                                    </button>
                                </h6>
                            </div>
                            <div id="solution1" class="collapse show" data-parent="#troubleshootingAccordion">
                                <div class="card-body py-3">
                                    <strong>Solución:</strong>
                                    <ul class="mb-2">
                                        <li>Verifique que escribió mínimo 3 caracteres</li>
                                        <li>Confirme que el estudiante existe en el sistema</li>
                                        <li>Intente buscar por cédula en lugar de nombre</li>
                                    </ul>
                                    <small class="text-muted"><i class="fas fa-clock mr-1"></i>Tiempo estimado: 1 minuto</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Problema 2 -->
                        <div class="card">
                            <div class="card-header bg-light" id="problem2">
                                <h6 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#solution2">
                                        <i class="fas fa-file-invoice-dollar text-danger mr-2"></i>
                                        No se muestran cuotas vencidas
                                    </button>
                                </h6>
                            </div>
                            <div id="solution2" class="collapse" data-parent="#troubleshootingAccordion">
                                <div class="card-body py-3">
                                    <strong>Solución:</strong>
                                    <ul class="mb-2">
                                        <li>Verifique que el estudiante tenga cuotas vencidas</li>
                                        <li>Confirme fechas de vencimiento en el sistema</li>
                                        <li>Revise que las cuotas no estén completamente pagadas</li>
                                    </ul>
                                    <small class="text-muted"><i class="fas fa-clock mr-1"></i>Tiempo estimado: 2 minutos</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Problema 3 -->
                        <div class="card">
                            <div class="card-header bg-light" id="problem3">
                                <h6 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#solution3">
                                        <i class="fas fa-calculator text-danger mr-2"></i>
                                        Cálculo de recargo incorrecto
                                    </button>
                                </h6>
                            </div>
                            <div id="solution3" class="collapse" data-parent="#troubleshootingAccordion">
                                <div class="card-body py-3">
                                    <strong>Solución:</strong>
                                    <ul class="mb-2">
                                        <li>Verifique fecha de vencimiento de la cuota</li>
                                        <li>Confirme el monto pendiente actual</li>
                                        <li>Revise que los meses de mora sean correctos</li>
                                        <li>Contacte soporte si persiste el problema</li>
                                    </ul>
                                    <small class="text-muted"><i class="fas fa-clock mr-1"></i>Tiempo estimado: 3 minutos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-box p-3 rounded mt-3">
                        <strong><i class="fas fa-headset mr-2"></i>Soporte Técnico:</strong> 
                        Si los problemas persisten, contacte al equipo de soporte con el ID del estudiante y captura de pantalla del error.
                    </div>
                </section>

                <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.morosidad') }}', '_blank')">
                    <i class="fas fa-print"></i> Versión Imprimible
                </button>
            </div>
        </div>
    </div>

    <small class="text-muted">Última actualización: Octubre 2025</small>

    @section('stylesheet')
    @parent
    <style>
        .guide-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            background: #f8f9fa;
        }
        
        .step-indicator-guide {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .step-guide {
            text-align: center;
            flex: 1;
            position: relative;
            padding-top: 15px;
        }
        
        .step-guide:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 7px;
            left: 50%;
            width: 100%;
            height: 2px;
            background-color: #dee2e6;
            z-index: 1;
        }
        
        .step-circle-guide {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #dee2e6;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 2;
            margin-bottom: 0.5rem;
        }
        
        .step-guide.active .step-circle-guide {
            border-color: #007bff;
            color: #007bff;
        }
        
        .step-guide.completed .step-circle-guide {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .nav-guide-sticky {
            position: sticky;
            top: 20px;
        }
        
        .info-box {
            border-left: 4px solid #17a2b8;
            background: #e7f3ff;
        }
        
        .warning-box {
            border-left: 4px solid #ffc107;
            background: #fff3cd;
        }
        
        .success-box {
            border-left: 4px solid #28a745;
            background: #d4edda;
        }
        
        .danger-box {
            border-left: 4px solid #dc3545;
            background: #f8d7da;
        }
        
        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            border-radius: 4px;
            padding: 0.75rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
        
        .screenshot-placeholder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 6px;
            padding: 2rem 1rem;
            text-align: center;
        }
    </style>
    @endsection

    @section('scripts')
    @parent
    @endsection

</div>