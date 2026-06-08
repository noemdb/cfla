<div class="form-group">
    <label for="pestudio_id" class="m-0 font-weight-bold text-secondary">Plan de Estudio</label>
    {!! Form::select('pestudio_id', $list_pestudio, old('pestudio_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="grado_id" class="m-0 font-weight-bold text-secondary">Grado</label>
<div class="input-group mb-3">
    {!! Form::select('grado_id', $list_grado, old('grado_id'), [
        'id' => 'grado_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="asignatura_id" class="m-0 font-weight-bold text-secondary">Asignatura</label>
    {!! Form::select('asignatura_id', $list_asignatura, old('asignatura_id'), [
        'id' => 'asignatura_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_component" class="m-0 font-weight-bold text-secondary">Contiene componentes de Formación?</label>
    {!! Form::select('status_component', ['true' => 'SI', 'false' => 'NO'], old('status_component'), [
        'id' => 'status_component',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="observations" class="m-0 font-weight-bold text-secondary">Observación</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observación',
        'id' => 'observations',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_official" class="m-0 font-weight-bold text-secondary">Observación</label>
    {!! Form::text('status_official', old('status_official'), [
        'class' => 'form-control',
        'placeholder' => 'Observación',
        'id' => 'status_official',
    ]) !!}
</div>
