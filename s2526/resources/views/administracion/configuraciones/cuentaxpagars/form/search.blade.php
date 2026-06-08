<div class="card-header p-1 m-1 alert-light">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-2">Plan de pago</div>
        <div class="col-4">Tipo</div>
        <div class="col-2">F.Inicial</div>
        <div class="col-2">F.Final</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">

        <div class="col-2">
            {!! Form::select('planpago_id', $list_planpago, $planpago_id, [
                'class' => 'form-control',
                'id' => 'planpago_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-4">
            {!! Form::select('type', ['GENERAL' => 'GENERAL', 'INDIVIDUAL' => 'INDIVIDUAL'], $type, [
                'class' => 'form-control',
                'id' => 'type',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::date('finicial', $finicial, [
                'class' => 'form-control',
                'placeholder' => 'Fecha Inicial',
                'id' => 'finicial',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::date('ffinal', $ffinal, [
                'class' => 'form-control',
                'placeholder' => 'Fecha Inicial',
                'id' => 'ffinal',
            ]) !!}
        </div>
        <div class="col-2">
            <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                <i class="fa fa-search" aria-hidden="true"></i>
                Buscar
            </button>
        </div>

    </div>
    {!! Form::close() !!}
</div>
