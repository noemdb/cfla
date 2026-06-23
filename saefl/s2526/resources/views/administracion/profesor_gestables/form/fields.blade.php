{{-- 
{{ Form::hidden('seccion_id', (empty($seccion_id)) ? null : $seccion_id) }}
{{ Form::hidden('lapso_id', (empty($lapso_id)) ? null : $lapso_id) }}

{{ Form::hidden('pevaluacion_id', $selected->id) }} --}}
{{-- <div class=" font-weight-bold text-muted text-right">{{ ($grado) ? $grado->name : null}}</div> --}}
{{-- <hr> --}}

{{ Form::hidden('grado_id', empty($grado_id) ? null : $grado_id) }}
{{ Form::hidden('pensum_id', empty($pensum_id) ? null : $pensum_id) }}

<label for="pensum_seccion_id" class="m-0">
    <div class="font-weight-bold">{{ $grado ? $grado->name : null }}</div>
    <div>Sección</div>
</label>
<div class="input-group mb-3">
    {!! Form::select('pensum_seccion_id', $list_seccion, $pensum_seccion_id, [
        'class' => 'form-control',
        'id' => 'pensum_seccion_id',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="lapso_id" class="m-0">Momento</label>
<div class="input-group mb-3">
    {!! Form::select('lapso_id', $list_lapso, $lapso_id, [
        'class' => 'form-control',
        'id' => 'lapso_id',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="profesor_id" class="m-0 text-muted font-weight-bold">Profesor</label>
<div class="input-group mb-3">
    {!! Form::select('profesor_id', $list_profesor, old('profesor_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<label for="grupo_estable_id" class="m-0 text-muted font-weight-bold">Grupo Estable</label>
<div class="input-group mb-3">
    {!! Form::select('grupo_estable_id', $list_grupo_estable, old('grupo_estable_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="observations" class="m-0">Observación</label>
    {!! Form::text('observations', old('observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observación',
        'id' => 'observations',
    ]) !!}
</div>
