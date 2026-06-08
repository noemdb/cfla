{!! Form::open([
    'route' => 'administracion.configuraciones.grados.store',
    'method' => 'POST',
    'id' => 'form-peducativos-create',
    'class' => 'form-signin',
]) !!}

@include('administracion.configuraciones.grados.form.fields')

{!! Form::submit('Registrar', [
    'class' => 'btn-grupo_estable-create btn btn-primary btn-block',
    'placeholder' => 'Seleccione',
    'id' => 'create',
]) !!}

{!! Form::close() !!}
