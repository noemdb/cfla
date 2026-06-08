{{-- ===================================================
     BOTÓN TRIGGER Flyer 4 (Gestión de Indicadores)
   =================================================== --}}
<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#flyerIndicadoresLogro">
    <i class="fas fa-info-circle"></i>
</button>

{{-- ===================================================
     MODAL OSCURO (Flyer 4 — Gestión de Indicadores de Logro)
   =================================================== --}}
<div class="modal fade" id="flyerIndicadoresLogro" tabindex="-1" role="dialog" aria-labelledby="flyerIndicadoresLogroLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content bg-dark text-light" style="border-radius: 12px;">

            {{-- Header --}}
            <div class="modal-header border-secondary">
                <h4 class="modal-title font-weight-bold text-success" id="flyerIndicadoresLogroLabel">
                    <i class="fas fa-chart-line"></i> Gestión de Indicadores de Logro
                </h4>
                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Contenido --}}
            <div class="modal-body px-5">
                <div class="container-fluid py-4">

                    {{-- ENCABEZADO --}}
                    <div class="row justify-content-center mb-3">
                        <div class="col-lg-8 col-md-10 text-center">
                            <h2 class="font-weight-bold text-success">Gestión de Indicadores de Logro</h2>
                            <p class="text-muted">Aprende a registrar y vincular los indicadores que reflejan los logros esperados en tus actividades académicas.</p>
                        </div>
                    </div>

                    {{-- TARJETA CENTRAL --}}
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">

                            <div class="card shadow-lg border-0" style="background-color:#1c1f24;">
                                <div class="card-body p-5">

                                    {{-- Objetivo --}}
                                    <h4 class="text-success font-weight-bold">🎯 Objetivo</h4>
                                    <p>Registrar y asociar indicadores de logro a las actividades académicas para guiar la evaluación del aprendizaje.</p>

                                    {{-- Prerrequisitos --}}
                                    <h4 class="text-info font-weight-bold mt-4">✅ Prerrequisitos</h4>
                                    <ul>
                                        <li>Acceder al módulo de planificación (Flyer 1)</li>
                                        <li>Contar con al menos una actividad creada (Flyer 2 o 3)</li>
                                    </ul>

                                    {{-- PASOS --}}
                                    <h4 class="text-primary font-weight-bold mt-4">🚀 Pasos para gestionar indicadores</h4>
                                    <ol style="line-height: 2em; font-size: 1.1rem;">
                                        <li>
                                            <strong>Seleccionar la actividad:</strong> En el listado de actividades,
                                            para abrir la sección correspondiente.
                                        </li>

                                        <li class="mt-2">
                                            <strong>Agregar un nuevo indicador:</strong> Presiona
                                            <span class="badge badge-info"><i class="fas fa-plus"></i> Nuevo Indicador</span>
                                            y completa los siguientes campos:
                                            <ul>
                                                <li>Descripción del indicador de logro</li>
                                                <li>Nivel de desempeño o logro</li>
                                                <li>Instrumento de evaluación (si aplica)</li>
                                            </ul>
                                        </li>

                                        <li class="mt-2">
                                            <strong>Guardar indicador:</strong> Haz clic en
                                            <span class="badge badge-success"><i class="fas fa-save"></i> Guardar</span>
                                            para asociarlo a la actividad seleccionada.
                                        </li>

                                        <li class="mt-2">
                                            <strong>Editar o eliminar:</strong> Puedes modificar un indicador haciendo clic en
                                            <span class="badge badge-warning"><i class="fas fa-edit"></i> Editar</span>
                                            o eliminarlo con
                                            <span class="badge badge-danger"><i class="fas fa-trash-alt"></i> Eliminar</span>.
                                        </li>

                                        <li class="mt-2">
                                            <strong>Verificación:</strong> Comprueba que los indicadores se muestran correctamente en la lista de la actividad.
                                        </li>
                                    </ol>

                                    {{-- NOTA IMPORTANTE --}}
                                    <div class="alert alert-warning mt-4">
                                        <i class="fas fa-lightbulb"></i>
                                        Los indicadores deben redactarse en términos de aprendizaje observable: “El estudiante logra...”, “El estudiante aplica...”.
                                    </div>

                                </div>
                            </div>

                            {{-- RESULTADO --}}
                            <div class="mt-4 text-center text-light">
                                <h5 class="font-weight-bold text-success">✔ Resultado esperado</h5>
                                <p>Los indicadores de logro quedan correctamente asociados a las actividades, listos para ser evaluados y reportados.</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
