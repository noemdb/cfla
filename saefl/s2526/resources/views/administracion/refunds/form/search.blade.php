<div class="card-header p-1 m-1 mb-3">
    <div class="form-row">
        <div class="col-2">
            <label for="number_i_pay" class="m-0">F. Inicial</label>
            {!! Form::date('finicial', !empty($finicial) ? $finicial : null, [
                'class' => 'form-control',
                'id' => 'finicial',
            ]) !!}
        </div>
        <div class="col-2">
            <label for="number_i_pay" class="m-0">F. Final</label>
            {!! Form::date('ffinal', !empty($ffinal) ? $ffinal : null, ['class' => 'form-control', 'id' => 'ffinal']) !!}
        </div>
        <div class="col-2">
            <label for="number_i_pay" class="m-0">Banco</label>
            {!! Form::select('banco_id', $list_banco, !empty($banco_id) ? $banco_id : null, [
                'class' => 'form-control',
                'id' => 'banco_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            <label for="representant_ci" class="m-0">Cédula</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="{{ $icon_menus['profile'] ?? '' }} fa-1x"></i>
                    </span>
                </div>
                {!! Form::text('representant_ci', !empty($representant_ci) ? $representant_ci : null, [
                    'class' => 'form-control',
                    'placeholder' => 'Identificador',
                    'id' => 'representant_ci',
                    'maxlength' => '10',
                ]) !!}
            </div>
        </div>
        <div class="col-2">
            <div class="input-group">
                <label for="number_i_pay" class="m-0">Referencia</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="{{ $icon_menus['banco'] ?? '' }} fa-1x"></i>
                        </span>
                    </div>
                    {!! Form::text('number_i_pay', !empty($number_i_pay) ? $number_i_pay : null, [
                        'class' => 'form-control',
                        'placeholder' => 'Referencia',
                        'id' => 'number_i_pay',
                    ]) !!}
                </div>
            </div>
        </div>
        <div class="col-2">
            <label for="number_i_pay" class="m-0">Buscar</label>
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</div>
