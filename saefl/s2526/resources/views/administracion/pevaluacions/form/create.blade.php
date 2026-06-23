<div class="border rounded-bottom p-1">
    {!! Form::open([
        'route' => 'administracion.pevaluacions.store',
        'method' => 'POST',
        'id' => 'form-pevaluacions-create',
        'class' => 'form-signin',
    ]) !!}
    @include('administracion.pevaluacions.form.fields')
    <button type="submit" class="btn-pevaluacions-create btn btn-primary btn-block" value="Registrar"
        data-id="create" id="btn-create-pevaluacions">
        <i class="far fa-save"></i> Registrar
    </button>
    {!! Form::close() !!}
</div>