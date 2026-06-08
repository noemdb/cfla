{{ Form::hidden('pevaluacion_id', $pevaluacion->id) }}
{{-- {{ Form::hidden('pensum_id', $pensum->id) }}
  {{ Form::hidden('grado_id', $grado->id) }}
  {{ Form::hidden('lapso_id', $lapso->id) }} --}}

<label for="seccion_id" class="m-0">Sección</label>
<div class="input-group mb-3">
    {!! Form::select('seccion_id', $seccion_list, old('seccion_id'), [
        'id' => 'seccion_id',
        'class' => 'form-control',
        'placeholder' => 'Seleccione',
        'required',
    ]) !!}
</div>
