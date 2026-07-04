@component('elements.widgets.modal')
    @slot('classH','secondary')
    @slot('id','modal_search')
    @slot('title','Más opciones')
    @slot('close',true)
    @slot('size','modal-md')
    @slot('body')
        <div class="form-row font-weight-bold">
            <div class="col-5">Fecha Inicial</div>
            <div class="col-5">Fecha Final</div>
            <div class="col-2">&nbsp;</div>
        </div>
        <div class="form-row">

            <div class="col-5">
                {!! Form::date('finicial', $finicial,['class'=>'form-control','id'=>'finicial']) !!}
            </div>

            <div class="col-5">
                {!! Form::date('ffinal', $ffinal,['class'=>'form-control','id'=>'ffinal']) !!}
            </div>

            <div class="col-2">
                <div class="btn-group btn-group">
                    <button class="btn btn-primary my-2 my-sm-0" type="submit">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

        </div>
    @endslot
@endcomponent
