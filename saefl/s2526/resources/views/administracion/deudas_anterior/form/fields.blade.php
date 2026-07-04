<div class="form-group">
    <label for="concepto_ammount" class="m-0">Monto de la cuenta de cobro</label>
    {!! Form::text('concepto_ammount', old('concepto_ammount'), [
        'class' => 'form-control',
        'placeholder' => 'Monto del Pago',
        'id' => 'concepto_ammount',
    ]) !!}
</div>
