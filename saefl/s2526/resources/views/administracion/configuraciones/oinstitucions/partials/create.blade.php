{!! Form::open([
    'route' => 'administracion.configuraciones.seccions.store',
    'method' => 'POST',
    'id' => 'form-seccions-create',
    'class' => 'form-signin',
]) !!}

@include('administracion.configuraciones.seccions.form.fields')

{!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block', 'id' => 'create']) !!}

{!! Form::close() !!}
