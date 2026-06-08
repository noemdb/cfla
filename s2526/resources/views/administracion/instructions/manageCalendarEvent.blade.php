{{-- resources/views/administracion/instructions/calendarEvents.blade.php --}}
<div class="row">
    <!-- Contenido Principal -->
    <div class="col-md-12">
        <!-- Introducción -->
        <section id="introduccion" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-calendar-plus text-primary"></i>
                Introducción
            </h2>

            <div class="row">
                <div class="col-md-8">
                    <p class="lead">
                        La <strong>Sección para la gestión de Eventos/Días Feriados/Lunes Bancarios</strong> es una herramienta crítica para el 
                        <strong>control automatizado de la tasa de cambio</strong>.
                    </p>

                    <div class="tip-box">
                        <strong><i class="fas fa-lightbulb"></i> Propósito Principal del Sistema:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Gestión automatizada de la tasa de cambio</strong> en días feriados</li>
                            <li>Mecánica automática para cálculo de tasas en días no hábiles</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-exchange-alt fa-3x text-success mb-3"></i>
                            <h5>Características</h5>
                            <span class="badge badge-primary feature-badge">Tasa de Cambio</span>
                            <span class="badge badge-success feature-badge">Automático</span>
                            <span class="badge badge-info feature-badge">Días Feriados</span>
                            <span class="badge badge-warning feature-badge">Cálculo Hábiles</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Objetivo Principal -->
        <section id="objetivo" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-bullseye text-danger"></i>
                Objetivo Principal: Tasa de Cambio
            </h2>

            <div class="alert alert-warning">
                <strong><i class="fas fa-exclamation-triangle"></i> Función Crítica:</strong>
                Este módulo es fundamental para el <strong>sistema automatizado de tasa de cambio</strong> que afecta directamente los procesos financieros de la institución.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-cogs"></i> Mecánica Automatizada</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Comportamiento del Sistema:</strong></p>
                            <ul class="mb-0">
                                <li>En días <strong>feriados registrados</strong>, el sistema automáticamente</li>
                                <li>Utiliza la tasa de cambio del <strong>último día hábil anterior</strong></li>
                                <li>Evita cálculos erróneos en días no laborables</li>
                                <li>Mantiene consistencia en procesos financieros</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-calendar-check"></i> Ejemplo Práctico</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Escenario:</strong></p>
                            <ul class="mb-0">
                                <li><strong>Viernes:</strong> Tasa = 4.25 Bs/$</li>
                                <li><strong>Sábado (Feriado):</strong> Sistema usa 4.25 Bs/$</li>
                                <li><strong>Domingo (Feriado):</strong> Sistema usa 4.25 Bs/$</li>
                                <li><strong>Lunes:</strong> Tasa actual del día</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Requisitos -->
        <section id="requisitos" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-clipboard-check text-warning"></i>
                Requisitos Previos
            </h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Permisos Necesarios</h6>
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Acceso al módulo de Administración</li>
                                <li>Rol de administrador o permisos específicos</li>
                                <li>Autorización para modificar el calendario institucional</li>
                                <li>Conocimiento del impacto en tasa de cambio</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <strong><i class="fas fa-info-circle"></i> Impacto Financiero:</strong>
                        <p>Los días feriados registrados aquí afectan directamente el <strong>cálculo de la tasa de cambio</strong> utilizada en todos los procesos financieros y de pago de la institución.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Flujo de Pasos -->
        <section id="flujo" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-stream text-primary"></i>
                Flujo de Trabajo
            </h2>

            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">1</div>
                            <h6>Listar Eventos</h6>
                            <p class="small">Ver eventos existentes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">2</div>
                            <h6>Crear Nuevo</h6>
                            <p class="small">Registrar evento o feriado</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">3</div>
                            <h6>Configurar</h6>
                            <p class="small">Definir tipo y detalles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center step-card">
                        <div class="card-body">
                            <div class="step-number mx-auto mb-3">4</div>
                            <h6>Validar Impacto</h6>
                            <p class="small">Verificar efecto en tasa</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Paso 1: Navegación -->
        <section id="paso1" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-list text-primary"></i>
                Paso 1: Navegación y Listado
            </h2>
            <p>Al acceder al módulo, encontrará una tabla con todos los eventos registrados en el sistema.</p>
            <ul>
                <li>Use la <strong>paginación</strong> para navegar entre múltiples páginas de eventos.</li>
                <li>Cada evento muestra: <strong>Fecha, Nombre, Descripción y Tipo</strong>.</li>
                <li>Los días feriados se destacan con un badge especial <span class="badge badge-success">Día Feriado</span>.</li>
                <li>Desde aquí puede <strong>Ver, Editar o Eliminar</strong> eventos existentes.</li>
            </ul>
        </section>

        <!-- Paso 2: Creación - Actualizar tabla de campos -->
        <section id="paso2" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-plus-circle text-success"></i>
                Paso 2: Crear Nuevo Evento
            </h2>
            
            <div class="alert alert-info">
                <strong><i class="fas fa-mouse-pointer"></i> Acción Inicial:</strong>
                Haga clic en el botón <button class="btn btn-primary btn-sm" disabled><i class="fas fa-plus"></i></button> en la esquina superior derecha para abrir el formulario de creación.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                            <th>Obligatorio</th>
                            <th>Impacto en Tasa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Fecha del Evento</strong></td>
                            <td>Fecha específica del evento o feriado</td>
                            <td><span class="badge badge-danger">Sí</span></td>
                            <td><span class="badge badge-warning">Directo</span></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre</strong></td>
                            <td>Título descriptivo del evento</td>
                            <td><span class="badge badge-warning">Opcional</span></td>
                            <td><span class="badge badge-secondary">Ninguno</span></td>
                        </tr>
                        <tr>
                            <td><strong>Descripción</strong></td>
                            <td>Detalles adicionales del evento</td>
                            <td><span class="badge badge-warning">Opcional</span></td>
                            <td><span class="badge badge-secondary">Ninguno</span></td>
                        </tr>
                        <tr>
                            <td><strong>Día Feriado</strong></td>
                            <td>Indica si es un día no laborable</td>
                            <td><span class="badge badge-danger">Sí</span></td>
                            <td><span class="badge badge-danger">CRÍTICO</span></td>
                        </tr>
                        <tr>
                            <td><strong>Icono</strong></td>
                            <td>Representación visual del tipo de evento</td>
                            <td><span class="badge badge-warning">Opcional</span></td>
                            <td><span class="badge badge-secondary">Ninguno</span></td>
                        </tr>
                        <tr>
                            <td><strong>Observaciones</strong></td>
                            <td>Notas internas adicionales</td>
                            <td><span class="badge badge-warning">Opcional</span></td>
                            <td><span class="badge badge-secondary">Ninguno</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Nueva Sección: Sistema de Iconos -->
        <section id="iconos" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-icons text-info"></i>
                Sistema de Iconos Visuales
            </h2>

            <div class="alert alert-info">
                <strong><i class="fas fa-palette"></i> Mejora Visual:</strong>
                El sistema ahora incluye iconos representativos para identificar rápidamente el tipo de evento en el listado.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-star"></i> Iconos Disponibles</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <i class="fas fa-calendar-day text-primary fa-lg mr-2"></i>
                                    <small><strong>Evento Regular</strong></small>
                                </div>
                                <div class="col-6 mb-3">
                                    <i class="fas fa-umbrella-beach text-danger fa-lg mr-2"></i>
                                    <small><strong>Día Feriado</strong></small>
                                </div>
                                <div class="col-6 mb-3">
                                    <i class="fas fa-gift text-warning fa-lg mr-2"></i>
                                    <small><strong>Celebración</strong></small>
                                </div>
                                <div class="col-6 mb-3">
                                    <i class="fas fa-flag text-success fa-lg mr-2"></i>
                                    <small><strong>Feriado Nacional</strong></small>
                                </div>
                                <div class="col-6 mb-3">
                                    <i class="fas fa-building text-info fa-lg mr-2"></i>
                                    <small><strong>Lunes Bancario</strong></small>
                                </div>
                                <div class="col-6 mb-3">
                                    <i class="fas fa-graduation-cap text-purple fa-lg mr-2"></i>
                                    <small><strong>Evento Académico</strong></small>
                                </div>
                                <div class="col-6 mb-3">
                                    <i class="fas fa-bullhorn text-orange fa-lg mr-2"></i>
                                    <small><strong>Anuncio Especial</strong></small>
                                </div>
                                <div class="col-6 mb-3">
                                    <i class="fas fa-clock text-secondary fa-lg mr-2"></i>
                                    <small><strong>Fecha Límite</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-eye"></i> Beneficios Visuales</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li><strong>Identificación rápida</strong> del tipo de evento</li>
                                <li><strong>Mejora la experiencia</strong> de usuario</li>
                                <li><strong>Organización visual</strong> en listados</li>
                                <li><strong>Diferenciación inmediata</strong> entre eventos</li>
                                <li><strong>Vista previa en tiempo real</strong> al seleccionar</li>
                            </ul>
                        </div>
                    </div>

                    <div class="tip-box mt-3">
                        <strong><i class="fas fa-lightbulb"></i> Recomendación:</strong>
                        Utilice iconos que representen adecuadamente la naturaleza del evento. Por ejemplo, <i class="fas fa-umbrella-beach"></i> para días feriados vacacionales.
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h5><i class="fas fa-desktop"></i> Vista en el Listado</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60px">Icono</th>
                                    <th>Evento</th>
                                    <th width="150px">Tipo</th>
                                    <th>Descripción Visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-warning">
                                    <td class="text-center"><i class="fas fa-umbrella-beach text-danger"></i></td>
                                    <td><strong>Navidad</strong></td>
                                    <td><span class="badge badge-danger">Día Feriado</span></td>
                                    <td><small>Fondo amarillo con icono rojo</small></td>
                                </tr>
                                <tr>
                                    <td class="text-center"><i class="fas fa-graduation-cap text-primary"></i></td>
                                    <td><strong>Inicio de Clases</strong></td>
                                    <td><span class="badge badge-primary">Evento Regular</span></td>
                                    <td><small>Fondo normal con icono azul</small></td>
                                </tr>
                                <tr class="table-warning">
                                    <td class="text-center"><i class="fas fa-flag text-success"></i></td>
                                    <td><strong>Día de la Independencia</strong></td>
                                    <td><span class="badge badge-danger">Día Feriado</span></td>
                                    <td><small>Fondo amarillo con icono verde</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Paso 3: Configuración -->
        <section id="paso3" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-exchange-alt text-primary"></i>
                Paso 3: Configuración para Tasa de Cambio
            </h2>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-calendar-times"></i> Días Feriados (AFECTA TASA)</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Comportamiento del Sistema:</strong></p>
                            <ul>
                                <li><strong>Activa mecanismo automático</strong> de tasa</li>
                                <li>Usa tasa del <strong>último día hábil anterior</strong></li>
                                <li>No se permiten actividades académicas</li>
                                <li>Afecta cálculos de fechas límite</li>
                                <li><strong>Impacto directo en procesos financieros</strong></li>
                                <li><strong>Se destacan en amarillo</strong> en el listado</li>
                            </ul>
                            <div class="d-flex align-items-center mt-2">
                                <i class="fas fa-umbrella-beach text-danger fa-lg mr-2"></i>
                                <small><strong>Icono recomendado:</strong> Para días feriados vacacionales</small>
                            </div>
                            <div class="alert alert-warning mb-0 mt-2">
                                <small><strong>⚠️ Importante:</strong> Marcar como feriado SOLO días no laborables que afecten la tasa de cambio.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-calendar-check"></i> Eventos Regulares (NO AFECTA TASA)</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Características:</strong></p>
                            <ul>
                                <li><strong>No afecta</strong> el cálculo de tasa de cambio</li>
                                <li>Actividades académicas normales</li>
                                <li>Recordatorios y fechas importantes</li>
                                <li>Informativos para la comunidad</li>
                                <li>Sin impacto en procesos financieros</li>
                                <li><strong>Apariencia normal</strong> en el listado</li>
                            </ul>
                            <div class="d-flex align-items-center mt-2">
                                <i class="fas fa-graduation-cap text-primary fa-lg mr-2"></i>
                                <small><strong>Icono recomendado:</strong> Para eventos académicos</small>
                            </div>
                            <div class="alert alert-info mb-0 mt-2">
                                <small><strong>💡 Use para:</strong> Eventos informativos, actividades, recordatorios.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="warning-box mt-3">
                <strong><i class="fas fa-calculator"></i> Mecánica de Cálculo Automático:</strong>
                Cuando el sistema detecta un día marcado como <strong>"Día Feriado"</strong>, automáticamente busca la tasa de cambio del último día hábil anterior y la aplica para todos los cálculos financieros de ese día.
            </div>
        </section>

        <!-- Paso 4: Validación y Gestión -->
        <section id="paso4" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-search-dollar text-primary"></i>
                Paso 4: Validación del Impacto
            </h2>

            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <button class="btn btn-info btn-sm mb-2" disabled><i class="fas fa-eye"></i></button>
                        <h6>Ver Detalles</h6>
                        <p class="small">Consulta completa incluyendo tipo de evento</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <button class="btn btn-warning btn-sm mb-2" disabled><i class="fas fa-edit"></i></button>
                        <h6>Editar</h6>
                        <p class="small">Modificar tipo feriado/regular</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <button class="btn btn-danger btn-sm mb-2" disabled><i class="fas fa-trash"></i></button>
                        <h6>Eliminar</h6>
                        <p class="small">Remover evento del sistema</p>
                    </div>
                </div>
            </div>

            <div class="danger-box mt-3">
                <strong><i class="fas fa-exclamation-triangle"></i> Restricción de Eliminación CRÍTICA:</strong>
                <strong>No es posible eliminar eventos con fecha pasada.</strong> Esta restricción es esencial para mantener la integridad del historial de tasas de cambio y evitar inconsistencias en los registros financieros.
            </div>

            <div class="tip-box mt-3">
                <strong><i class="fas fa-check-double"></i> Verificación Recomendada:</strong>
                Antes de guardar un día como feriado, verifique que efectivamente sea un día no laborable que requiera el uso de la tasa de cambio del día hábil anterior.
            </div>
        </section>

        <!-- Confirmación -->
        <section id="confirmacion" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-check-double text-success"></i>
                Confirmación y Validación
            </h2>
            
            <div class="success-box">
                <strong><i class="fas fa-save"></i> Al Guardar:</strong>
                El sistema validará automáticamente todos los campos. Los días feriados se integran inmediatamente con el <strong>módulo de tasa de cambio</strong> y afectan los cálculos financieros a partir de ese momento.
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check"></i> Validaciones Automáticas</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>Formato correcto de fecha</li>
                                <li>Campos obligatorios completos</li>
                                <li>No duplicados en misma fecha</li>
                                <li>Integridad del historial de tasas</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-sync"></i> Sincronización con Tasa</h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>Actualización en tiempo real</li>
                                <li>Integración con módulo financiero</li>
                                <li>Cálculo automático de días hábiles</li>
                                <li>Consistencia en registros contables</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Actualización en el FAQ -->
        <section id="faq" class="guide-section px-2">
            <h2 class="mb-4">
                <i class="fas fa-question-circle text-primary"></i>
                Preguntas Frecuentes
            </h2>
            <div class="accordion" id="faqAccordion">
                <!-- Preguntas existentes... -->
                
                <div class="card">
                    <div class="card-header" id="faq5">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer5">
                                <i class="fas fa-question"></i> ¿Puedo cambiar el icono de un evento después de crearlo?
                            </button>
                        </h5>
                    </div>
                    <div id="answer5" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            <strong>Sí</strong>, puede editar cualquier evento y cambiar su icono, simepre y cuando su fecha sea mayor al día actual. La modificación se reflejará inmediatamente en el listado.
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header" id="faq6">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer6">
                                <i class="fas fa-question"></i> ¿Qué pasa si no selecciono un icono para un evento?
                            </button>
                        </h5>
                    </div>
                    <div id="answer6" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            El sistema asignará automáticamente el icono por defecto <i class="fas fa-calendar-day"></i> "Evento Regular". Siempre puede editarlo posteriormente si lo desea.
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header" id="faq7">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#answer7">
                                <i class="fas fa-question"></i> ¿Los iconos afectan el comportamiento del sistema o son solo visuales?
                            </button>
                        </h5>
                    </div>
                    <div id="answer7" class="collapse" data-parent="#faqAccordion">
                        <div class="card-body">
                            Los iconos son <strong>puramente visuales</strong> y no afectan el comportamiento del sistema. Solo el campo "Día Feriado" (Sí/No) determina si se activa el mecanismo de tasa de cambio automática.
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>

@section('stylesheet')
@parent

<style>
    /* Estilos consistentes con el sistema existente */
    .guide-section { padding: 20px 0; border-bottom: 1px solid #eee; }
    .tip-box { background-color: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; border-radius: 4px; }
    .info-box { background-color: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; border-radius: 4px; }
    .warning-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px; }
    .danger-box { background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; border-radius: 4px; }
    .success-box { background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; border-radius: 4px; }
    .step-card { height: 100%; transition: transform 0.2s; }
    .step-card:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .step-number { width: 40px; height: 40px; background-color: #007bff; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; }
    .feature-badge { display: block; margin: 2px 0; }
    .text-purple { color: #6f42c1 !important; }
    .text-orange { color: #fd7e14 !important; }
</style>
@endsection