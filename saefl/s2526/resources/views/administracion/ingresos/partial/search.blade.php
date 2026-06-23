<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="d-flex justify-content-end">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="status_late_payment" name="status_late_payment"
                {{ $status_late_payment == 'on' ? 'checked' : null }}>
            <label class="custom-control-label" for="status_late_payment">Extemporaneos</label>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="is_public" name="is_public"
                {{ $is_public == 'on' ? 'checked' : null }}>
            <label class="custom-control-label" for="is_public">Bancos Público</label>
        </div>
    </div>
    <div class="form-row font-weight-bold">
        <div class="col-2" title="Fecha de pago incial">F. Pago Inicial</div>
        <div class="col-2" title="Fecha de pago incial">F. Pago Final</div>
        <div class="col-2">Banco</div>
        <div class="col-2">Identificador</div>
        <div class="col-2">Referencia</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-2">
            {!! Form::date('finicial', $finicial, ['class' => 'form-control', 'id' => 'finicial']) !!}
        </div>
        <div class="col-2">
            {!! Form::date('ffinal', $ffinal, ['class' => 'form-control', 'id' => 'ffinal']) !!}
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
                        <i class="{{ $icon_menus['profile'] ?? '' }} fa-1x"></i>
                    </span>
                </div>
                {!! Form::text('ci', $ci, [
                    'class' => 'form-control',
                    'placeholder' => 'Identificador',
                    'id' => 'ci',
                    'maxlength' => '10',
                ]) !!}
            </div>
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

        {{-- <div class="col-1">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="customSwitch1">
                    <label class="custom-control-label" for="customSwitch1">&nbsp;</label>
                </div>
            </div> --}}

        <div class="col-2">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    {{-- Buscar --}}
                </button>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
                @if (!empty($btn_toprint))
                    <a id="btn_toprint" class="btn btn-dark" href="#" role="button" title="Generar PDF">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
