<div class="container-fluid">
    <div class="row">
        <!-- Contenido Principal -->
        <div class="col-md-12">
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="text-primary">
                    <i class="fas fa-user-graduate mr-3"></i>
                    Guía Completa - Gestión de Retiros Estudiantiles
                </div>
                <p class="lead text-muted">Gestión segura y controlada de retiros académicos y administrativos</p>
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Actualización:</strong> Se ha implementado el campo de observaciones obligatorias para mejorar el control y seguimiento de retiros administrativos.
                </div>
            </div>

            <!-- Introducción -->
            <section id="introduccion" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-user-minus text-danger"></i>
                    Introducción
                </h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-dark">
                            La <strong>gestión de Retiro de Estudiantes</strong> es una herramienta especializada que permite gestionar el retiro de estudiantes de manera segura y controlada, diferenciando entre retiro académico y administrativo según el rol del usuario.
                        </p>
                        
                        <div class="tip-box">
                            <strong><i class="fas fa-lightbulb"></i> Nueva Funcionalidad:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Observaciones obligatorias</strong> para retiros administrativos</li>
                                <li>Modal nativo para captura de justificaciones</li>
                                <li>Validación en tiempo real (10-500 caracteres)</li>
                                <li>Prevención mejorada de retiros duplicados</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-users-slash fa-3x text-danger mb-3"></i>
                                <h5>Características Principales</h5>
                                <span class="badge badge-primary feature-badge">2 Tipos de Retiro</span>
                                <span class="badge badge-success feature-badge">Observaciones Obligatorias</span>
                                <span class="badge badge-info feature-badge">Control de Deudas</span>
                                <span class="badge badge-warning feature-badge">Validación en Tiempo Real</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Observaciones Obligatorias -->
            <section id="observaciones" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-clipboard-list text-success"></i>
                    Nuevo: Campo de Observaciones Obligatorias
                </h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <h4>¿Qué ha cambiado?</h4>
                        <p>Se ha implementado un <strong>campo de observaciones obligatorio</strong> para todos los retiros administrativos, con el objetivo de mejorar el control y seguimiento del proceso.</p>
                        
                        <h5>Características del Campo:</h5>
                        <ul>
                            <li><strong>Tipo:</strong> Textarea con validación en tiempo real</li>
                            <li><strong>Longitud:</strong> Mínimo 10 caracteres, máximo 500 caracteres</li>
                            <li><strong>Requerido:</strong> Exclusivamente para retiros administrativos</li>
                            <li><strong>Propósito:</strong> Razonamiento y justificación del retiro</li>
                        </ul>
                        
                        <div class="warning-box">
                            <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong> 
                            Sin las observaciones completas, el sistema <strong>NO permitirá procesar</strong> el retiro administrativo. El botón de confirmación permanecerá deshabilitado hasta que se cumplan los requisitos.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-edit fa-2x text-success mb-3"></i>
                                <p class="mb-1"><strong>Nuevo Modal de Observaciones</strong></p>
                                <small class="text-muted">Captura obligatoria</small>
                                <div class="mt-3">
                                    <span class="badge badge-success">10-500 chars</span>
                                    <span class="badge badge-info">Validación en tiempo real</span>
                                    <span class="badge badge-warning">Obligatorio</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Validaciones Implementadas</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Validación</th>
                                <th>Requisito</th>
                                <th>Mensaje de Error</th>
                                <th>Comportamiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Campo Requerido</strong></td>
                                <td>No puede estar vacío</td>
                                <td>"Las observaciones son obligatorias para el retiro administrativo."</td>
                                <td>Botón deshabilitado</td>
                            </tr>
                            <tr>
                                <td><strong>Mínimo de Caracteres</strong></td>
                                <td>Al menos 10 caracteres</td>
                                <td>"Las observaciones deben tener al menos 10 caracteres."</td>
                                <td>Botón deshabilitado</td>
                            </tr>
                            <tr>
                                <td><strong>Máximo de Caracteres</strong></td>
                                <td>Máximo 500 caracteres</td>
                                <td>"Las observaciones no pueden exceder los 500 caracteres."</td>
                                <td>Bloqueo de escritura</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Roles y Permisos -->
            <section id="roles" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-user-shield text-primary"></i>
                    Roles de Usuario y Permisos
                </h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-check"></i>
                                    Rol: Control Académico
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-primary">Flujo de Confirmación:</h6>
                                <ul>
                                    <li><strong>Ventana SweetAlert</strong> tradicional</li>
                                    <li><strong>Sin observaciones</strong> obligatorias</li>
                                    <li>Confirmación directa con "Sí, continuar"</li>
                                </ul>
                                
                                <h6 class="text-primary">Acciones:</h6>
                                <ul>
                                    <li>Elimina inscripción académica</li>
                                    <li>Crea registro de retiro tipo "control"</li>
                                    <li>No requiere justificación escrita</li>
                                    <li>No genera deudas financieras</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-cog"></i>
                                    Rol: Administración
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-danger">Nuevo Flujo de Confirmación:</h6>
                                <ul>
                                    <li><strong>Modal nativo</strong> con textarea</li>
                                    <li><strong>Observaciones obligatorias</strong> (10-500 chars)</li>
                                    <li>Validación en tiempo real</li>
                                    <li>Botón habilitado solo con datos válidos</li>
                                </ul>
                                
                                <h6 class="text-danger">Acciones:</h6>
                                <ul>
                                    <li>Genera deuda pendiente si aplica</li>
                                    <li>Actualiza datos administrativos</li>
                                    <li>Guarda observaciones en el registro</li>
                                    <li>Requiere justificación escrita completa</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Inicio Rápido -->
            <section id="quickstart" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-bolt text-warning"></i>
                    Inicio Rápido - Flujos por Rol
                </h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-center step-card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Control Académico - 3 Pasos</h5>
                            </div>
                            <div class="card-body">
                                <div class="step-number mx-auto mb-3 bg-primary">1</div>
                                <h6>Buscar Estudiante</h6>
                                <small class="text-muted">Por nombre o cédula</small>
                                
                                <div class="step-number mx-auto mb-3 bg-primary">2</div>
                                <h6>Confirmar en SweetAlert</h6>
                                <small class="text-muted">Ventana emergente</small>
                                
                                <div class="step-number mx-auto mb-3 bg-primary">3</div>
                                <h6>Retiro Procesado</h6>
                                <small class="text-muted">Inscripción eliminada</small>
                                
                                <span class="badge badge-primary mt-2">Flujo Rápido</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-center step-card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">Administración - 4 Pasos</h5>
                            </div>
                            <div class="card-body">
                                <div class="step-number mx-auto mb-3 bg-danger">1</div>
                                <h6>Buscar Estudiante</h6>
                                <small class="text-muted">Por nombre o cédula</small>
                                
                                <div class="step-number mx-auto mb-3 bg-danger">2</div>
                                <h6>Modal de Observaciones</h6>
                                <small class="text-muted">Justificación obligatoria</small>
                                
                                <div class="step-number mx-auto mb-3 bg-danger">3</div>
                                <h6>Validar y Confirmar</h6>
                                <small class="text-muted">Botón inteligente</small>
                                
                                <div class="step-number mx-auto mb-3 bg-danger">4</div>
                                <h6>Retiro Procesado</h6>
                                <small class="text-muted">Con deuda si aplica</small>
                                
                                <span class="badge badge-danger mt-2">Con Validación</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 1: Búsqueda -->
            <section id="paso1" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-search text-primary"></i>
                    Paso 1: Búsqueda y Selección de Estudiantes
                </h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <h4>Procedimiento de Búsqueda</h4>
                        <ol>
                            <li><strong>Acceder al sistema</strong> - Se muestra lista paginada de estudiantes activos</li>
                            <li>
                                <strong>Utilizar buscador:</strong>
                                <ul>
                                    <li>Escribir nombre completo o parcial del estudiante</li>
                                    <li>O ingresar número de cédula</li>
                                    <li>El sistema busca en tiempo real (500ms de delay)</li>
                                </ul>
                            </li>
                            <li><strong>Filtrar resultados</strong> - Seleccionar cantidad de registros a mostrar (10, 25, 50, 100)</li>
                            <li><strong>Identificar estudiante</strong> en la tabla de resultados</li>
                        </ol>
                        
                        <div class="tip-box">
                            <strong><i class="fas fa-lightbulb"></i> Consejo de Búsqueda:</strong>
                            Use solo el primer nombre o primer apellido para obtener mejores resultados. El sistema busca coincidencias parciales en nombre, apellido y cédula.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="screenshot text-center">
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-search fa-2x text-primary mb-3"></i>
                                <p class="mb-1"><strong>Buscador Integrado</strong></p>
                                <small class="text-muted">Búsqueda en tiempo real</small>
                                <div class="mt-3">
                                    <span class="badge badge-primary">Nombre</span>
                                    <span class="badge badge-info">Cédula</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Interpretación de la Tabla de Resultados</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Columna</th>
                                <th>Descripción</th>
                                <th>Indicadores Clave</th>
                                <th>Significado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Estudiante</strong></td>
                                <td>Nombre completo</td>
                                <td>Color del texto</td>
                                <td><span class="text-danger">Rojo = Deuda pendiente</span><br><span class="text-dark">Negro = Sin deuda</span></td>
                            </tr>
                            <tr>
                                <td><strong>Plan de Pago</strong></td>
                                <td>Plan asignado</td>
                                <td>Badge de color</td>
                                <td>Muestra plan actual o "NINGUNO"</td>
                            </tr>
                            <tr>
                                <td><strong>Deuda [USD]</strong></td>
                                <td>Monto adeudado</td>
                                <td>Número formateado</td>
                                <td>0.00 = Solvente<br>>0.00 = Con deuda</td>
                            </tr>
                            <tr>
                                <td><strong>R.Académico</strong></td>
                                <td>Retiro académico</td>
                                <td>Badge naranja/gris</td>
                                <td><span class="badge badge-warning">SI</span> = Con retiro<br><span class="badge badge-secondary">NO</span> = Sin retiro</td>
                            </tr>
                            <tr>
                                <td><strong>R.Administrativo</strong></td>
                                <td>Retiro administrativo</td>
                                <td>Badge rojo/gris</td>
                                <td><span class="badge badge-danger">SI</span> = Con retiro<br><span class="badge badge-secondary">NO</span> = Sin retiro</td>
                            </tr>
                            <tr>
                                <td><strong>Acción</strong></td>
                                <td>Botón de retiro</td>
                                <td>Color del botón</td>
                                <td><span class="badge badge-danger">Rojo</span> = Disponible<br><span class="badge badge-secondary">Gris</span> = No disponible</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Paso 2: Confirmación -->
            <section id="paso2" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-check-circle text-primary"></i>
                    Paso 2: Confirmación del Retiro
                </h2>
                
                <h4>Nuevos Procesos de Confirmación</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-check"></i>
                                    Control Académico (Existente)
                                </h5>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li><strong>Click en botón rojo</strong> del estudiante</li>
                                    <li><strong>SweetAlert de confirmación</strong> aparece</li>
                                    <li><strong>Revisar información</strong> del estudiante</li>
                                    <li><strong>Confirmar con "Sí, continuar"</strong></li>
                                    <li><strong>Retiro procesado</strong> inmediatamente</li>
                                </ol>
                                
                                <div class="alert alert-primary mt-3">
                                    <strong><i class="fas fa-info-circle"></i> Características:</strong>
                                    <ul class="mb-0">
                                        <li>Proceso rápido y directo</li>
                                        <li>Sin captura de observaciones</li>
                                        <li>Confirmación tradicional</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-cog"></i>
                                    Administración (Nuevo)
                                </h5>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li><strong>Click en botón rojo</strong> del estudiante</li>
                                    <li><strong>Modal nativo aparece</strong> con textarea</li>
                                    <li><strong>Ingresar observaciones</strong> (10-500 chars)</li>
                                    <li><strong>Botón se habilita</strong> automáticamente</li>
                                    <li><strong>Confirmar retiro</strong> con justificación</li>
                                </ol>
                                
                                <div class="alert alert-danger mt-3">
                                    <strong><i class="fas fa-exclamation-circle"></i> Nuevas Características:</strong>
                                    <ul class="mb-0">
                                        <li>Validación en tiempo real</li>
                                        <li>Contador de caracteres dinámico</li>
                                        <li>Botón inteligente</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Características del Nuevo Modal</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-text-height fa-2x text-info mb-2"></i>
                            <h6>Contador en Tiempo Real</h6>
                            <small>Muestra 0/500 → 150/500</small>
                            <div class="mt-2">
                                <span class="badge badge-success">✓ Válido</span>
                                <span class="badge badge-danger">✗ Inválido</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-toggle-on fa-2x text-success mb-2"></i>
                            <h6>Botón Inteligente</h6>
                            <small>Se habilita solo con datos válidos</small>
                            <div class="mt-2">
                                <span class="badge badge-success">Habilitado</span>
                                <span class="badge badge-secondary">Deshabilitado</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-ban fa-2x text-warning mb-2"></i>
                            <h6>Bloqueo por Retiro Existente</h6>
                            <small>Campos deshabilitados si ya tiene retiro</small>
                            <div class="mt-2">
                                <span class="badge badge-warning">Bloqueado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Paso 3: Procesamiento -->
            <section id="paso3" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-cogs text-primary"></i>
                    Paso 3: Procesamiento con Observaciones
                </h2>
                
                <h4>Mejoras en el Registro de Retiros</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-check"></i>
                                    Retiro Académico (Control)
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-primary">Acciones Ejecutadas:</h6>
                                <ol>
                                    <li><strong>Registro de Retiro:</strong> Crea/actualiza registro tipo "control"</li>
                                    <li><strong>Eliminación de Inscripción:</strong> Remueve inscripción académica</li>
                                    <li><strong>Estado:</strong> Marca status_control = 'true'</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-cog"></i>
                                    Retiro Administrativo (Admon)
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-danger">Acciones Ejecutadas:</h6>
                                <ol>
                                    <li><strong>Registro de Retiro:</strong> Crea/actualiza registro tipo "admon"</li>
                                    <li><strong>Gestión de Deudas:</strong> Genera deuda pendiente si existe</li>
                                    <li><strong>Actualización Administrativa:</strong> Cambia plan a "D. RETIRO ADMINISTRATIVO"</li>
                                    <li><strong>Estado:</strong> Marca status_admon = 'true'</li>
                                    <li><strong>Observaciones:</strong> Guarda justificación permanentemente</li>
                                </ol>
                                
                                <p class="text-success small mt-2">
                                    <i class="fas fa-save"></i> <strong>Nuevo:</strong> Las observaciones ahora se guardan permanentemente en el registro del retiro.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Procesamiento de Deudas (Solo Administrativo)</h4>
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Generación Automática de Deuda:</strong>
                    Si el estudiante tiene <code> deuda actual vencida > 0</code>, el sistema:
                    <ul class="mb-0 mt-2">
                        <li><strong>Crea cuenta por pagar</strong> en plan "D. RETIRO ADMINISTRATIVO"</li>
                        <li><strong>Genera concepto de pago</strong> por el monto adeudado</li>
                        <li><strong>Fecha de vencimiento:</strong> Fecha actual</li>
                        <li><strong>Descripción:</strong> Incluye usuario que realizó el retiro</li>
                    </ul>
                </div>
            </section>

            <!-- Resultados y Confirmación -->
            <section id="resultados" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-clipboard-check text-primary"></i>
                    Resultados y Confirmación Final
                </h2>
                
                <h4>Mensajes de Resultado</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5><i class="fas fa-check-circle"></i> Éxito</h5>
                                <p><strong>Mensaje:</strong> "El estudiante [Nombre] ha sido retirado exitosamente."</p>
                                <p><strong>Indicadores:</strong></p>
                                <ul>
                                    <li>Ventana verde con icono de verificación</li>
                                    <li>Estudiante desaparece de la lista o muestra retiro</li>
                                    <li>Botón cambia a gris deshabilitado</li>
                                    <li>Badge correspondiente cambia a "SI"</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h5><i class="fas fa-exclamation-circle"></i> Error</h5>
                                <p><strong>Mensaje:</strong> Describe el error específico ocurrido</p>
                                <p><strong>Causas comunes:</strong></p>
                                <ul>
                                    <li>Problemas de conexión a base de datos</li>
                                    <li>Estudiante no encontrado</li>
                                    <li>Permisos insuficientes</li>
                                    <li>Error en transacción</li>
                                    <li>Validación de observaciones fallida</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Cambios Visuales Post-Retiro</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Elemento</th>
                                <th>Cambio Visual</th>
                                <th>Significado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Botón de Acción</strong></td>
                                <td>Rojo → Gris deshabilitado</td>
                                <td>Retiro ya realizado</td>
                            </tr>
                            <tr>
                                <td><strong>Badge R.Académico</strong></td>
                                <td>NO → SI (naranja)</td>
                                <td>Retiro académico registrado</td>
                            </tr>
                            <tr>
                                <td><strong>Badge R.Administrativo</strong></td>
                                <td>NO → SI (rojo)</td>
                                <td>Retiro administrativo registrado</td>
                            </tr>
                            <tr>
                                <td><strong>Columna Fecha</strong></td>
                                <td>Aparece fecha/hora</td>
                                <td>Momento exacto del retiro</td>
                            </tr>
                            <tr>
                                <td><strong>Observaciones</strong></td>
                                <td>Guardadas en BD</td>
                                <td>Justificación disponible para auditoría</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Casos Especiales -->
            <section id="casos-especiales" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Casos Especiales y Validaciones
                </h2>
                
                <h4>Nuevos Escenarios de Validación</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-edit"></i> Observaciones Válidas</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Condiciones:</strong> 10-500 caracteres, texto significativo</p>
                                <p><strong>Resultado:</strong> Botón de confirmación HABILITADO</p>
                                <p><strong>Indicador:</strong> Contador en verde ✓</p>
                                <p><strong>Ejemplo válido:</strong> "Estudiante se retira por cambio de ciudad. Los padres presentaron documentación de traslado familiar."</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-exclamation"></i> Observaciones Inválidas</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Condiciones:</strong> Menos de 10 chars o más de 500</p>
                                <p><strong>Resultado:</strong> Botón de confirmación DESHABILITADO</p>
                                <p><strong>Indicador:</strong> Contador en rojo ✗</p>
                                <p><strong>Ejemplo inválido:</strong> "Se retira" (muy corto)</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-user-slash"></i> Retiro Administrativo Existente</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Comportamiento:</strong> Modal muestra campos DESHABILITADOS</p>
                                <p><strong>Textarea:</strong> No editable</p>
                                <p><strong>Botón confirmar:</strong> Deshabilitado permanentemente</p>
                                <p><strong>Mensaje:</strong> "No se puede procesar el retiro: ya tiene retiro administrativo"</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-sync-alt"></i> Cierre y Reapertura</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Cancelar modal:</strong> Cierra y limpia observaciones</p>
                                <p><strong>Reabrir:</strong> Comienza con textarea vacío</p>
                                <p><strong>Persistencia:</strong> No guarda datos hasta confirmación</p>
                                <p><strong>Seguridad:</strong> No permite confirmación sin validación</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mt-4">Escenarios Comunes Adicionales</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-user-clock"></i> Estudiante con Deuda</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Comportamiento:</strong> Nombre en rojo, retiro administrativo genera deuda</p>
                                <p><strong>Acción:</strong> Sistema crea registro de deuda automáticamente</p>
                                <p><strong>Plan destino:</strong> "D. RETIRO ADMINISTRATIVO"</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-ban"></i> Sin Plan de Pago</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Indicador:</strong> Badge gris "NINGUNO"</p>
                                <p><strong>Retiro administrativo:</strong> Asigna plan "D. RETIRO ADMINISTRATIVO"</p>
                                <p><strong>Retiro académico:</strong> No afecta planes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-user-check"></i> Estudiante Solvente</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Indicador:</strong> Nombre en negro, deuda 0.00</p>
                                <p><strong>Retiro administrativo:</strong> No genera deudas</p>
                                <p><strong>Proceso:</strong> Más rápido y simple</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Preguntas Frecuentes -->
            <section id="faq" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-question-circle text-primary"></i>
                    Preguntas Frecuentes Actualizadas
                </h2>
                
                <div class="accordion" id="faqAccordion">
                    <!-- Pregunta 1 -->
                    <div class="card">
                        <div class="card-header" id="faqNew1">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#answerNew1">
                                    <i class="fas fa-question"></i> ¿Por qué ahora debo ingresar observaciones para retiros administrativos?
                                </button>
                            </h5>
                        </div>
                        <div id="answerNew1" class="collapse show" data-parent="#faqAccordion">
                            <div class="card-body">
                                <strong>Mejora en el control y seguimiento.</strong> Las observaciones obligatorias permiten:
                                <ul>
                                    <li>Documentar el razonamiento detrás de cada retiro administrativo</li>
                                    <li>Crear un historial auditable de justificaciones</li>
                                    <li>Mejorar la transparencia en los procesos administrativos</li>
                                    <li>Proveer contexto para futuras consultas o auditorías</li>
                                    <li>Cumplir con estándares de buenas prácticas en gestión educativa</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 2 -->
                    <div class="card">
                        <div class="card-header" id="faqNew2">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answerNew2">
                                    <i class="fas fa-question"></i> ¿Qué tipo de información debo incluir en las observaciones?
                                </button>
                            </h5>
                        </div>
                        <div id="answerNew2" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <strong>Información relevante y específica:</strong>
                                <ul>
                                    <li><strong>Razón principal:</strong> "Retiro por transferencia a otra institución"</li>
                                    <li><strong>Contexto:</strong> "Estudiante con deuda pendiente del período anterior"</li>
                                    <li><strong>Justificación:</strong> "Solicitud de padres por cambio de residencia"</li>
                                    <li><strong>Detalles importantes:</strong> "Acuerdo de pago pendiente establecido"</li>
                                </ul>
                                <div class="tip-box mt-2">
                                    <strong>Ejemplo de observaciones válidas:</strong><br>
                                    "El estudiante se retira por cambio de ciudad. Los padres presentaron documentación que avala el traslado familiar. Se genera deuda pendiente por el saldo del período actual."
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 3 -->
                    <div class="card">
                        <div class="card-header" id="faqNew3">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answerNew3">
                                    <i class="fas fa-question"></i> ¿Qué pasa si el estudiante ya tiene un retiro administrativo registrado?
                                </button>
                            </h5>
                        </div>
                        <div id="answerNew3" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <strong>El sistema previene retiros duplicados:</strong>
                                <ul>
                                    <li>El modal se abre pero muestra un mensaje de advertencia</li>
                                    <li>El campo de observaciones aparece <span class="badge badge-secondary">DESHABILITADO</span></li>
                                    <li>El botón de confirmar aparece <span class="badge badge-secondary">DESHABILITADO</span></li>
                                    <li>Se muestra la fecha del retiro anterior</li>
                                </ul>
                                <div class="warning-box mt-2">
                                    <strong>Acción requerida:</strong> No es posible procesar un nuevo retiro administrativo para un estudiante que ya tiene uno registrado. Contacte al administrador del sistema si esto representa un error.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 4 -->
                    <div class="card">
                        <div class="card-header" id="faqNew4">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answerNew4">
                                    <i class="fas fa-question"></i> ¿Puedo guardar las observaciones y completarlas después?
                                </button>
                            </h5>
                        </div>
                        <div id="answerNew4" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <strong>No, el proceso es secuencial y en una sola sesión:</strong>
                                <ul>
                                    <li>Las observaciones se capturan justo antes de la confirmación</li>
                                    <li>Si cierra el modal, las observaciones se pierden</li>
                                    <li>Debe completar todo el proceso en una sola interacción</li>
                                    <li>Prepare la justificación antes de iniciar el retiro</li>
                                </ul>
                                <div class="tip-box mt-2">
                                    <strong>Recomendación:</strong> Tenga preparada la justificación del retiro antes de hacer clic en el botón de retirar. Esto agiliza el proceso y asegura que capture toda la información relevante.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 5 -->
                    <div class="card">
                        <div class="card-header" id="faqNew5">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answerNew5">
                                    <i class="fas fa-question"></i> ¿Dónde puedo ver las observaciones de retiros anteriores?
                                </button>
                            </h5>
                        </div>
                        <div id="answerNew5" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <strong>Las observaciones se almacenan en el registro del retiro:</strong>
                                <ul>
                                    <li>En la tabla de retiros en la base de datos</li>
                                    <li>En reportes del sistema de administración</li>
                                    <li>En consultas históricas de estudiantes</li>
                                    <li>En auditorías del sistema</li>
                                </ul>
                                <p class="mt-2">Para acceder a observaciones de retiros anteriores, consulte los módulos de reportes o contacte al administrador del sistema.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pregunta 6 -->
                    <div class="card">
                        <div class="card-header" id="faq6">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer6">
                                    <i class="fas fa-question"></i> ¿Por qué el botón de confirmar no se habilita?
                                </button>
                            </h5>
                        </div>
                        <div id="answer6" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <strong>Causas comunes del botón deshabilitado:</strong>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Causa</th>
                                            <th>Solución</th>
                                            <th>Indicador</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Observaciones muy cortas</strong></td>
                                            <td>Escriba al menos 10 caracteres</td>
                                            <td><span class="badge badge-danger">✗</span> Contador rojo</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Observaciones muy largas</strong></td>
                                            <td>Reduzca a máximo 500 caracteres</td>
                                            <td><span class="badge badge-danger">✗</span> Contador rojo</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Retiro administrativo existente</strong></td>
                                            <td>No puede procesar nuevo retiro</td>
                                            <td><span class="badge badge-secondary">Bloqueado</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Campo vacío</strong></td>
                                            <td>Ingrese las observaciones requeridas</td>
                                            <td><span class="badge badge-warning">0/500</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Resumen de Mejoras -->
            <section id="resumen" class="guide-section">
                <h2 class="mb-4">
                    <i class="fas fa-star text-warning"></i>
                    Resumen de Mejoras Implementadas
                </h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-check-circle"></i>
                                    Nuevas Funcionalidades
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li><strong>Observaciones obligatorias</strong> para retiros administrativos</li>
                                    <li><strong>Modal nativo</strong> con validación en tiempo real</li>
                                    <li><strong>Contador de caracteres</strong> dinámico (0/500 → 150/500)</li>
                                    <li><strong>Botón inteligente</strong> que se habilita automáticamente</li>
                                    <li><strong>Bloqueo por retiros duplicados</strong> mejorado</li>
                                    <li><strong>Almacenamiento permanente</strong> de justificaciones</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-shield-alt"></i>
                                    Mejoras de Control
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li><strong>Auditoría mejorada</strong> con justificaciones documentadas</li>
                                    <li><strong>Prevención de errores</strong> con validación estricta</li>
                                    <li><strong>Transparencia</strong> en procesos administrativos</li>
                                    <li><strong>Historial completo</strong> de razones de retiro</li>
                                    <li><strong>Seguridad</strong> contra retiros inconsistentes</li>
                                    <li><strong>Calidad de datos</strong> con información contextual</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="success-box mt-3">
                    <strong><i class="fas fa-rocket"></i> Beneficios para los Usuarios:</strong> 
                    Estas mejoras proporcionan un mejor control sobre el proceso de retiros, documentación completa de las decisiones administrativas, y prevención de errores mediante validaciones en tiempo real.
                </div>

                <div class="text-center mt-4">
                    <div class="alert alert-primary">
                        <h5><i class="fas fa-graduation-cap"></i> ¿Necesita más ayuda?</h5>
                        <p class="mb-0">Contacte al administrador del sistema o consulte la documentación técnica para información adicional.</p>
                    </div>
                </div>

                <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.retiros') }}', '_blank')">
                    <i class="fas fa-print"></i> Versión Imprimible
                </button>
            </section>
        </div>
    </div>
</div>

@section('stylesheet')
@parent
<style>
.guide-section {
    margin-bottom: 3rem;
    padding: 2rem;
    padding-top: 1rem;
    border-radius: 10px;
    border-left: 5px solid #007bff;
    background: #f8f9fa;
}

.step-card {
    transition: transform 0.3s ease;
    margin-bottom: 1.5rem;
}

.step-card:hover {
    transform: translateY(-5px);
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
}

.feature-badge {
    font-size: 0.8rem;
    margin: 0.2rem;
}

.tip-box {
    background: #e7f3ff;
    border-left: 4px solid #17a2b8;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 5px 5px 0;
}

.warning-box {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 5px 5px 0;
}

.success-box {
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 5px 5px 0;
}

.nav-guide {
    position: sticky;
    top: 20px;
}

.screenshot {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 10px;
    background: white;
    margin: 1rem 0;
}

/* Nuevos estilos para mejoras visuales */
pre {
    border-radius: 5px;
    border: 1px solid #dee2e6;
    font-size: 0.8rem;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.page-header {
    border-bottom: 2px solid #007bff;
    padding-bottom: 1rem;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #dee2e6;
}

.list-group-item:last-child {
    border-bottom: none;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.accordion .card-header {
    background-color: #f8f9fa;
}

.accordion .btn-link {
    color: #495057;
    text-decoration: none;
    width: 100%;
    text-align: left;
}

.accordion .btn-link:hover {
    color: #007bff;
}
</style>
@endsection
