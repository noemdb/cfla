{{ Form::hidden('grado_id', empty($grado_id) ? null : $grado_id) }}
{{ Form::hidden('seccion_id', empty($seccion_id) ? null : $seccion_id) }}
{{ Form::hidden('lapso_id', empty($lapso_id) ? null : $lapso_id) }}
{{ Form::hidden('selected_id', $selected->id) }}

{{ Form::hidden('nota_type', $selected->nota_type) }}
{{ Form::hidden('nota_max', $selected->escala->maximo) }}

{!! Form::hidden('nota_ctr', '1') !!}

@php $escala_name = (!empty($selected->escala->id)) ? $selected->escala->name : null; @endphp
@php $escala_id = (!empty($selected->escala->id)) ? $selected->escala->id : null; @endphp

<div class="form-group">
    <label for="description" class="m-0 font-weight-bold text-muted">Grupo Estable</label>
    {!! Form::select('escala_id', $list_grupo_estable, old('escala_id'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="description" class="m-0 font-weight-bold text-muted">Descripción / Estrategía</label>
    {!! Form::text('description', old('description'), [
        'class' => 'form-control',
        'placeholder' => 'Descripción / Estrategía',
        'id' => 'description',
        'required',
    ]) !!}
</div>

<label for="escala_id" class="m-0 font-weight-bold text-muted">Escala</label>
<div class="input-group mb-3">
    @switch($selected->nota_type)
        @case('ACUMULATIVA')
            {!! Form::select('escala_id', $escala_list, old('escala_id'), [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        @break

        @case('PROMEDIADA')
            {!! Form::text('escala_name', $escala_name, [
                'class' => 'form-control',
                'placeholder' => 'Escala',
                'id' => 'escala_name',
                'readonly',
            ]) !!}
            {!! Form::hidden('escala_id', $escala_id) !!}
        @break

        @default
    @endswitch
</div>

<div class="form-group pb-1">
    {!! Form::label('fecha', 'Fecha', ['class' => 'm-0 font-weight-bold text-muted']) !!}
    {!! Form::date('fecha', old('fecha'), [
        'class' => 'form-control',
        'placeholder' => 'Fecha',
        'required' => 'required',
    ]) !!}
    <div class="form-group pb-1">
    </div>
</div>
