<div class="first-of-type" id="step-coll_politicals-group">
    <div class="alert alert-secondary rounded flex-center px-4 py-2"  style="min-height: 25rem;">
        <div>
            <h4>Seleccione el grupo al cual va dirigida la notificación y además sí solo se enviará a los deudores del grupo seleccionado</h4>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="canon" class="font-weight-bold text-secondary m-0">{{$list_comment['canon'] ?? ''}}</label>
                            {!! Form::select('canon',$list_coll_politicals_canon,old('canon'),['class'=>'form-control','placeholder'=>'Seleccione']);!!}
                            @error('canon')<span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="status_debts" class="font-weight-bold text-secondary m-0">{{$list_comment['status_debts'] ?? ''}}</label>
                            {!! Form::select('status_debts',['true'=>'Si','false'=>'No'],old('status_debts'),['class'=>'form-control','placeholder'=>'Seleccione']);!!}
                            @error('status_debts')<span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="numbers_bills" class="font-weight-bold text-secondary m-0">{{$list_comment['numbers_bills'] ?? ''}}</label>
                            {!! Form::selectRange('numbers_bills',1,20,old('numbers_bills'),['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="step_form py-2 px-1 font-weight-bold">
        <input type="button" class="clic-step btn btn-light w-25 p-2" value="Anterior" data-step-show="step-coll_politicals-name" data-step-hide="step-coll_politicals-group" data-direction="down" />
        <input type="button" class="clic-step btn btn-info w-75 p-2 float-right" value="Siguinte &#10148"data-step-show="step-coll_politicals-date" data-step-hide="step-coll_politicals-group" data-direction="up" />
    </div>
</div>
