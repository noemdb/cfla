<div class="form-group">
    <label for="cuentaxpagar_id" class="m-0">Cuenta por Pagar</label>
    {!! Form::select('cuentaxpagar_id', $cuentaxpagar_list, old('cuentaxpagar_id'), [
        'class' => 'form-control',
        'id' => 'cuentaxpagar_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
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
    <label for="seccion_id" class="m-0">Número de la transacción</label>
    {!! Form::text('number_i_pay', old('number_i_pay'), [
        'class' => 'form-control',
        'placeholder' => 'Número de la transacción',
        'id' => 'observations',
    ]) !!}
</div>
<div class="form-group">
    <label for="ammount" class="m-0">Monto del Pago</label>
    {!! Form::text('ammount', old('ammount'), [
        'class' => 'form-control',
        'placeholder' => 'Monto del Pago',
        'id' => 'ammount',
    ]) !!}
</div>
<div class="form-group">
    <label for="person_bill_ci" class="m-0">Cédula de la Persona a quien se le registrará el pago</label>
    {!! Form::text('person_bill_ci', old('person_bill_ci'), [
        'class' => 'form-control',
        'placeholder' => 'Cédula',
        'id' => 'ammount',
    ]) !!}
</div>
<div class="form-group">
    <label for="person_bill_name" class="m-0">Nombre de la Persona a quien se le registrará el pago</label>
    {!! Form::text('person_bill_name', old('person_bill_name'), [
        'class' => 'form-control',
        'placeholder' => 'Nombre',
        'id' => 'ammount',
    ]) !!}
</div>
