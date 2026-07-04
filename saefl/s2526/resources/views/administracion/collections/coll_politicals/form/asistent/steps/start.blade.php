<div id="step-start">
    <div class="alert alert-secondary rounded flex-center px-4 py-2"  style="min-height: 25rem;">
        <div class="">
            <h1 class="display-4">Hola {{Auth::user()->fullname}}. Bienvenido!</h1>
            <p class="lead">
                Este asistente le guiará para lograr con éxito el envío de notificaciones masivas automatizadas a los representantes a través del correo electrónico.
                Al finalizar el SAEFL quedará configurado para realizar estas actividades autonómicamente.
            </p>
            <hr class="my-4">
            <p>Todas las instrucciones que necesites serán mostradas en pantalla. Tendrás toda la información.</p>
            <p>Cuando estés listo, pulsa el botón <b>Comenzar</b> para iniciar.</p>

            <div class="d-flex flex-row-reverse">
                <div class="alert alert-light shadow-sm p-2 m-2 w-25 " role="alert">
                    <div class="lead font-weight-bold">Estás listo? Comencemos!!!</div>
                </div>
            </div>

            {{-- <p class="text-right display-4"></p> --}}
        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" name="previous" class="previous-step btn btn-light  w-25 p-2" value="Inicio" disabled/>
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Comenzar &#10148" data-step-show="step-coll_politicals-pescolar_id" data-step-hide="step-start" data-direction="up" />
    </div>
</div>

