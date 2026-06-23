<div class="first-of-type" id="step-coll_messeges-footer">
    <div class="alert alert-secondary rounded flex-center px-4 py-2" style="min-height: 25rem;">
        <div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-7">
                        <h4>Ingrese pie de página del mensaje de la notificación</h4>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <span class="badge badge-light text-danger font-weight-bold">8</span>
                                        <label for="footer"
                                            class="font-weight-bold text-secondary m-0">{{ $list_comment['footer'] ?? '' }}</label>
                                        {!! Form::textarea('footer', old('footer'), [
                                            'class' => 'form-control',
                                            'placeholder' => $list_comment['footer'],
                                            'id' => 'footer',
                                            'rows' => '4',
                                        ]) !!}
                                        @error('footer')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 class="text-center text-secondary">Modelo guía</h5>
                        @include('administracion.collections.coll_politicals.form.asistent.steps.coll_messeges.modals.footer')
                    </div>

                </div>
            </div>


        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior"
            data-step-show="step-coll_messeges-sentence" data-step-hide="step-coll_messeges-footer"
            data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right"
            value="Siguinte &#10148"data-step-show="step-preview" data-step-hide="step-coll_messeges-footer"
            data-direction="up" />
    </div>
</div>
