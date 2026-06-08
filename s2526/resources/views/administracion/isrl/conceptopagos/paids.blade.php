@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header alert-secondary">

                {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-2">
                        @include('administracion.isrl.conceptopagos.menus.crud')
                    </div>
                {{-- FIN Menu rapido --}}
                <h4><span title="Listado especial con botones de acción"><u>Listado</u></span> de <span class="font-weight-bolder">Representantes con cuentas de cobro anuales/mensuales pagadas</span></h4>
            </div>

            <div class="card-body p-2">

                <div class="card-header p-0 m-0 mb-2">
                    {!! Form::open(['route'=>'administracion.isrl.conceptopagos.paids','method'=>'GET','class'=>'pb-1','id'=>'form_search','role'=>'search']) !!}

                        @include('administracion.isrl.conceptopagos.form.search.paids')

                    {!! Form::close() !!}
                </div>

                @if($monto_total)
                    <div class=" bg-light pb-1 font-weight-bold d-block alert-dark">
                        <div class="float-right d-block">
                            Total General: <span>$ {{f_float($monto_total) ?? ''}}</span> ||
                            Nº <span>{{ $paids->count() ?? ''}}</span>
                        </div>
                    </div>
                @endif

                <br>

                @include('administracion.isrl.conceptopagos.table.paids')

            </div>
        </div>
    </main>

@endsection
