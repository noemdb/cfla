<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-10">Consulta</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-10">
            {!! Form::select('poll_main_id', $list_poll_main, $poll_main_id, [
                'class' => 'form-control form-control-sm',
                'id' => 'poll_main_id',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
        <div class="col-2">
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
