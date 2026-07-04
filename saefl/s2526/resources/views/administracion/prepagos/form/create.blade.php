<div class=" p-2">

    {!! Form::open([
        'route' => 'administracion.prepagos.store',
        'method' => 'POST',
        'id' => 'form-prepago-create',
        'class' => 'form-signin',
    ]) !!}

    @include('administracion.prepagos.form.fields.prepago')

    {!! Form::submit('Registrar', ['class' => 'btn-create btn btn-primary btn-block', 'id' => 'create']) !!}

    {!! Form::close() !!}

</div>
