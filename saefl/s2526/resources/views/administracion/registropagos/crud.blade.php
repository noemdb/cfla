@extends('administracion.layouts.dashboard.app')

@section('title') Pagos Registrados, Listado - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-2">
                    @include('administracion.registropagos.menus.crud')
                </div>
                {{-- FIN Menu rapido --}}
                <h4><span title="Listado especial con botones de acción"><u>Listado</u></span> de <span class="font-weight-bolder">Pagos Registrados</span></h4>
            </div>

            <div class="card-body p-2">

                <div class="card-header p-0 m-0 mb-2">
                    {!! Form::open(['route'=>'administracion.registropagos.crud','method'=>'GET','class'=>'pb-1','id'=>'form_search','role'=>'search']) !!}

                        @include('administracion.registropagos.form.search')

                    {!! Form::close() !!}
                </div>

                @if(!empty($registropagos->count()))
                    <div class=" bg-light pb-1 font-weight-normal d-block text-muted">
                        <div class="float-right d-block">
                            Total General: <span>Bs. {{(!empty($pago_total)) ? f_float($pago_total):''}}</span>
                            [<span>$ {{f_float($pago_total_exchage)}}</span>] ||
                            Nº <span>{{ $registropagos->count() ?? ''}}</span>
                        </div>
                    </div>
                @endif

                <br>

                @include('administracion.registropagos.table.crud')

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        //btn para ir a editar los datos del EST
        $(document).ready(function() {
            $('#btn_xls').click(function (e) {
                e.preventDefault();
                var cuentaxpagar_id   = $('#cuentaxpagar_id').val();	//console.log(ci_estudiant);
                var finicial          = $('#finicial').val();	//console.log(ci_estudiant);
                var ffinal            = $('#ffinal').val();	//console.log(ci_estudiant);
                var number_i_pay      = $('#number_i_pay').val();	//console.log(ci_estudiant);
                var ci                = $('#ci').val();	//console.log(ci_estudiant);
                var dataString = '?cuentaxpagar_id='+cuentaxpagar_id+'&finicial='+finicial+'&ffinal='+ffinal+'&number_i_pay='+number_i_pay+'&ci='+ci; //console.log(dataString);
                var url = "{{ route('administracion.registropagos.export.xls') }}"+dataString; //console.log(url);
                window.open(url,'_blank');
            });
        });
    </script>

@endsection
