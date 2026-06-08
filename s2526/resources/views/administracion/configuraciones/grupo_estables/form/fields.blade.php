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
    <label for="code" class="m-0">Código (Hasta 10 caracteres)</label>
    {!! Form::text('code', old('code'), [
        'maxlength' => '10',
        'class' => 'form-control',
        'placeholder' => 'Código',
        'id' => 'code',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="code_sm" class="m-0">Abreviación (Sólo dos letras)</label>
    {!! Form::text('code_sm', old('code_sm'), [
        'maxlength' => '4',
        'class' => 'form-control',
        'placeholder' => 'Abreviación',
        'id' => 'code_sm',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="hour_t_week" class="m-0">Número de horas teóricas dictadas en la semana</label>
    {!! Form::selectRange('hour_t_week', 0, 10, old('hour_t_week'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<div class="form-group">
    <label for="hour_t_week" class="m-0">Número de horas prácticas dictadas en la semana</label>
    {!! Form::selectRange('hour_p_week', 0, 10, old('hour_p_week'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>
<div class="form-group">
    <label for="description" class="m-0">Descripción</label>
    {!! Form::text('description', old('description'), [
        'class' => 'form-control',
        'placeholder' => 'Descripción',
        'id' => 'description',
    ]) !!}
</div>
<div class="form-group">
    <label for="observations" class="m-0">Observaciones</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observaciones',
        'id' => 'observations',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_active"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['status_active'] ?? '' }}</label>
    {!! Form::select('status_active', ['true' => 'Activo', 'false' => 'Inactivo'], old('status_active'), [
        'class' => 'form-control',
        'required',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_belongs_ins"
        class="font-weight-bold text-secondary m-0">{{ $list_comment['status_belongs_ins'] ?? '' }}</label>
    {!! Form::select('status_belongs_ins', ['true' => 'SI', 'false' => 'NO'], old('status_belongs_ins'), [
        'class' => 'form-control',
        'required',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>
