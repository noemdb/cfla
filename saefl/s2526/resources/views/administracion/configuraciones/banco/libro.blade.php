@extends('administracion.layouts.dashboard.app')

@section('title')
    Bancos, Libro de Facturación
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary text-center">
                <h4>
                    {{ $institucion->legalname ?? '' }}<br>
                    <small class="text-muted">
                        {{ $institucion->rif_institution ?? '' }}
                    </small>
                </h4>
                <h5>
                    Libro de Facturación<br>
                    <span class=" text-uppercase"> {{ $banco->name ?? '' }}</span><br>
                    <small class="text-default small">{{ Date::now()->format('l j F Y H:i:s') ?? '' }}</small>
                    <div class="text-muted small">
                        @if (!empty($finicial))
                            <span>Fecha inicial: {{ $finicial }}</span>&nbsp;
                        @endif
                        @if (!empty($ffinal))
                            <span>Fecha final: {{ $ffinal }}</span>
                        @endif
                    </div>
                    <div class="text-muted font-weight-bold small">
                        @if ($status_late_payment == 'true')
                            <span>Pagos reportados extemporáneamente</span>&nbsp;
                        @endif
                    </div>

                    {{-- INI Menu rapido --}}
                    {{-- <div class="btn-group float-right pt-2"> --}}

                    {{-- @include('administracion.configuraciones.menus.index') --}}

                    {{-- </div> --}}
                    {{-- FIN Menu rapido --}}
                </h5>

            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                {{-- @include('administracion.elements.messeges.oper_ok') --}}

                {!! Form::open(['route' => 'administracion.libro.banco', 'method' => 'GET', 'class' => '', 'role' => 'search']) !!}
                {{-- {{ Form::hidden('banco_id', $banco->id,['id'=>'banco_id']) }} --}}
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-2">
                            {!! Form::label('banco_id', 'Banco', ['class' => 'm-0 font-weight-bold text-muted']) !!}
                            {!! Form::select('banco_id', $list_banco, $banco_id, [
                                'class' => 'form-control',
                                'id' => 'banco_id',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                        </div>
                        <div class="col-2">
                            {!! Form::label('method_pay_id', 'Mètodo de Pago', ['class' => 'm-0 font-weight-bold text-muted']) !!}
                            {!! Form::select('method_pay_id', $list_metodo_pago, $method_pay_id, [
                                'class' => 'form-control',
                                'id' => 'method_pay_id',
                                'placeholder' => 'Seleccione',
                            ]) !!}
                        </div>
                        <div class="col-2">
                            {!! Form::label('finicial', 'Fecha inicial', ['class' => 'm-0 font-weight-bold text-muted']) !!}
                            {!! Form::text('finicial', $finicial, [
                                'class' => 'form-control datepicker',
                                'placeholder' => '2019-01-01',
                                'id' => 'finicial',
                                'maxlength' => '10',
                            ]) !!}
                        </div>
                        <div class="col-2">
                            {!! Form::label('ffinal', 'Fecha Final', ['class' => 'm-0 font-weight-bold text-muted']) !!}
                            {!! Form::text('ffinal', $ffinal, [
                                'class' => 'form-control datepicker',
                                'placeholder' => '2019-01-01',
                                'id' => 'ffinal',
                                'maxlength' => '10',
                            ]) !!}
                        </div>
                        <div class="col-sm-2">
                            {!! Form::label('status_late_payment', 'Pago Extemporaneo', ['class' => 'm-0 font-weight-bold text-muted']) !!}
                            {!! Form::select('status_late_payment', ['false' => 'NO', 'true' => 'SI'], $status_late_payment, [
                                'class' => 'form-control',
                                'id' => 'status_late_payment',
                            ]) !!}
                        </div>
                        <div class="col-sm-2">
                            {!! Form::label('submit', ' ', ['class' => 'm-0 font-weight-bold text-muted']) !!}
                            <div class="input-group">
                                <div class="input-group-append w-100" style="z-index: 0;">
                                    <button class="btn btn-primary my-2 my-sm-0 w-100" type="submit">Buscar</button>
                                    <button class="btn btn-dark my-2 my-sm-0" id="btn_toprint" type="button">
                                        <i class="fa fa-print" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="input-group">

                        {!! Form::text('finicial', $finicial, ['class' => 'form-control datepicker','placeholder'=>'2019-01-01','id'=>'finicial','maxlength'=>"10"]); !!}
                        <span class="input-group-text"> - </span>
                        {!! Form::text('ffinal', $ffinal, ['class' => 'form-control datepicker','placeholder'=>'2019-12-31','id'=>'ffinal','maxlength'=>"10"]); !!}

                        <div class="input-group-append" style="z-index: 0;">
                            <button class="btn btn-primary my-2 my-sm-0" type="submit">Buscar</button>
                            <button class="btn btn-dark my-2 my-sm-0" id="btn_toprint" type="button">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="status_late_payment" name="status_late_payment" {{($status_late_payment=='on') ? 'checked' : null}}>
                            <label class="custom-control-label" for="status_late_payment">Extemporaneos</label>
                        </div>
                    </div> --}}
                {!! Form::close() !!}

                <hr>

                <table class="table text-left table table-sm" style="pading-botton:0px; margin-botton:0" cellspacing="0"
                    cellpadding="0">
                    <thead>
                        <tr style="background-color:#e0e0e0">
                            <th colspan="2">Total General</th>
                            <th>Bs. {{ f_float($ingresos->sum('ingreso_ammount')) }}</th>
                            <th>$ {{ f_float($ingresos->sum('exchange_ammount')) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Método de pago</th>
                            <th>Cantidad</th>
                            <th>Monto (Bs.)</th>
                            <th>M.Cambiario ($)</th>
                        </tr>
                        @foreach ($metodos as $metodo)
                            <tr>
                                <td scope="row">{{ $metodo->name ?? '' }}</td>
                                <td>{{ $metodo->count ?? '' }}</td>
                                <td>{{ f_float($metodo->total) }}</td>
                                <td>{{ f_float($metodo->total_exchange_ammount) }}</td>
                            </tr>
                        @endforeach
                        {{-- <tr>
                            <td colspan="4">
                                @if (!empty($ingresos))
                                    @include('administracion.configuraciones.banco.libro.table')
                                @endif
                            </td>
                        </tr> --}}

                    </tbody>

                    {{-- <hr> --}}

                    @if (!empty($ingresos))
                        @include('administracion.configuraciones.banco.tabs.navtabs')
                    @endif

            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @parent

    <script src="{{ asset('vendor/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.es.min.js') }}"></script>

    <script type="text/javascript">
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            autoclose: true,
            startView: 2
        });

        //btn para ir a editar los datos del EST
        $(document).ready(function() {
            $('#btn_toprint').click(function(e) {
                e.preventDefault();
                var banco_id = $('#banco_id').val(); //console.log(ci_estudiant);
                var method_pay_id = $('#method_pay_id').val(); //console.log(ci_estudiant);
                var finicial = $('#finicial').val(); //console.log(ci_estudiant);
                var ffinal = $('#ffinal').val(); //console.log(ci_estudiant);
                var status_late_payment = $('#status_late_payment').val(); //console.log(ci_estudiant);
                var dataString = '?banco_id=' + banco_id + '&method_pay_id=' + method_pay_id +
                    '&finicial=' + finicial + '&ffinal=' + ffinal + '&status_late_payment=' +
                    status_late_payment; //console.log(dataString);
                var url = "{{ route('administracion.configuraciones.banco.libro.facturacion') }}" +
                    dataString;
                window.open(url, '_blank');
            });
        });
    </script>
@endsection

@section('stylesheet')
    @parent

    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    @parent
    {{-- <script src="{{ asset("js/Chart.js") }}"></script> --}}
    <script src="{{ asset('js/Chart.bundle.js') }}"></script>
    {{-- <script src="{{ asset("js/utils.js") }}"></script> --}}
    <script src="{{ asset('js/ChartFunction.js') }}"></script>{{-- Funciones para generar los Chart --}}

    {{-- INI Evento clic para generar los Chart por rango --}}
    <script src="{{ asset('js/ChartEvent.js') }}"></script>{{-- Funciones para generar los Chart --}}
    {{-- FIN Evento clic para generar los Chart por rango --}}
@endsection
