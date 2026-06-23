{!! Form::model($edescriptiva, [
    'route' => ['administracion.edescriptivas.update', $edescriptiva->id],
    'method' => 'PUT',
    'id' => 'form-update-grado_' . $edescriptiva->id,
    'role' => 'form',
]) !!}

@include('administracion.edescriptivas.form.fields')

{!! Form::submit('Actualizar', ['class' => 'btn-edit btn btn-primary btn-block']) !!}

{!! Form::close() !!}
