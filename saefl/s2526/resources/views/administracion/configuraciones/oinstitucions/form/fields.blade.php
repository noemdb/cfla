<div class="form-group">
    <label for="name" class="m-0">Nombre</label>
    {!! Form::text('name', old('name'), [
        'class' => 'form-control',
        'placeholder' => 'Nombre',
        'id' => 'name',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="code" class="m-0">Código (Hasta 20 caracteres)</label>
    {!! Form::text('code', old('code'), [
        'maxlength' => '20',
        'class' => 'form-control',
        'placeholder' => 'Código',
        'id' => 'code',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="description" class="m-0">Descripción</label>
    {!! Form::text('description', old('description'), [
        'class' => 'form-control',
        'placeholder' => 'description',
        'id' => 'description',
    ]) !!}
</div>
<div class="form-group">
    <label for="locations" class="m-0">Localización - Municipio</label>
    {!! Form::text('locations', old('locations'), [
        'class' => 'form-control',
        'placeholder' => 'Localización',
        'id' => 'locations',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="state" class="m-0">Estado - Entidad Federal (sólo dos carácteres)</label>
    {!! Form::text('state', old('state'), [
        'class' => 'form-control',
        'maxlength' => '2',
        'placeholder' => 'Estado - E.F.',
        'id' => 'state',
        'required',
    ]) !!}
</div>

<label for="status_except"
    class="font-weight-bold text-secondary m-0">{{ $list_comment['status_except'] ?? '' }}</label>
<div class="form-group">
    {!! Form::select('status_except', [true => 'SI', false => 'NO'], old('status_except'), [
        'class' => 'form-control',
        'id' => 'status_except',
        'required',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>
