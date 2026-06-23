{!! Form::model($edescriptiva, [
    'route' => ['profesors.edescriptivas.update', $edescriptiva->id],
    'method' => 'PUT',
    'id' => 'form-update-grado_' . $edescriptiva->id,
    'role' => 'form',
]) !!}

@include('profesors.edescriptivas.form.fields')

{!! Form::submit('Actualizar', ['class' => 'btn-edit btn btn-primary btn-block']) !!}

{!! Form::close() !!}
