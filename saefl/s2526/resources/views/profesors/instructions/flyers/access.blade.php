{{-- ===================================================
     BOTÓN TRIGGER (puede ubicarse donde lo necesites)
   =================================================== --}}
<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#flyerAccesoModulo">
    <i class="fas fa-info-circle"></i>
</button>


{{-- ===================================================
     MODAL OSCURO (Flyer 1)
   =================================================== --}}
<div class="modal fade" id="flyerAccesoModulo" tabindex="-1" role="dialog" aria-labelledby="flyerAccesoModuloLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content bg-dark text-light" style="border-radius: 12px;">

            {{-- Header del Modal --}}
            <div class="modal-header border-secondary">
                <h4 class="modal-title font-weight-bold text-info" id="flyerAccesoModuloLabel">
                    <i class="fas fa-sign-in-alt"></i> Acceso al Módulo de Planificación
                </h4>
                <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Contenido del Flyer --}}
            <div class="modal-body px-5">

                <div class="container-fluid bg-dark text-light py-5" style="min-height: 100vh;">

                    {{-- ENCABEZADO DEL FLYER --}}
                    <div class="row justify-content-center mb-2">
                        <div class="col-lg-8 col-md-10 text-center">
                            <h1 class="font-weight-bold text-info">
                                <i class="fas fa-sign-in-alt"></i> Acceso al Módulo de Planificación
                            </h1>
                            <h5 class="text-light-50">
                                Aprende a ingresar al módulo donde podrás gestionar tus actividades académicas dentro
                                del sistema SAEFL
                            </h5>
                        </div>
                    </div>

                    {{-- TARJETA CENTRAL --}}
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">

                            <div class="card shadow-lg border-0" style="background-color:#1c1f24;">
                                <div class="card-body p-5">

                                    {{-- Objetivo --}}
                                    <h4 class="text-info font-weight-bold">
                                        🎯 Objetivo
                                    </h4>
                                    <p class="text-light">
                                        Ingresar al módulo de actividades para visualizar y gestionar tu planificación.
                                    </p>

                                    {{-- Prerrequisitos --}}
                                    <h4 class="text-warning font-weight-bold mt-4">
                                        ✅ Prerrequisitos
                                    </h4>
                                    <ul class="text-light">
                                        <li>Tener usuario y contraseña del sistema SAEFL</li>
                                        <li>Acceso a Internet</li>
                                    </ul>

                                    {{-- PASOS --}}
                                    <h4 class="text-primary font-weight-bold mt-4">
                                        🚀 Pasos para ingresar
                                    </h4>

                                    <ol class="text-light" style="line-height: 2em; font-size: 1.1rem;">
                                        <li>

                                            <span class="badge badge-light p-2">
                                                <span class="top-right links">
                                                    <a href="#" style="color:#004000">Ingresar</a>
                                                </span>
                                            </span>

                                            
                                            {{-- <span class="badge badge-info p-2">
                                                <i class="fas fa-user-lock"></i> Inicio de sesión
                                            </span> --}}
                                            <br>
                                            Entra a SAEFL a través del enlace (dirección web) y escribe tu usuario y contraseña. Luego haz clic en
                                            <strong>“Ingresar”</strong>.
                                        </li>
                                        <li class="mt-3">

                                            <strong>Sistemas 

                                                → 

                                                <i class="{{ $icon_menus['profesor'] ?? null}} text-primary btn btn-sm btn-light"></i> Profesor

                                                → 

                                                <i class="{{ $icon_menus['activities'] }} fa-1x  btn btn-sm btn-light text-info"></i> Plan de Actividades

                                            </strong>

                                            <br>

                                            <span>
                                                Busca la opción Sistemas, luego haz clic en Profesor y selecciona Plan de Actividades.
                                            </span>
                                        </li>
                                        <li class="mt-3">
                                            <span class="badge badge-success p-2"><i class="fas fa-book"></i></span>
                                             Ver listado
                                            <br>
                                            Se mostrará el listado de áreas de formación asignadas. Haz clic en
                                            <strong>"Actividades"</strong> para ver el detalle del plan.
                                        </li>
                                        <li class="mt-3">
                                            <span class="badge badge-secondary p-2"><i class="fas fa-eye"></i></span>
                                            Confirmación
                                            <br>
                                            Verifica que en el listado se muestren las áreas de formación asignadas, nivel (grado/año), sección y lapso.
                                        </li>
                                    </ol>

                                    {{-- NOTA IMPORTANTE --}}
                                    <div class="alert alert-warning mt-4">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Si el lapso académico ha finalizado, algunas funciones aparecerán bloqueadas
                                        (solo lectura).
                                    </div>

                                </div>
                            </div>

                            {{-- RESULTADO --}}
                            <div class="mt-4 text-center text-light">
                                <h5 class="font-weight-bold text-success">
                                    ✔ Resultado esperado
                                </h5>
                                <p>
                                    Accediste correctamente al módulo de planificación y estás listo para registrar tus actividades.
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
