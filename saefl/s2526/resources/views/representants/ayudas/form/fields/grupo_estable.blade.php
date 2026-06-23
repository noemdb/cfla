<div class="form-group">
    <label for="programacion_id" class="m-0 font-weight-bold">Grupo Estable</label>
    {!! Form::select('grupo_estable_id', $list_grupo_estables, $inscripcion->grupo_estable_id, [
        'class' => 'form-control',
        'id' => 'grupo_estable_id',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>
