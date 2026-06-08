<div class="p-2">
    <div class="text-muted small font-weight-bold">Antes de generar los detalles del debate académico, es fundamental
        proporcionar información teórica adicional detallada y basada en evidencia. Esta información debe complementar
        los conceptos mencionados en la descripción teórica de las actividades para este debate debate. Sería útil
        incluir un breve desarrollo del tema principal, así como un enfoque en los aspectos esenciales del mismo.
        Además, se recomienda integrar referentes teóricos, prácticos y éticos, junto con un listado de conceptos clave
        asociados a la temática central del debate. <span class="text-primary">Máximo de 20 palabras</span></div>
    <div class="form-group">
        @php
            $name = 'referents';
            $model = '' . $name;
        @endphp
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => 'Información teórica adicional',
            'rows' => '4',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>
