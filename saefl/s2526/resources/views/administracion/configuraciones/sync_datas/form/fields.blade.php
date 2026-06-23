<div class="form-group">
    <label for="descuento_name" class="m-0">Nombre</label>
    {!! Form::text('descuento_name', old('descuento_name'), [
        'class' => 'form-control',
        'placeholder' => 'Nombre',
        'id' => 'descuento_name',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="descuento_description" class="m-0">Descripción</label>
    {!! Form::text('descuento_description', old('descuento_description'), [
        'class' => 'form-control',
        'placeholder' => 'Descripción',
        'id' => 'descuento_description',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="descuento_type" class="m-0">Tipo dedescuento</label>
    {!! Form::select('descuento_type', ['Porcentaje' => 'Porcentaje'], old('descuento_type'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="descuento_ammount" class="m-0">Porcentaje <span class="text-muted small">[Decimales separados por
            punto]</span></label>
    {!! Form::text('descuento_ammount', old('descuento_ammount'), [
        'class' => 'form-control',
        'placeholder' => 'Porcentaje',
        'id' => 'descuento_ammount',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="descuento_observations" class="m-0">Observaciones</label>
    {!! Form::text('descuento_observations', old('descuento_observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observaciones',
        'id' => 'descuento_observations',
    ]) !!}
</div>
