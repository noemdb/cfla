<label for="escala_id" class="m-0">Escala</label>
<div class="input-group mb-3">
    @switch($pevaluacion->nota_type)
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
