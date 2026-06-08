<div class="first-of-type" id="step-coll_messeges-subject">
    <div class="alert alert-secondary rounded flex-center px-4 py-2" style="min-height: 25rem;">
        <div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-7">
                        <h4>Ingrese el asunto, título y subtítulo del mensaje de la notificación</h4>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <span class="badge badge-light text-danger font-weight-bold">1</span>
                                        <label for="subject"
                                            class="font-weight-bold text-secondary m-0">{{ $list_comment['subject'] ?? '' }}</label>
                                        {!! Form::textarea('subject', old('subject'), [
                                            'class' => 'form-control',
                                            'placeholder' => $list_comment['subject'],
                                            'id' => 'subject',
                                            'rows' => '4',
                                        ]) !!}
                                        @error('subject')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <span class="badge badge-light text-danger font-weight-bold"
                                            float-right>2</span>
                                        <label for="title"
                                            class="font-weight-bold text-secondary m-0">{{ $list_comment['title'] ?? '' }}
                                        </label>
                                        {!! Form::textarea('title', old('title'), [
                                            'class' => 'form-control',
                                            'placeholder' => $list_comment['title'],
                                            'id' => 'title',
                                            'rows' => '4',
                                        ]) !!}
                                        @error('title')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <span class="badge badge-light text-danger font-weight-bold">3</span>
                                        <label for="subtitle"
                                            class="font-weight-bold text-secondary m-0">{{ $list_comment['subtitle'] ?? '' }}</label>
                                        {!! Form::textarea('subtitle', old('subtitle'), [
                                            'class' => 'form-control',
                                            'placeholder' => $list_comment['subtitle'],
                                            'id' => 'subtitle',
                                            'rows' => '4',
                                        ]) !!}
                                        @error('subtitle')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 class="text-center text-secondary">Modelo guía</h5>
                        {{-- <img src="{{ asset('images/help/collection/subject.png') }}" class="img-fluid rounded shadow border" alt=""> --}}
                        @include('administracion.collections.coll_politicals.form.asistent.steps.coll_messeges.modals.subject')
                        {{-- <img class="card-img-top" src="{{ asset('images/help/collection/subject_sm.png') }}"> --}}
                    </div>

                </div>
            </div>


        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior"
            data-step-show="step-coll_politicals-date" data-step-hide="step-coll_messeges-subject"
            data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right"
            value="Siguinte &#10148"data-step-show="step-coll_messeges-greeting"
            data-step-hide="step-coll_messeges-subject" data-direction="up" />
    </div>
</div>
