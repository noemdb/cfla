{!! Form::open([
    'route' => 'administracion.receibts.recibos.index',
    'method' => 'GET',
    'class' => 'pb-2',
    'role' => 'search',
]) !!}

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-5 px-1">
            <div class="form-group">
                <label for="representant_id" class="m-0 font-weight-bold text-muted">Representante</label>
                <div class="input-group pb-3">
                    <div class="input-group-append w-25" style="z-index: 0;">
                        {!! Form::text('help_representante', $help_representante, [
                            'class' => 'form-control',
                            'placeholder' => 'CI o nombre',
                            'id' => 'help_representante',
                        ]) !!}
                    </div>
                    {!! Form::select('representant_id', $list_representant, $representant_id, [
                        'class' => 'form-control w-75',
                        'id' => 'representant_id',
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="col-sm-2 px-1">
            <div class="form-group">
                <label for="num_caashs" class="m-0 font-weight-bold text-muted">Cant. de billetes</label>
                {!! Form::selectRange('num_caashs', 1, 10, $num_caashs, [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-2 px-1">
            <div class="form-group">
                <label for="num_changes" class="m-0 font-weight-bold text-muted">Cant. de billetes vuelto</label>
                {!! Form::selectRange('num_changes', 1, 10, $num_changes, [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-2 px-1">
            <div class="form-group">
                <label for="num_pagos" class="m-0 font-weight-bold text-muted">Cant. de cuotas</label>
                {!! Form::selectRange('num_pagos', 1, 10, $num_pagos, [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione',
                    'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-1 px-1">
            <div class="form-group">
                <br>
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
