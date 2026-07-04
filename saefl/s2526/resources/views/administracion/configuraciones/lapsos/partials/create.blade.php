{!! Form::open([
    'route' => 'administracion.configuraciones.lapsos.store',
    'method' => 'POST',
    'id' => 'form-lapsos-create',
    'class' => 'form-signin',
]) !!}

@include('administracion.configuraciones.lapsos.form.fields')

{!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block', 'id' => 'create']) !!}

{!! Form::close() !!}
