@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.cuentaxpagars.menus.account_bad')
                </div>
                {{-- FIN Menu rapido --}}

                <h4><span class="font-weight-bolder">Conceptos de cobros Incobrables</span> registrados</h4>

            </div>

            <div class="card-body">


                @include('administracion.configuraciones.cuentaxpagars.form.search',['route'=>'administracion.configuraciones.cuentaxpagars.account_bad'])

                <hr>

                @include('administracion.configuraciones.cuentaxpagars.table.account_bad')

            </div>
        </div>
    </main>

@endsection

@section('style')
    @parent
@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Conceptos de Cobro, listado'; </script> @endsection

