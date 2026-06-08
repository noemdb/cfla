{!! Form::open([
    'route' => 'administracion.configuraciones.pensums.store',
    'method' => 'POST',
    'class' => 'form-signin',
]) !!}

{{ Form::hidden('pestudio_id', $pestudio->id) }}
{{ Form::hidden('grado_id', $grado->id) }}
@include('administracion.configuraciones.pensums.form.fields')
{!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block']) !!}

{!! Form::close() !!}
