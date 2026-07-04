{!! Form::model($campo_conocimiento, [
    'route' => ['administracion.configuraciones.campo_conocimientos.update', $campo_conocimiento->id],
    'method' => 'PUT',
    'id' => 'form-update-campo_' . $campo_conocimiento->id,
    'role' => 'form',
]) !!}

@include('administracion.configuraciones.area_conocimientos.form.campo_conocimientos.fields')

{!! Form::submit('Actualizar', ['class' => 'btn-edit btn btn-primary btn-block']) !!}

{!! Form::close() !!}
