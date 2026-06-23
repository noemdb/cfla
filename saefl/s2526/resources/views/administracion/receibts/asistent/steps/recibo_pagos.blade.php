@php
    $stepShow = [
        'up'=>'step-register',
        'dowm'=>'step-recibo_changes',
    ] ;
    $stepId = "step-recibo_pagos";
@endphp

<div class="first-of-type" id="{{$stepId}}">

    <div class="alert alert-secondary rounded flex-center px-4 py-2"  style="min-height: 25rem;">
        <div class="w-100">
            <h4>Ingrese los datos de las cuotas</h4>
            @include('administracion.receibts.form.recibo_pagos',['list_comment'=>$list_comment_arr['recibo_pagos']])
        </div>
    </div>

    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior" data-step-show="{{$stepShow['dowm'] ?? ''}}" data-step-hide="{{$stepId ?? ''}}" data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Siguinte &#10148"data-step-show="{{$stepShow['up'] ?? ''}}" data-step-hide="{{$stepId ?? ''}}" data-direction="up" />
    </div>
</div>

