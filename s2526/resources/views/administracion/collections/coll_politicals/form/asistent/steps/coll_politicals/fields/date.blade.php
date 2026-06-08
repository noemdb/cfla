<div class="first-of-type" id="step-coll_politicals-date">
    <div class="alert alert-secondary rounded flex-center px-4 py-2"  style="min-height: 25rem;">
        <div>
            <h4>Seleccione la fecha de inicio y de finalización para programar en envío automatizado de la notificación.</h4>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="finicial" class="font-weight-bold text-secondary m-0">{{$list_comment['finicial'] ?? ''}}</label>
                            {!! Form::date('finicial', old('finicial'),['class'=>'form-control','placeholder'=>$list_comment['finicial']]) !!}
                            @error('finicial')<span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="ffinal" class="font-weight-bold text-secondary m-0">{{$list_comment['ffinal'] ?? ''}}</label>
                            {!! Form::date('ffinal', old('ffinal'),['class'=>'form-control','placeholder'=>$list_comment['ffinal']]) !!}
                            @error('ffinal')<span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior" data-step-show="step-coll_politicals-group" data-step-hide="step-coll_politicals-date" data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Siguinte &#10148"data-step-show="step-coll_messeges-subject" data-step-hide="step-coll_politicals-date" data-direction="up" />
    </div>
</div>
