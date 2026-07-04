{{ Form::hidden('pe_id_origen', $pevaluacion->id) }}

<label for="grado_id" class="m-0 font-weight-bold text-muted">Plan de Evaluación destino</label>
<div class="input-group mb-2 pb-2">
    {!! Form::select('pe_id_destino', $pevaluacion_list, old('pe_id_destino'), [
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'id' => 'pe_id_destino',
        'required',
    ]) !!}
</div>

<label for="seccion_id" class="m-0 font-weight-bold text-muted">Sección</label>
<div class="input-group mb-2  pb-2">
    {!! Form::select('seccion_id', $seccion_list, old('seccion_id'), [
        'id' => 'seccion_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>
