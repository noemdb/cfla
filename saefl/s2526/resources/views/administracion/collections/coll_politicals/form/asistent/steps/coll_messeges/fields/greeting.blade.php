<div class="first-of-type" id="step-coll_messeges-greeting">
    <div class="alert alert-secondary rounded flex-center px-4 py-2" style="min-height: 25rem;">
        <div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-7">
                        <h4>Ingrese el saludo formal y el considerando del mensaje de la notificación</h4>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <span class="badge badge-light text-danger font-weight-bold">4</span>
                                        <label for="greeting"
                                            class="font-weight-bold text-secondary m-0">{{ $list_comment['greeting'] ?? '' }}</label>
                                        {!! Form::textarea('greeting', old('greeting'), [
                                            'class' => 'form-control',
                                            'placeholder' => $list_comment['greeting'],
                                            'id' => 'greeting',
                                            'rows' => '4',
                                        ]) !!}
                                        @error('greeting')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <span class="badge badge-light text-danger font-weight-bold">5</span>
                                        <label for="consider"
                                            class="font-weight-bold text-secondary m-0">{{ $list_comment['consider'] ?? '' }}</label>
                                        {!! Form::textarea('consider', old('consider'), [
                                            'class' => 'form-control',
                                            'placeholder' => $list_comment['consider'],
                                            'id' => 'consider',
                                            'rows' => '4',
                                        ]) !!}
                                        @error('consider')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 class="text-center text-secondary">Modelo guía</h5>
                        {{-- <img src="{{ asset('images/help/collection/greeting.png') }}" class="img-fluid rounded shadow border" alt=""> --}}
                        @include('administracion.collections.coll_politicals.form.asistent.steps.coll_messeges.modals.greeting')
                    </div>

                </div>
            </div>


        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior"
            data-step-show="step-coll_messeges-subject" data-step-hide="step-coll_messeges-greeting"
            data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right"
            value="Siguinte &#10148"data-step-show="step-coll_messeges-sentence"
            data-step-hide="step-coll_messeges-greeting" data-direction="up" />
    </div>
</div>
