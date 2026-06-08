@php $required_seccion = (!empty($required_seccion)) ? 'required':true ; @endphp
@php $required_lapso = (!empty($required_lapso)) ? 'required':true ; @endphp

<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        <div class="col-2">Fecha Inicial</div>
        <div class="col-2">Fecha Final</div>
        <div class="col-2">Identificador <span class=" font-weight-normal text-muted small">[Representante]</span></div>
        <div class="col-2" title="Representnates activos">Activos</div>
        <div class="col-2" title="Omitido">Omitidos</div>
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
            {!! Form::select('active', ['SI' => 'SI', 'NO' => 'NO'], $active, [
                'class' => 'form-control',
                'id' => 'active',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-2">
            {!! Form::select('status_omitted_request', ['true' => 'SI', 'false' => 'NO'], $status_omitted_request, [
                'class' => 'form-control',
                'id' => 'status_omitted_request',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-2">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
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
                {{-- @if (!empty($btn_xls))
                        <a id="btn_toxls" class="btn-toxls btn btn-success" href="#" role="button" title="Generar XLS">
                            <i class="fas fa-file-excel" aria-hidden="true"></i>
                        </a>
                    @endif --}}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
