<div class="font-weight-bolder border-bottom">
    DATOS DE LA TRANSACCIÓN
</div>

{{ Form::hidden('prepago_id', $prepago->id) }}
{{ Form::hidden('representant_id', $representant->id) }}
{{ Form::hidden('estudiant_id', $estudiant->id) }}

<div class="form-group pt-2">
    <label for="method_pay_id" class=" m-0  font-weight-bold text-muted font-weight-bold text-muted">Método de
        Pago</label>
    {!! Form::select('method_pay_id', $method_pay_list, $prepago->method_pay_id, [
        'readonly',
        'class' => 'form-control',
        'id' => 'method_pay_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="banco_id" class=" m-0  font-weight-bold text-muted">Banco receptor del pago</label>
    {!! Form::select('banco_id', $banco_list, $prepago->banco_id, [
        'readonly',
        'class' => 'form-control',
        'id' => 'banco_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="number_i_pay" class=" m-0  font-weight-bold text-muted">Número de la transacción</label>
    {!! Form::text('number_i_pay', $prepago->number_i_pay, [
        'readonly',
        'class' => 'form-control',
        'placeholder' => 'Número de la transacción',
        'id' => 'number_i_pay',
        'required' => 'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="ingreso_ammount" class=" m-0  font-weight-bold text-muted">Monto <span
            class="small text-muted">[Decimales separados por punto]</span></label>
    {!! Form::text('ingreso_ammount_mask', f_float($prepago->ingreso_ammount), [
        'readonly',
        'class' => 'form-control',
        'placeholder' => 'Monto del Pago',
        'id' => 'ingreso_ammount_mask',
        'required' => 'required',
    ]) !!}
    {{ Form::hidden('ingreso_ammount', $prepago->ingreso_ammount) }}
</div>

<div class="form-group pb-1">
    <label for="date_transaction" class=" m-0  font-weight-bold text-muted">Fecha de la transacción</label>
    {!! Form::date('date_transaction', $prepago->date_transaction, [
        'readonly',
        'class' => 'form-control',
        'id' => 'date_transaction',
    ]) !!}
</div>

<div class="form-group">
    <label for="ingreso_description" class=" m-0  font-weight-bold text-muted">Descripción</label>
    {!! Form::text('ingreso_description', null, [
        'class' => 'form-control',
        'placeholder' => 'Descripción',
        'id' => 'ingreso_description',
    ]) !!}
</div>
