{!! Form::model($area_conocimiento, [
    'route' => ['administracion.configuraciones.area_conocimientos.update', $area_conocimiento->id],
    'method' => 'PUT',
    'id' => 'form-update-area_' . $area_conocimiento->id,
    'role' => 'form',
]) !!}

@include('administracion.configuraciones.area_conocimientos.form.fields')

{!! Form::submit('Actualizar', ['class' => 'btn-edit btn btn-primary btn-block']) !!}

{!! Form::close() !!}
