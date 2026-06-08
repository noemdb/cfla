{!! Form::open([
    'route' => 'administracion.configuraciones.area_conocimientos.store',
    'method' => 'POST',
    'id' => 'form-area_conocimientos-create',
    'class' => 'form-signin',
]) !!}

@include('administracion.configuraciones.area_conocimientos.form.fields')

{!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block']) !!}

{!! Form::close() !!}
