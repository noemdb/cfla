{{-- ===================================================
     BOTÓN TRIGGER Flyer 2 (Registrar Actividad)
   =================================================== --}}
<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#flyerRegistrarActividad">
    <i class="fas fa-info-circle"></i>
</button>

{{-- ===================================================
     MODAL OSCURO (Flyer 2 — Registrar Actividad)
   =================================================== --}}
<div class="modal fade" id="flyerRegistrarActividad" tabindex="-1" role="dialog" aria-labelledby="flyerRegistrarActividadLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content bg-dark text-light" style="border-radius: 12px;">

            {{-- Header --}}
            <div class="modal-header border-secondary">
                <h4 class="modal-title font-weight-bold text-info" id="flyerRegistrarActividadLabel">
                    <i class="fas fa-plus-circle"></i> Registrar una Actividad Académica
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
                            <h2 class="font-weight-bold text-info">Registrar una Actividad Académica</h2>
                            <p class="text-muted">Aprende a agregar nuevas actividades al plan académico.</p>
                        </div>
                    </div>

                    {{-- TARJETA CENTRAL --}}
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">

                            <div class="card shadow-lg border-0" style="background-color:#1c1f24;">
                                <div class="card-body p-5">

                                    <h4 class="text-info font-weight-bold">🎯 Objetivo</h4>
                                    <p>Registrar una nueva actividad dentro del Plan de Actividades.</p>

                                    <h4 class="text-warning font-weight-bold mt-4">✅ Prerrequisitos</h4>
                                    <ul>
                                        <li>Haber accedido al módulo del plan de actividades (Flyer 1)</li>
                                        <li>Visualizar el listado de áreas de formación</li>
                                    </ul>

                                    <h4 class="text-primary font-weight-bold mt-4">🚀 Pasos para registrar una actividad</h4>

                                    <ol style="line-height: 2em; font-size: 1.1rem;">
                                        <li><strong>Selecciona el área de formación:</strong> Haz clic en <i class="{{ $icon_menus['activities'] }} fa-1x  btn btn-sm btn-light text-info"></i></li>

                                        <li><strong>Crear actividad:</strong> Haz clic en <i class="fas fa-plus-circle btn btn-primary btn-sm" aria-hidden="true"></i> Registrar Actividad.</li>

                                        <li><strong>Completa los datos:</strong> Llena los campos requeridos (Fecha Inicial, Fecha Final, Actividad Evaluativa, Tema generador y Énfasis, etc).</li>

                                        <li><strong>Guardar actividad:</strong> Presiona <span class="badge badge-primary">
											<button class="form-control btn btn-primary btn-sm" wire:click="save()" type="button">Guardar</button>
                                        </span>.</li>

                                        <li><strong>Verificación:</strong> La actividad aparecerá en el listado.</li>
                                    </ol>

                                    <div class="alert alert-warning mt-4">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Si el lapso está cerrado, el sistema no permitirá agregar nuevas actividades.
                                    </div>

                                </div>
                            </div>

                            <div class="mt-4 text-center text-light">
                                <h5 class="font-weight-bold text-success">✔ Resultado esperado</h5>
                                <p>La nueva actividad se registró correctamente dentro del plan.</p>
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
