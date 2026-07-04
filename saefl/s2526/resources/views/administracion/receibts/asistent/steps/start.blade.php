@php
    $stepShow = [
        // 'up'=>'step-coll_promises-coll_political_id',
        'up'=>'step-recibo_cashes',
        'dowm'=>'step-start',
    ] ;
    $stepId = "step-start";
@endphp

<div id="{{$stepId}}">
    <div class="alert alert-secondary rounded flex-center px-4 py-2"  style="min-height: 25rem;">
        <div class="">
            <h1 class="display-4">Hola {{Auth::user()->fullname}}. Bienvenido!</h1>
            <p class="lead">
                Este asistente le guiará para lograr el registro de un recibo rápido de pago.
            </p>
            <hr class="my-4">
            <p>Todas las instrucciones que necesites serán mostradas en pantalla. Tendrás toda la información requira por el representante.</p>
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
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Siguinte &#10148"data-step-show="{{$stepShow['up'] ?? ''}}" data-step-hide="{{$stepId ?? ''}}" data-direction="up" />
    </div>
</div>

