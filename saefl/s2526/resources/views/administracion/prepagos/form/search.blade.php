<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-3">CI Representante</div>
        <div class="col-2">Aprobación</div>
        <div class="col-2">Aplicación</div>
        <div class="col-2">Banco</div>
        <div class="col-2">Referencia</div>
        <div class="col-1">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="{{ $icon_menus['representante'] ?? '' }} fa-1x"></i>
                    </span>
                </div>
                {!! Form::text('ci_representant', $ci_representant, [
                    'class' => 'form-control',
                    'placeholder' => 'CI Representante',
                    'id' => 'ci_representant',
                    'maxlength' => '10',
                ]) !!}
            </div>
        </div>
        <div class="col-2">
            {!! Form::select('status_approved', ['SI' => 'APROBADAS', 'NO' => 'NO APROBADAS'], $status_approved, [
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('status_apply', ['SI' => 'APLICADAS', 'NO' => 'NO APLICADAS'], $status_apply, [
                'class' => 'form-control',
                'id' => 'status_apply',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('banco_id', $list_banco, $banco_id, [
                'class' => 'form-control',
                'id' => 'banco_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">
                        <i class="{{ $icon_menus['banco'] ?? '' }} fa-1x"></i>
                    </span>
                </div>
                {!! Form::text('number_i_pay', $number_i_pay, [
                    'class' => 'form-control',
                    'placeholder' => 'Referencia',
                    'id' => 'number_i_pay',
                ]) !!}
            </div>
        </div>

        <div class="col-1">

            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    {{-- Buscar --}}
                </button>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal_search">
                    <i class="{{ $icon_menus['puntos'] }}"></i>
                </button>
            </div>

            @include('administracion.prepagos.form.search.modal')

        </div>
    </div>
    {!! Form::close() !!}
</div>
