<main role="main" class="col-md-12 ml-sm-auto col-lg-12 px-0">
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="jumbotron alert-secondary py-2 mb-2">
            <h3 class="mb-1"><i class="fas fa-shield-alt text-danger"></i>Gestión de Anulación de Pagos</h3>
            <p class="lead mb-0">Proceso administrativo para habilitar / reversar anulaciones de recibos de pago</p>
            <hr class="my-1 bg-white">
            <p class="small mb-0">Tiempo estimado de lectura: 4 min</p>
        </div>

        <div class="row">

            {{-- CONTENIDO --}}
            <div class="col-md-12">
                {{-- INTRO --}}
                <section id="intro" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-shield-alt text-danger"></i> Introducción</h5>
                    <p class="small">Esta pantalla permite <strong>auditar</strong> y <strong>autorizar</strong>
                        futuras anulaciones de recibos antes de que se ejecuten, garantizando trazabilidad y control
                        administrativo.</p>

                    <div class="alert alert-info py-1 small">
                        <strong><i class="fas fa-lightbulb"></i> ¿Por qué “marcar anulable”?</strong> Evita anulaciones
                        accidentales; solo los usuarios autorizados podrán anular después de esta habilitación.
                    </div>
                </section>

                {{-- 1. FILTRAR --}}
                <section id="filtros" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-filter text-primary"></i> 1. Filtrar pagos</h5>

                    <table class="table table-borderless table-sm small">
                        <thead class="thead-light">
                            <tr>
                                <th>Campo</th>
                                <th>Uso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><kbd>Buscar</kbd></td>
                                <td>Estudiante, cédula, representante o concepto.</td>
                            </tr>
                            <tr>
                                <td><kbd>Fecha inicio / fin</kbd></td>
                                <td>Rango de registro de pago.</td>
                            </tr>
                            <tr>
                                <td><kbd>Estado</kbd></td>
                                <td>
                                    <span class="badge badge-success feature-badge">Activo</span>
                                    <span class="badge badge-warning feature-badge">Anulable</span>
                                    <span class="badge badge-danger feature-badge">Anulado</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="alert alert-light border-left-primary py-1 small">
                        <strong>Tip:</strong> Use <kbd>Ctrl</kbd> + <kbd>F</kbd> para saltar al cuadro de búsqueda.
                    </div>
                </section>

                {{-- 2. REVISAR --}}
                <section id="revisar" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-info-circle text-info"></i> 2. Revisar detalle del pago</h5>
                    <p class="small">Presione <button class="btn btn-xs btn-info"><i class="fas fa-info"></i></button>
                        para abrir modal con:</p>
                    <ul class="small">
                        <li>Datos del estudiante y representante</li>
                        <li>Concepto y monto cancelado</li>
                        <li>Usuario que registró el pago</li>
                        <li>Estado actual (activo / anulado / anulable)</li>
                    </ul>
                </section>

                {{-- 3. MARCAR ANULABLE --}}
                <section id="anulable" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-unlock text-warning"></i> 3. Marcar pago como “anulable”</h5>

                    <ol class="small">
                        <li>Click en <button class="btn btn-xs btn-warning"><i class="fas fa-unlock"></i></button></li>
                        <li>Confirme estudiante y concepto.</li>
                        <li>Escriba <strong>justificación</strong> (10-500 caracteres).</li>
                        <li>Presione <span class="badge badge-warning">Marcar como anulable</span>.</li>
                    </ol>

                    <div class="alert alert-light border-left-warning py-1 small">
                        El pago ahora aparece con etiqueta <span class="badge badge-warning">Anulable</span> y puede ser
                        anulado desde la sección "Listado de pagos registrados".
                    </div>
                </section>

                {{-- 4. REVERTIR --}}
                <section id="revertir" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-lock text-secondary"></i> 4. Quitar estado “anulable”</h5>
                    <p class="small">Si se equivocó o ya no desea permitir la anulación:</p>
                    <ul class="small">
                        <li>Click en <button class="btn btn-xs btn-secondary"><i class="fas fa-lock"></i></button></li>
                        <li>Confirme la acción en el cuadro de diálogo.</li>
                    </ul>
                    <div class="alert alert-light border-left-secondary py-1 small">No disponible si el pago ya fue
                        anulado.</div>
                </section>

                {{-- FAQ --}}
                <section id="faq" class="guide-section py-2">
                    <h5 class="mb-2"><i class="fas fa-question-circle text-primary"></i> Preguntas frecuentes</h5>

                    <div class="accordion" id="accordionFaq">
                        <div class="card border-0">
                            <div class="card-header p-1" id="fq1">
                                <button class="btn btn-sm btn-link" data-toggle="collapse" data-target="#ans1">
                                    ¿Puedo anular directamente desde esta pantalla?
                                </button>
                            </div>
                            <div id="ans1" class="collapse show" data-parent="#accordionFaq">
                                <div class="card-body py-1 small">No; aquí solo se <strong>autoriza</strong> la
                                    anulación. El proceso de anular se realiza desde “Registros de pago” una vez marcado
                                    como anulable.</div>
                            </div>
                        </div>

                        <div class="card border-0">
                            <div class="card-header p-1" id="fq2">
                                <button class="btn btn-sm btn-link collapsed" data-toggle="collapse"
                                    data-target="#ans2">
                                    ¿Qué significa “Pendiente Aprobación”?
                                </button>
                            </div>
                            <div id="ans2" class="collapse" data-parent="#accordionFaq">
                                <div class="card-body py-1 small">El pago fue anulado pero falta la aprobación final de
                                    un usuario con rol superior.</div>
                            </div>
                        </div>
                    </div>{{-- /accordion --}}

                    <button type="button" class="btn btn-success" onclick="window.open('{{ route('helpers.pdf.cancelations') }}', '_blank')">
                        <i class="fas fa-print"></i> Versión Imprimible
                    </button>
                </section>

            </div>{{-- /col-md-9 --}}
        </div>{{-- /row --}}
    </div>{{-- /container-fluid --}}
</main>

{{-- SCRIPTS OPCIONALES --}}
@section('stylesheet')
    @parent
    <style>
        .guide-section {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: .75rem;
            margin-bottom: .5rem;
            border-radius: .25rem;
        }

        .nav-guide {
            position: sticky;
            top: 20px
        }
    </style>
@endsection
