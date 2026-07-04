<div class="card p-0 m-1">
    <div class="card-title p-1 alert alert-secondary font-weight-bolder pb-0 mb-0">
        <i class="{{ $icon_menus['banco'] ?? '' }} fa-1x"></i>
        Información bancaria de las transacciones
    </div>
    <div class="card-body p-1 m-1">
        <div class="card-text">
            {{-- <div class="container p-1"> --}}
                <div class="form-group row pb-1 ml-1 mr-1">
                    <label for="inputName" class="col-sm-1-12 col-form-label"></label>
                    <div class="col-6 pr-0">
                        <label for="finicial" class="m-0 font-weight-bold" title="Fecha inicial de las operaciones bancarias">Fecha inicial</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <i class="far fa-calendar"></i>
                                </span>
                            </div>
                            {!! Form::date('finicial', $finicial,['class'=>'form-control','id'=>'finicial']) !!}
                        </div>
                    </div>
                    <div class="col-6 pr-0">
                        <label for="ffinal" class="m-0 font-weight-bold" title="Fecha final de las operaciones bancarias">Fecha final</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <i class="far fa-calendar"></i>
                                </span>
                            </div>
                            {!! Form::date('ffinal', $ffinal,['class'=>'form-control','id'=>'ffinal']) !!}
                        </div>
                    </div>
                </div>
            {{-- </div> --}}
        </div>
    </div>
</div>

<button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
    <i class="fa fa-search" aria-hidden="true"></i>
    Buscar
</button>
