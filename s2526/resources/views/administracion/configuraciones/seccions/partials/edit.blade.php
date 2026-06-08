{!! Form::model($seccion, [
    'route' => ['administracion.configuraciones.seccions.update', $seccion->id],
    'method' => 'PUT',
    'id' => 'form-update-grado_' . $seccion->id,
    'role' => 'form',
]) !!}

@include('administracion.configuraciones.seccions.form.fields')

{!! Form::submit('Actualizar', ['class' => 'btn-edit btn btn-primary btn-block']) !!}

{!! Form::close() !!}
