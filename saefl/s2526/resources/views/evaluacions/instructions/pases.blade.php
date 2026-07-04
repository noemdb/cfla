<div class="container-fluid py-1">

    <!-- Header Principal -->
    <div class="row mb-1">
        <div class="col-12">
            <div class="jumbotron alert-danger py-2 mb-2">
                <h3 class="font-weight-bold">
                    <i class="fas fa-passport text-danger"></i>
                    Gestión de Pases Escolares
                </h3>
                <p class="text-dark mb-1">
                    Módulo del Sistema SAEFL destinado a la administración, control, autorización y seguimiento integral
                    de los pases de salida estudiantil.
                </p>
                <small class="text-muted">
                    Guía actualizada — procedimientos, flujos y mejores prácticas para operadores administrativos,
                    profesores y coordinadores.
                </small>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Contenido Principal -->
        <div class="col-md-12">

            <!-- Introducción -->
            <section id="introduccion" class="guide-section">
                <h5 class="mt-3">
                    <i class="fas fa-play-circle text-danger"></i>
                    Introducción
                </h5>

                <div class="row">
                    <div class="col-md-8">
                        <p class="text-dark">
                            La <strong>Sección de Gestión de Pases Escolares</strong> forma parte de los procesos
                            institucionales de vigilancia, seguridad y administración escolar. Permite registrar,
                            autorizar, monitorear y notificar las salidas del alumnado durante la jornada regular.
                        </p>

                        <div class="tip-box">
                            <strong><i class="fas fa-lightbulb"></i> Objetivos del Módulo:</strong>
                            <ul class="mt-2 mb-0">
                                <li>Digitalizar completamente el proceso de solicitudes de pase.</li>
                                <li>Garantizar control documental, trazabilidad y seguridad sobre cada salida.</li>
                                <li>Automatizar autorizaciones obligatorias según normativa interna.</li>
                                <li>Notificar a representantes o responsables mediante canales oficiales.</li>
                                <li>Evitar irregularidades, duplicidades y registros sin permisos válidos.</li>
                            </ul>
                        </div>

                        <div class="warning-box">
                            <strong><i class="fas fa-exclamation-circle"></i> Importante:</strong>
                            Toda salida debe cumplir estrictamente con la normativa interna y estar debidamente
                            registrada en el sistema antes de la ejecución del pase.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-door-open fa-3x text-danger mb-3"></i>
                                <h5 class="font-weight-bold">Características Principales</h5>
                                <span class="badge badge-danger feature-badge">Gestión Integral</span>
                                <span class="badge badge-success feature-badge">Autorización Múltiple</span>
                                <span class="badge badge-info feature-badge">Notificación Automática</span>
                                <span class="badge badge-warning feature-badge">Trazabilidad Completa</span>
                                <span class="badge badge-dark feature-badge">Certificados PDF</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Inicio Rápido -->
            <section id="quickstart" class="guide-section">
                <h5>
                    <i class="fas fa-bolt text-warning"></i>
                    Inicio Rápido — Flujo Básico en 4 Pasos
                </h5>

                <div class="row mt-3">
                    @php
                        $steps = [
                            ['num' => 1, 'title' => 'Registrar Pase', 'desc' => 'Crear nueva solicitud de pase.', 'time' => '2-3 minutos', 'badge' => 'primary'],
                            ['num' => 2, 'title' => 'Gestionar Estado', 'desc' => 'Controlar flujo de aprobación.', 'time' => '1 minuto', 'badge' => 'info'],
                            ['num' => 3, 'title' => 'Enviar Notificación', 'desc' => 'Notificar a padres y profesores.', 'time' => '30 segundos', 'badge' => 'success'],
                            ['num' => 4, 'title' => 'Generar PDF', 'desc' => 'Emitir comprobante oficial.', 'time' => '15 segundos', 'badge' => 'warning']
                        ];
                    @endphp

                    @foreach($steps as $s)
                        <div class="col-md-3">
                            <div class="card text-center step-card">
                                <div class="card-body">
                                    <div class="step-number mx-auto mb-3">{{ $s['num'] }}</div>
                                    <h5>{{ $s['title'] }}</h5>
                                    <p class="small">{{ $s['desc'] }}</p>
                                    <span class="badge badge-{{ $s['badge'] }}">{{ $s['time'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="success-box">
                    <strong><i class="fas fa-rocket"></i> Consejos para Nuevos Operadores:</strong>
                    Comience registrando un <strong>pase de prueba</strong> para familiarizarse con los pasos básicos del
                    proceso.
                </div>
            </section>

            <!-- Listado de Pases -->
            <section id="listado-pases" class="guide-section">

                <h5>
                    <i class="fas fa-list text-danger"></i>
                    Sección 1 — Listado y Búsqueda Avanzada de Pases
                </h5>

                <p class="text-dark">
                    Esta sección permite visualizar, filtrar y gestionar los pases registrados. Los filtros permiten
                    segmentar por plan de estudio, grado, sección, profesor, estado y otros criterios.
                </p>

                <!-- Tabla de Filtros -->
                <div class="text-dark font-weight-bold mt-3">Filtros Disponibles</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th width="25%">Filtro</th>
                                <th width="35%">Descripción</th>
                                <th width="20%">Tipo</th>
                                <th width="20%">Uso Recomendado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Búsqueda General</strong></td>
                                <td>Busca por datos relevantes del pase.</td>
                                <td><span class="badge badge-primary">Texto Libre</span></td>
                                <td>Identificación rápida de registros.</td>
                            </tr>
                            <tr>
                                <td><strong>Plan de Estudio</strong></td>
                                <td>Filtra por proyectos académicos asociados.</td>
                                <td><span class="badge badge-info">Selector</span></td>
                                <td>Análisis académico.</td>
                            </tr>
                            <tr>
                                <td><strong>Grado y Sección</strong></td>
                                <td>Segmenta por ubicación académica del estudiante.</td>
                                <td><span class="badge badge-warning">Selector</span></td>
                                <td>Control por grupo escolar.</td>
                            </tr>
                            <tr>
                                <td><strong>Profesor</strong></td>
                                <td>Filtra por docente asignado al pase.</td>
                                <td><span class="badge badge-secondary">Selector</span></td>
                                <td>Seguimiento por docente.</td>
                            </tr>
                            <tr>
                                <td><strong>Estado</strong></td>
                                <td>Filtra por la situación actual del pase.</td>
                                <td><span class="badge badge-success">Selector</span></td>
                                <td>Flujo administrativo.</td>
                            </tr>
                            <tr>
                                <td><strong>Items por Página</strong></td>
                                <td>Controla la cantidad de registros visibles.</td>
                                <td><span class="badge badge-dark">Numérico</span></td>
                                <td>Optimizar navegación.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Explicación de tabla -->
                <div class="alert alert-info mt-3">
                    <strong><i class="fas fa-info-circle"></i> Estructura de la Tabla:</strong>
                    <ul class="mt-2 mb-0">
                        <li><strong>Estudiante:</strong> información general + inscripción.</li>
                        <li><strong>Profesor:</strong> docente relacionado al pase.</li>
                        <li><strong>Asignatura (Pensum):</strong> área académica vinculada.</li>
                        <li><strong>Descripción y Motivo:</strong> detalles de la salida.</li>
                        <li><strong>Fecha y Hora:</strong> momento programado.</li>
                        <li><strong>Estado:</strong> con indicador de notificación.</li>
                        <li><strong>Acciones:</strong> PDF, editar, notificar, eliminar.</li>
                    </ul>
                </div>

            </section>

            <!-- Estados -->
            <section id="gestion-estados" class="guide-section">
                <h5>
                    <i class="fas fa-exchange-alt text-danger"></i>
                    Sección 2 — Gestión de Estados
                </h5>

                <p class="text-dark">
                    Cada pase transita por un conjunto de estados que reflejan su etapa en el flujo administrativo.
                    Existen dos métodos para gestionarlos: cambio rápido y gestión avanzada.
                </p>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card border-left-primary h-100">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="font-weight-bold mb-0">
                                    <i class="fas fa-sync-alt mr-2"></i>Cambio Rápido
                                </h6>
                            </div>
                            <div class="card-body small">
                                <ul class="mb-0">
                                    <li>Selección inmediata desde menú desplegable.</li>
                                    <li>Confirmación automática con SweetAlert.</li>
                                    <li>Actualización instantánea.</li>
                                    <li>Control visual inmediato del cambio.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-left-warning h-100">
                            <div class="card-header bg-warning text-white py-2">
                                <h6 class="font-weight-bold mb-0">
                                    <i class="fas fa-sliders-h mr-2"></i>Gestión Avanzada
                                </h6>
                            </div>
                            <div class="card-body small">
                                <ul class="mb-0">
                                    <li>Permite modificar con más contexto.</li>
                                    <li>Validación adicional de permisos.</li>
                                    <li>Posible historial de cambios (si aplica).</li>
                                    <li>Ideal para revisiones complejas.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-3">
                    <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                    Una vez que un pase está marcado como <strong>notificado</strong>:
                    <ul class="mt-2 mb-0">
                        <li>❌ No puede ser editado</li>
                        <li>❌ No puede ser eliminado</li>
                        <li>❌ No se puede reenviar notificación</li>
                        <li>✔ Sí puede generar PDF</li>
                        <li>✔ Sí puede visualizarse</li>
                        <li>✔ Sí puede cambiar su estado</li>
                    </ul>
                </div>

            </section>

            <!-- Creación y Edición -->
            <section id="creacion-edicion" class="guide-section">
                <h5>
                    <i class="fas fa-edit text-danger"></i>
                    Sección 3 — Registro y Edición de Pases
                </h5>

                <p class="text-dark">
                    El formulario de registro incluye campos obligatorios para garantizar precisión y trazabilidad.
                    Todos los campos cuentan con validación en tiempo real.
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Campo</th>
                                <th>Descripción</th>
                                <th>Obligatorio</th>
                                <th>Validaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Estudiante</strong></td>
                                <td>Búsqueda inteligente por nombre, apellido o CI.</td>
                                <td><span class="badge badge-danger">Sí</span></td>
                                <td>Debe existir y estar inscrito.</td>
                            </tr>
                            <tr>
                                <td><strong>Profesor</strong></td>
                                <td>Docente relacionado.</td>
                                <td><span class="badge badge-danger">Sí</span></td>
                                <td>Debe existir.</td>
                            </tr>
                            <tr>
                                <td><strong>Pensum</strong></td>
                                <td>Asignatura vinculada al pase.</td>
                                <td><span class="badge badge-danger">Sí</span></td>
                                <td>Pertenecer al plan de estudio.</td>
                            </tr>

                            <tr>
                                <td><strong>Tipo y Motivo</strong></td>
                                <td>Clasificaciones internas.</td>
                                <td><span class="badge badge-danger">Sí</span></td>
                                <td>Seleccionados de listas oficiales.</td>
                            </tr>

                            <tr>
                                <td><strong>Destino</strong></td>
                                <td>Lugar específico de salida.</td>
                                <td><span class="badge badge-danger">Sí</span></td>
                                <td>Hasta 255 caracteres.</td>
                            </tr>

                            <tr>
                                <td><strong>Fecha y Hora</strong></td>
                                <td>Momento exacto del pase.</td>
                                <td><span class="badge badge-danger">Sí</span></td>
                                <td>Formato válido.</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Sistema de autorizaciones -->
                <div class="text-dark font-weight-bold mt-4">Autorizaciones Requeridas</div>
                <div class="row mt-2">
                    @php
                        $auths = [
                            ['icon'=>'user-shield','color'=>'primary','title'=>'Guardián / Representante','desc'=>'Autorización familiar obligatoria'],
                            ['icon'=>'chalkboard-teacher','color'=>'warning','title'=>'Profesor','desc'=>'Autorización académica obligatoria'],
                            ['icon'=>'user-tie','color'=>'success','title'=>'Coordinador','desc'=>'Autorización administrativa final']
                        ];
                    @endphp

                    @foreach($auths as $a)
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-{{ $a['icon'] }} fa-2x text-{{ $a['color'] }} mb-3"></i>
                                <h6>{{ $a['title'] }}</h6>
                                <p class="small">{{ $a['desc'] }}</p>
                                <span class="badge badge-{{ $a['color'] }}">Requerida</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </section>

            <!-- Acciones -->
            <section id="acciones" class="guide-section">
                <h5>
                    <i class="fas fa-cogs text-danger"></i>
                    Sección 4 — Acciones Disponibles
                </h5>

                <p class="text-dark">
                    El panel de acciones permite ejecutar operaciones rápidas sobre cada pase registrado:
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Acción</th>
                                <th>Descripción</th>
                                <th>Icono</th>
                                <th>Color</th>
                                <th>Restricciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>PDF</strong></td>
                                <td>Generar comprobante oficial.</td>
                                <td><i class="fas fa-file-pdf"></i></td>
                                <td><span class="badge badge-dark">Negro</span></td>
                                <td>Ninguna</td>
                            </tr>
                            <tr>
                                <td><strong>Estado</strong></td>
                                <td>Gestionar el estado del pase.</td>
                                <td><i class="fas fa-exchange-alt"></i></td>
                                <td><span class="badge badge-info">Azul</span></td>
                                <td>Ninguna</td>
                            </tr>
                            <tr>
                                <td><strong>Editar</strong></td>
                                <td>Modificar información.</td>
                                <td><i class="fas fa-edit"></i></td>
                                <td><span class="badge badge-warning">Amarillo</span></td>
                                <td>No notificado</td>
                            </tr>
                            <tr>
                                <td><strong>Notificar</strong></td>
                                <td>Enviar notificación.</td>
                                <td><i class="fas fa-paper-plane"></i></td>
                                <td><span class="badge badge-success">Verde</span></td>
                                <td>No notificado</td>
                            </tr>
                            <tr>
                                <td><strong>Vista Previa</strong></td>
                                <td>Previsualizar formato oficial.</td>
                                <td><i class="fas fa-eye"></i></td>
                                <td><span class="badge badge-info">Azul Claro</span></td>
                                <td>Ninguna</td>
                            </tr>
                            <tr>
                                <td><strong>Eliminar</strong></td>
                                <td>Eliminar registro.</td>
                                <td><i class="fas fa-trash"></i></td>
                                <td><span class="badge badge-danger">Rojo</span></td>
                                <td>No notificado</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Sistema de confirmaciones -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card border-left-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Acciones Críticas</h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0 small">
                                    <li><strong>Eliminar</strong> pase</li>
                                    <li><strong>Notificar</strong> pase</li>
                                    <li><strong>Cambios de estado importantes</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-check-circle"></i> Acciones Directas</h6>
                            </div>
                            <div class="card-body small">
                                <ul class="mb-0">
                                    <li>Generación de PDF</li>
                                    <li>Vista previa</li>
                                    <li>Edición (si aplica)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flujo de Notificación -->
                <div class="alert alert-info mt-3">
                    <strong><i class="fas fa-bell"></i> Flujo de Notificación:</strong>
                    <ol class="mt-2 mb-0">
                        <li>Creación de pase en estado “Pendiente”.</li>
                        <li>Confirmación de autorizaciones necesarias.</li>
                        <li>Envío de notificación a los responsables.</li>
                        <li>El sistema marca el pase como “Notificado”.</li>
                        <li>Se bloquea edición y eliminación.</li>
                        <li>Disponible únicamente para consulta, reporte o cambio de estado.</li>
                    </ol>
                </div>

            </section>

            <!-- Mejores Prácticas -->
            <section id="mejores-practicas" class="guide-section">
                <h5>
                    <i class="fas fa-star text-danger"></i>
                    Mejores Prácticas y Recomendaciones
                </h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-check-circle"></i> Recomendaciones</h6>
                            </div>
                            <div class="card-body small">
                                <ul class="mb-0">
                                    <li>Verificar la inscripción del estudiante antes del registro.</li>
                                    <li>Completar todos los campos obligatorios sin excepción.</li>
                                    <li>Notificar sólo cuando el pase esté completamente aprobado.</li>
                                    <li>Generar PDF para respaldo institucional.</li>
                                    <li>Revisar estados diariamente.</li>
                                    <li>Actualizar información con responsabilidad.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0"><i class="fas fa-exclamation-circle"></i> Evitar</h6>
                            </div>
                            <div class="card-body small">
                                <ul class="mb-0">
                                    <li>Notificar pases sin autorizaciones completas.</li>
                                    <li>Ingresar información parcial o confusa.</li>
                                    <li>Duplicar pases para la misma fecha y estudiante.</li>
                                    <li>Dejar pases “pendientes” sin seguimiento.</li>
                                    <li>Ignorar alertas del sistema.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            <!-- FAQ -->
            <section id="faq" class="guide-section">
                <h5>
                    <i class="fas fa-question-circle text-danger"></i>
                    Preguntas Frecuentes (FAQ)
                </h5>

                <div class="accordion" id="faqAccordion">

                    <!-- Preguntas automatizadas -->
                    @php
                        $faqs = [
                            [
                                'q' => '¿Cómo crear un nuevo pase escolar?',
                                'a' => 'Haga click en "Nuevo Pase", complete todos los campos requeridos y confirme el registro.'
                            ],
                            [
                                'q' => '¿Por qué no puedo editar o eliminar un pase?',
                                'a' => 'Una vez notificado, el pase queda protegido para mantener integridad documental.'
                            ],
                            [
                                'q' => '¿Cómo cambio el estado de un pase rápidamente?',
                                'a' => 'Use el botón de cambio rápido en la tabla y confirme mediante SweetAlert.'
                            ],
                            [
                                'q' => '¿Qué significan los colores de estado?',
                                'a' => 'Cada badge representa una etapa del flujo administrativo (pendiente, aprobado, rechazado, etc.).'
                            ],
                            [
                                'q' => '¿Cómo buscar un pase específico?',
                                'a' => 'Use la barra de búsqueda combinada con filtros para obtener resultados más precisos.'
                            ]
                        ];
                    @endphp

                    @foreach($faqs as $i => $f)
                    <div class="card">
                        <div class="card-header" id="faq{{ $i }}">
                            <h5 class="mb-0">
                                <button class="btn btn-link {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-toggle="collapse" data-target="#answer{{ $i }}">
                                    <i class="fas fa-question"></i> {{ $f['q'] }}
                                </button>
                            </h5>
                        </div>
                        <div id="answer{{ $i }}" class="collapse {{ $i === 0 ? 'show' : '' }}" data-parent="#faqAccordion">
                            <div class="card-body">
                                {!! $f['a'] !!}
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
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
        border-left: 5px solid #dc3545;
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
        background: #dc3545;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .feature-badge {
        font-size: 0.8rem;
        margin: 0.2rem;
    }

    .tip-box, .warning-box, .success-box {
        border-radius: 0 5px 5px 0;
        padding: 1rem;
        margin: 1rem 0;
    }

    .tip-box {
        background: #e7f3ff;
        border-left: 4px solid #17a2b8;
    }

    .warning-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
    }

    .success-box {
        background: #d4edda;
        border-left: 4px solid #28a745;
    }

</style>
@endsection

@section('scripts')
@parent
<script>
    $(document).ready(function() {
        // Smooth scroll
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 70
                }, 1000);
            }
        });
    });
</script>
@endsection
