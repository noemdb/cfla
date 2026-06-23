<span class="font-weight-bold">
    Información adicional
</span>
<div class="px-2 rounded border">

    <div class="row small">
        <div class="col">
            <div class="form-group">
                <label for="seccion_id" class="m-0 font-weight-bold">Sección</label>
                {!! Form::select('seccion_id', $list_seccion, old('seccion_id'), [
                    'class' => 'form-control',
                    'id' => 'seccion_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="programacion_id" class="m-0 font-weight-bold">Programación - Lapsos/Períodos a cursar</label>
                {!! Form::select('programacion_id', $list_programacion, old('programacion_id'), [
                    'class' => 'form-control',
                    'id' => 'programacion_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
            {{--  --}}
        </div>
    </div>

    <div class="row small">
        <div class="col">
            <div class="form-group">
                <label for="tipo_id" class="m-0 font-weight-bold">Tipo</label>
                {!! Form::select('tipo_id', $list_tinscripcion, old('tipo_id'), [
                    'class' => 'form-control',
                    'id' => 'tipo_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="escolaridad_id" class="m-0 font-weight-bold">Escolaridad</label>
                {!! Form::select('escolaridad_id', $list_escolaridad, old('escolaridad_id'), [
                    'class' => 'form-control',
                    'id' => 'escolaridad_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="row small">
        <div class="col">
            <div class="form-group">
                <label for="programacion_id" class="m-0 font-weight-bold">Grupo Estable</label>
                {!! Form::select('grupo_estable_id', $list_grupo_estables, old('grupo_estable_id'), [
                    'class' => 'form-control',
                    'id' => 'grupo_estable_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

        </div>
        <div class="col">
            <div class="form-group">
                <label for="observations" class="m-0 font-weight-bold">Observaciones</label>
                {{-- {!! Form::text('observations', old('observations'), ['class' => 'form-control','placeholder'=>'Observaciones','id'=>'observations']); !!} --}}
                {!! Form::textarea('observations', old('observations'), [
                    'class' => 'form-control',
                    'placeholder' => 'Observaciones',
                    'id' => 'observations',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>

    </div>
</div>
