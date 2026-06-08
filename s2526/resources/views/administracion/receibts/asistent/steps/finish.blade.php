<div class="first-of-type" id="step-finish">

    <div class="alert alert-secondary rounded flex-center px-4" style="min-height: 25rem;">

        <div class="alert alert-light p-4 shadow ">
            <div class="px-4 flex-center">
                <div>

                    <div class="container-fluid">
                        <div class="row">
                            {{-- <div class="col-3 flex-center">
                                <i class="fa fa-check fa-10x text-success" aria-hidden="true"></i>
                            </div> --}}
                            <div class="col-12 flex-center">

                                {!! Form::submit('Registrar información', ['class' => 'btn btn-primary btn-block', 'id' => 'create']) !!}

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior" data-step-show="step-preview"
            data-step-hide="step-finish" data-direction="down" />
        {{-- <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Inicio &#10148"data-step-show="step-start" data-step-hide="step-finish" data-direction="up" /> --}}
    </div>
</div>
