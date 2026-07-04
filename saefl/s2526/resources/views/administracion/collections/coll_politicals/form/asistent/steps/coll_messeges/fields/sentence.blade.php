<div class="first-of-type" id="step-coll_messeges-sentence">
    <div class="alert alert-secondary rounded flex-center px-4 py-2" style="min-height: 25rem;">
        <div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-7">
                        <h4>Ingrese la solicitud y el '<i>esperando pronta respuesta...</i>' del mensaje de la
                            notificación</h4>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <span class="badge badge-light text-danger font-weight-bold">6</span>
                                        <label for="sentence"
                                            class="font-weight-bold text-secondary m-0">{{ $list_comment['sentence'] ?? '' }}</label>
                                        {!! Form::textarea('sentence', old('sentence'), [
                                            'class' => 'form-control',
                                            'placeholder' => $list_comment['sentence'],
                                            'id' => 'sentence',
                                            'rows' => '4',
                                        ]) !!}
                                        @error('sentence')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <span class="badge badge-light text-danger font-weight-bold">7</span>
                                        <label for="waiting"
                                            class="font-weight-bold text-secondary m-0">{{ $list_comment['waiting'] ?? '' }}</label>
                                        {!! Form::textarea('waiting', old('waiting'), [
                                            'class' => 'form-control',
                                            'placeholder' => $list_comment['waiting'],
                                            'id' => 'waiting',
                                            'rows' => '4',
                                        ]) !!}
                                        @error('waiting')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 class="text-center text-secondary">Modelo guía</h5>
                        @include('administracion.collections.coll_politicals.form.asistent.steps.coll_messeges.modals.sentence')
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior"
            data-step-show="step-coll_messeges-greeting" data-step-hide="step-coll_messeges-sentence"
            data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right"
            value="Siguinte &#10148"data-step-show="step-coll_messeges-footer"
            data-step-hide="step-coll_messeges-sentence" data-direction="up" />
    </div>
</div>
