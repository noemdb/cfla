{!! Form::model($grado, [
    'route' => ['administracion.configuraciones.grados.update', $grado->id],
    'method' => 'PUT',
    'id' => 'form-update-grado_' . $grado->id,
    'role' => 'form',
]) !!}

@include('administracion.configuraciones.grados.form.fields')

{!! Form::submit('Actualizar', ['class' => 'btn-grupo_estable-create btn btn-primary btn-block']) !!}

{!! Form::close() !!}
