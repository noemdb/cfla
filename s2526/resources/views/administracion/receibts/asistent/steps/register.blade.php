@php
    $stepShow = [
        'up' => 'step-finish',
        'dowm' => 'step-recibo_pagos',
    ];
    $stepId = 'step-register';
@endphp


<div class="first-of-type" id="{{ $stepId }}">

    <div class="alert alert-secondary rounded flex-center px-4" style="min-height: 25rem;">

        <div class="alert alert-light p-4 shadow ">
            <div class="px-4 flex-center">
                <div>

                    <div class="container-fluid">
                        <div class="row">

                            <div class="col-12 flex-center">

                                {!! Form::submit('Registrar información', ['class' => 'btn btn-primary btn-block', 'id' => 'create']) !!}

                            </div>
                        </div>

                        {{-- @include('administracion.collections.coll_promises.form.asistent.steps.modals.preview') --}}

                    </div>

                </div>

            </div>
        </div>

    </div>

    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior"
            data-step-show="{{ $stepShow['dowm'] ?? '' }}" data-step-hide="{{ $stepId ?? '' }}" data-direction="down" />
        {{-- <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Siguinte &#10148"data-step-show="{{$stepShow['up'] ?? ''}}" data-step-hide="{{$stepId ?? ''}}" data-direction="up" /> --}}
    </div>
</div>
