@php
    $stepShow = [
        'up'=>'step-representant-ident-1',
        'dowm'=>'step-representant-personal',
    ] ;
    $stepId = "step-representant-contact";
@endphp


<div class="first-of-type" id="{{$stepId}}">
    <div class="alert alert-secondary rounded flex-center px-4 py-2"  style="min-height: 25rem;">
        <div>
            {{-- <h4>Seleccione la política de cobro a la cual será asociada la promesa de pago</h4> --}}
            @include('administracion.collections.coll_promises.form.asistent.steps.representant.partials.contact')
        </div>
    </div>

    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior" data-step-show="{{$stepShow['dowm'] ?? ''}}" data-step-hide="{{$stepId ?? ''}}" data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Siguinte &#10148"data-step-show="{{$stepShow['up'] ?? ''}}" data-step-hide="{{$stepId ?? ''}}" data-direction="up" />
    </div>
</div>

