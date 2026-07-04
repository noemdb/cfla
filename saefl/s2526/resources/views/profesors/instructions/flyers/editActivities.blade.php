{{-- ===================================================
     BOTÓN TRIGGER Flyer 3 (Editar/Actualizar Actividad)
   =================================================== --}}
<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#flyerEditarActividad">
    <i class="fas fa-info-circle"></i>
</button>

{{-- ===================================================
     MODAL OSCURO (Flyer 3 — Editar/Actualizar Actividad)
   =================================================== --}}
<div class="modal fade" id="flyerEditarActividad" tabindex="-1" role="dialog" aria-labelledby="flyerEditarActividadLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content bg-dark text-light" style="border-radius: 12px;">

            {{-- Header --}}
            <div class="modal-header border-secondary">
                <h4 class="modal-title font-weight-bold text-warning" id="flyerEditarActividadLabel">
                    <i class="fas fa-edit"></i> Editar o Actualizar una Actividad.
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
                            <h2 class="font-weight-bold text-warning">Editar o Actualizar una Actividad.</h2>
                            <p class="text-muted">Aprende a modificar los datos de una actividad registrada en el plan académico.</p>
                        </div>
                    </div>

                    {{-- TARJETA CENTRAL --}}
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">

                            <div class="card shadow-lg border-0" style="background-color:#1c1f24;">
                                <div class="card-body p-5">

                                    {{-- Objetivo --}}
                                    <h4 class="text-warning font-weight-bold">🎯 Objetivo</h4>
                                    <p>Actualizar la información de una actividad existente para mantener el plan académico actualizado.</p>

                                    {{-- Prerrequisitos --}}
                                    <h4 class="text-info font-weight-bold mt-4">✅ Prerrequisitos</h4>
                                    <ul>
                                        <li>Haber registrado al menos una actividad (Flyer 2)</li>
                                        <li>Acceso al módulo de actividades</li>
                                    </ul>

                                    {{-- Pasos --}}
                                    <h4 class="text-primary font-weight-bold mt-4">🚀 Pasos para editar una actividad</h4>
                                    <ol style="line-height: 2em; font-size: 1.1rem;">
                                        <li>
                                            <strong>Selecciona la actividad:</strong>
                                            En el listado de actividades, haz clic en
                                            <span class="badge badge-warning"><i class="fas fa-edit"></i> Editar</span>
                                            junto a la actividad que deseas modificar.
                                        </li>

                                        <li class="mt-2">
                                            <strong>Actualizar información:</strong>
                                            Modifica los campos necesarios:
                                            <ul>
                                                <li>Fechas (inicio/fin)</li>
                                                <li>Tema generador/Énfasis</li>
                                                <li>Contenidos pedagógicos y evaluación</li>
                                            </ul>
                                        </li>

                                        <li class="mt-2">
                                            <strong>Guardar cambios:</strong>
                                            Haz clic en
                                            <span class="badge badge-warning"><i class="fas fa-save"></i> Guardar</span>
                                            para confirmar los cambios.
                                        </li>

                                        <li class="mt-2">
                                            <strong>Verificación:</strong>
                                            Asegúrate de que los cambios se reflejen correctamente en el listado de actividades.
                                        </li>
                                    </ol>

                                    {{-- Nota Importante --}}
                                    <div class="alert alert-warning mt-4">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Si el lapso académico ha finalizado, la edición estará deshabilitada (solo lectura).
                                    </div>

                                </div>
                            </div>

                            {{-- Resultado --}}
                            <div class="mt-4 text-center text-light">
                                <h5 class="font-weight-bold text-success">✔ Resultado esperado</h5>
                                <p>
                                    La actividad ha sido actualizada correctamente y la información está lista para generar reportes y PDFs.
                                </p>
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
