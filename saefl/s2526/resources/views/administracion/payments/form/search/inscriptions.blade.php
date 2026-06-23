<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}

    <div class="form-row">
        <div class="col-sm-3">
            <label class="font-weight-bold my-0 py-0" for="type_pay">Fecha Inicial</label>
            {!! Form::date('finicial', $finicial, [
                'class' => 'form-control alert-secondary font-weight-bold',
                'id' => 'finicial',
            ]) !!}
        </div>
        <div class="col-sm-3">
            <label class="font-weight-bold my-0 py-0" for="type_pay">Fecha Final</label>
            {!! Form::date('ffinal', $ffinal, ['class' => 'form-control', 'id' => 'ffinal']) !!}
        </div>
        <div class="col-sm-3">
            <label class="font-weight-bold my-0 py-0" for="type_pay">Banco</label>
            {!! Form::select('banco_id', $list_banco, $banco_id, [
                'class' => 'form-control',
                'id' => 'banco_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        {{-- <div class="col-sm-3">
                <label class="font-weight-bold my-0 py-0" for="type_pay">Estado</label>
                {!! Form::select('status_apply',['1'=>'APLICADO','0'=>'NO APLICADO'],$status_apply,['class' =>'form-control','id'=>'status_apply_id','placeholder'=>'Seleccione']) !!}
            </div> --}}
    </div>

    <div class="form-row pt-2">

        <div class="col-sm-3">
            <label class="font-weight-bold my-0 py-0" for="type_pay">Identificador</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="{{ $icon_menus['profile'] ?? '' }} fa-1x"></i>
                    </span>
                </div>
                {!! Form::text('ci_representant', $ci_representant, [
                    'class' => 'form-control',
                    'placeholder' => 'Identificador',
                    'id' => 'ci_representant',
                    'maxlength' => '10',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-3">
            <label class="font-weight-bold my-0 py-0" for="type_pay">Referencia</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="{{ $icon_menus['banco'] ?? '' }} fa-1x"></i>
                    </span>
                </div>
                {!! Form::text('number_i_pay', $number_i_pay, [
                    'class' => 'form-control',
                    'placeholder' => 'Referencia',
                    'id' => 'number_i_pay',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-3">
            <label class="font-weight-bold my-0 py-0" for="type_pay">Tipo de pago</label>
            <div class=" form-control">
                {{ $type_pay ?? '' }}
            </div>
        </div>

        <div class="col-sm-3">
            <br>
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
                @if (!empty($btn_toprint))
                    <a id="btn_toprint" class="btn btn-dark" href="#" role="button" title="Generar PDF">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
