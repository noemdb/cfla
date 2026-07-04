<div class="card-header p-0 m-0 mb-3">
    {!! Form::open([
        'route' => 'administracion.preinscripcions.crud',
        'method' => 'GET',
        'class' => 'p-1 m-1',
        'role' => 'search',
    ]) !!}
    <div class="form-row font-weight-bold">
        <div class="col-6">Nombre/Identificador</div>
        <div class="col-2">Grado/Preinscripción</div>
        <div class="col-2">Prosecución</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-6">
            {!! Form::text('search', $search, ['class' => 'form-control', 'placeholder' => 'Buscar Nombre o Identificador']) !!}
        </div>
        <div class="col-2">
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('prosecucion_seccion_id', $list_prosecucion, $prosecucion_seccion_id, [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">Buscar</button>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
