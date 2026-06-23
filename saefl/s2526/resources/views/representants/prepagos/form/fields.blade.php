<div class="form-group pt-2">
    <label for="method_pay_id" class="m-0">Método de Pago</label>
    {!! Form::select('method_pay_id', $method_pay_list, old('method_pay_id'), [
        'class' => 'form-control',
        'id' => 'method_pay_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="banco_id" class="m-0">Banco receptor del pago</label>
    {!! Form::select('banco_id', $banco_list, old('banco_id'), [
        'class' => 'form-control',
        'id' => 'banco_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="number_i_pay" class="m-0">Número de la transacción</label>
    {!! Form::text('number_i_pay', old('number_i_pay'), [
        'class' => 'form-control',
        'placeholder' => 'Número de la transacción',
        'id' => 'number_i_pay',
        'required' => 'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="ingreso_ammount" class="m-0">Monto <span class="small text-muted">[Decimales separados por
            punto]</span></label>
    {!! Form::text('ingreso_ammount', old('ingreso_ammount'), [
        'class' => 'form-control',
        'placeholder' => 'Monto de la transacción',
        'id' => 'ingreso_ammount',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="date_transaction" class="m-0">Fecha de la transacción</label>
    {!! Form::date('date_transaction', old('date_transaction'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha de la transacción',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="ingreso_observations" class="m-0">Observación</label>
    {!! Form::text('ingreso_observations', old('ingreso_observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observación',
        'id' => 'ingreso_observations',
    ]) !!}
</div>
