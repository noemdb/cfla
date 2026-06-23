{!! Form::model($lapso, [
    'route' => ['administracion.configuraciones.lapsos.update', $lapso->id],
    'method' => 'PUT',
    'id' => 'form-update-lapso_' . $lapso->id,
    'role' => 'form',
]) !!}

@include('administracion.configuraciones.lapsos.form.fields')

{!! Form::submit('Actualizar', ['class' => 'btn-edit btn btn-primary btn-block']) !!}

{!! Form::close() !!}
