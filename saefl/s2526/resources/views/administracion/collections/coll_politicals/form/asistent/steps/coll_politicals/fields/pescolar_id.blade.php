<div class="first-of-type" id="step-coll_politicals-pescolar_id">
    <div class="alert alert-secondary rounded flex-center px-4 py-2"  style="min-height: 25rem;">
        <div>
            <h4>Seleccione el período escolar al cual será asociada la notificación</h4>
            <div class="form-group">
                <label for="pescolar_id" class="font-weight-bold text-secondary m-0">{{$list_comment['pescolar_id'] ?? ''}}</label>
                {!! Form::select('pescolar_id',$pescolar_list,old('pescolar_id'),['class'=>'form-control','placeholder'=>'Seleccione']);!!}
                @error('pescolar_id')<span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior" data-step-show="step-start" data-step-hide="step-coll_politicals-pescolar_id" data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Siguinte &#10148"data-step-show="step-coll_politicals-name" data-step-hide="step-coll_politicals-pescolar_id" data-direction="up" />
    </div>
</div>
