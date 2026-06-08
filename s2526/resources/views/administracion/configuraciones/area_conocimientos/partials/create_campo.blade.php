{!! Form::open([
    'route' => 'administracion.configuraciones.campo_conocimientos.store',
    'method' => 'POST',
    'class' => 'form-signin',
]) !!}

{{ Form::hidden('area_conocimiento_id', $area_conocimiento->id) }}

@includeif('administracion.configuraciones.area_conocimientos.form.campo_conocimientos.fields')

{!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block']) !!}

{!! Form::close() !!}
