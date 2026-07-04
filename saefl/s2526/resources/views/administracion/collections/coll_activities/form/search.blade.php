<div class="card-header p-0 m-0 mb-3">
    {!! Form::open([
        'route' => 'administracion.collections.coll_activities.generate',
        'method' => 'GET',
        'class' => 'p-1 m-1',
        'role' => 'search',
    ]) !!}
    <div class="form-row font-weight-bold">
        <div class="col-8">Políticas de Cobro</div>
        <div class="col-4">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-8">
            {!! Form::select('coll_political_id', $list_coll_politicals, $coll_political_id, [
                'class' => 'form-control form-control-sm',
                'id' => 'coll_political_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-4">
            <div class="btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">
                <button class="btn btn-primary btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
